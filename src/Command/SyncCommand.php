<?php

declare(strict_types=1);

/*
 * This file is part of the NetBrothers SyncAccBundle.
 *
 * (c) 2024 NetBrothers GmbH | Stefan Wessel (https://netbrothers.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NetBrothers\SyncAccBundle\Command;

use NetBrothers\SyncAccBundle\Services\ConfigService;
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

#[AsCommand(
    name: 'netbrothers:sync-acc',
    description: 'Synchronize permissions with Access Control Center in your local instance.',
)]
final class SyncCommand extends Command
{
    private const string HELP_TEXT = <<<EOF
Synchronize permissions with Access Control Center in your local instance.

Options
========
all  (default)   : get roles and acl
role             : get roles from ACC
acl              : get acl from ACC

Example:
========

`php bin/console netbrothers:sync-acc`

EOF;

    private const array ALLOWED_OPTIONS = ['role', 'acl', 'all'];

    public function __construct(
        private readonly ConfigService $configService,
        private readonly SyncService $service
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp(self::HELP_TEXT)
            ->addOption(
                'sync-table',
                null,
                InputOption::VALUE_REQUIRED,
                'Specifies what to sync: ' . implode(', ', self::ALLOWED_OPTIONS),
                'all'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!$this->configService->isEnabled()) {
            $io->error('NetBrothersSyncAccBundle is disabled. Please enable it in your configuration to run this command.');
            return Command::FAILURE;
        }

        $script = $input->getOption('sync-table');

        if (!in_array($script, self::ALLOWED_OPTIONS, true)) {
            $io->error('Invalid option for "sync-table". See help.');
            return Command::INVALID;
        }

        try {
            if (!$this->askForConfirmation($io)) {
                return Command::SUCCESS;
            }

            $this->outputComment($io, $script);
            $this->service->execute($script);

            $io->success('Sync completed');
            return Command::SUCCESS;

        } catch (\Exception | TransportExceptionInterface $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }
    }

    private function outputComment(SymfonyStyle $io, string $script): void
    {
        $comment = match ($script) {
            'all' => "Sync all tables from ACC",
            'role' => "Sync table AclRole from ACC",
            'acl' => "Sync table AclAllow from ACC",
        };
        $io->comment($comment);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    private function askForConfirmation(SymfonyStyle $io): bool
    {
        $buildName = $this->service->getBuildName();
        $question = $buildName
            ? sprintf('Sync with Build %s? (yes/no)', $buildName)
            : 'Sync now? (Build Name could not be fetched) (yes/no)';

        if ('yes' !== $io->ask($question, 'yes')) {
            $io->warning('Sync aborted by user.');
            return false;
        }
        return true;
    }
}
