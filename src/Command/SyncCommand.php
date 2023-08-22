<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 24.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use NetBrothers\SyncAccBundle\Services\HttpClientService;
use NetBrothers\SyncAccBundle\Services\SyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class SyncCommand
 * @package NetBrothers\SyncAccBundle\Command
 */
#[AsCommand(
    name: 'netbrothers:sync-acc',
    description: 'Synchronize permissions with Access Control Center in your local instance.',
)]
class SyncCommand extends Command
{
    protected static $defaultName = 'netbrothers:sync-acc';

    const HELP_TEXT=<<<EOF
Synchronize permissions with Access Control Center in your local instance.

Options
========
all  (default)   : get roles and acl  
role             : get roles from ACC
acl              : get acl from ACC

Example:
========

`php bin/console netbrothers:acc`


EOF;

    /** @var SyncService */
    private SyncService $service;

    /** @var array benötigte Haupt-Schlüssel in der Konfiguration */
    private array $configKeys = array( 'acc_enable', 'acc_server', 'acc_software_token', 'acc_server_token');

    /** @var array benötigte Haupt-Schlüssel in der Konfiguration */
    private array $configAuthKeys = array('acc_basic_auth_user', 'acc_basic_auth_password');

    /** @var array Konfiguration für den HttpClient */
    private array $clientConfig = [
        'auth_basic' => [],
        'headers' => ['Content-Type' => 'application/json'],
    ];

    /** @var string[]  */
    private array $allowedOptions = ['role', 'acl', 'all'];

    /** @var array */
    private array $config = [];

    /** @var HttpClientService */
    private HttpClientService $httpService;

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setHelp(self::HELP_TEXT)
            ->addOption(
                'sync-table',
                '',
                InputOption::VALUE_OPTIONAL,
                '[role|acl|all], role for updating table AclRole, acl for updating table AclAllow, all for all :)'
            )
        ;
    }

    /**
     * SyncAccCommand constructor.
     * @param EntityManagerInterface $entityManager
     * @param array $config
     * @param string|null $name
     * @throws \Exception
     */
    public function __construct(EntityManagerInterface $entityManager, array $config = [], string $name = null)
    {
        parent::__construct($name);
        $this->setConfig($config);
        $this->httpService = new HttpClientService($this->config, $this->clientConfig);
        $this->service = new SyncService($entityManager, $this->httpService);

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output ): int
    {
        $io = new SymfonyStyle($input, $output);
        $script = $input->getOption('sync-table');
        if (null === $script || false === $script) {
            $script = 'all';
        }
        if (!in_array($script, $this->allowedOptions)) {
            $io->error('You have to pass an invalid option. See help');
            return 1;
        }
        if (true !== $this->askForBuildName($io)) {
            return 0;
        }
        if ($script == 'all') {
            $requestAction = 'all';
            $io->comment("Sync all tables from ACC");
        } elseif ($script == 'role') {
            $requestAction = 'get-roles';
            $io->comment("Sync table AclRole from ACC");
        } else {
            $requestAction = 'get-acl';
            $io->comment("Sync table AclAllow from ACC");
        }
        try {
            $this->service->execute($requestAction);
            $io->success('Sync completed');
            return 0;
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    private function setConfig(array $config = []): void
    {
        if (empty($config['acc_enable']) ||  false === boolval($config['acc_enable'])) {
            throw new \Exception("Sync Acc is disabled or not configured!");
        }

        foreach ($this->configKeys as $key) {
            if (!array_key_exists($key, $config)) {
                $message = "Config $key missing!";
                throw new \Exception($message);
            }
            $value = $config[$key];
            if (empty($value)) {
                $message = "Value missing for $key!";
                throw new \Exception($message);
            }
            $this->config[$key] = $value;
        }

        if (!empty($config['acc_use_basic_auth']) && true === boolval($config['acc_use_basic_auth'])) {
            $this->config['acc_use_basic_auth'] = true;
            $authConfig = [];
            foreach ($this->configAuthKeys as $key) {
                $value = $config[$key];
                if (empty($value)) {
                    $message = "You activated AUTH_BASIC, but not defined $key!";
                    throw new \Exception($message);
                }
                $authConfig[$key] = $value;
            }
            $this->clientConfig['auth_basic'] = [
                $authConfig['acc_basic_auth_user'], $authConfig['acc_basic_auth_password']
            ];
        } else {
            unset($this->clientConfig['auth_basic']);
            $this->config['acc_use_basic_auth'] = false;
        }
    }

    /**
     * @param SymfonyStyle $io
     * @return bool
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function askForBuildName(SymfonyStyle $io): bool
    {
        $buildName = $this->httpService->getBuildName();
        $question = (null !== $buildName && is_string($buildName)) ?
            $question = 'Sync with Build ' . $buildName . '? (yes/no)' :
            $question = 'Sync now? (Build Name could not be fetched) (yes/no)'
        ;
        $response = $io->ask($question, 'yes', );
        if ($response !== 'yes') {
            $io->error('Sync Aborted');
            return false;
        }
        return true;
    }
}
