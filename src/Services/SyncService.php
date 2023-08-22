<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 24.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Services;


use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use NetBrothers\SyncAccBundle\Entity\AclRole;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class SyncService
 * @package NetBrothers\SyncAccBundle\Services
 */
class SyncService
{

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var HttpClientService */
    private HttpClientService $clientService;

    /** @var TableService */
    private TableService $tableService;

    /** @var string */
    private string $requestAction = 'get-roles';


    /**
     * SyncService constructor.
     * @param EntityManagerInterface $entityManager
     * @param HttpClientService $httpClientService
     */
    public function __construct(EntityManagerInterface $entityManager, HttpClientService $httpClientService)
    {
        $this->entityManager = $entityManager;
        $this->clientService = $httpClientService;
        $this->tableService = new TableService($entityManager);
    }

    /**
     * @param string $requestAction
     * @throws \Exception|TransportExceptionInterface
     */
    public function execute(string $requestAction = 'get-roles'): void
    {
        if ($requestAction == 'all') {
            $this->requestAction = 'get-roles';
            $this->tableService->truncateTables();
        } else {
            $this->requestAction = $requestAction;
        }
        $this->tableService->setSyncAccEntity($this->requestAction);
        if ($this->requestAction === 'get-roles') {
            $this->getRoles();
        } else {
            $this->getPermissionsForRoles();
        }
        $this->tableService->updateSyncAccEntity();
        if ($requestAction == 'all') {
            $this->execute('get-acl');
        }
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    private function getRoles(): void
    {
        $response = $this->clientService->getRoles($this->tableService->getSyncAccEntity());
        if (false === $response) {
            throw new \Exception("Cannot get roles from ACC-Server");
        }
        if (is_array($response)
            && array_key_exists('error', $response)
            && array_key_exists('update', $response)) {
            if (true !== boolval($response['error']) && true !== boolval($response['update'])) {
                return;
            }
        }
        $this->tableService->setRoles($response);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    private function getPermissionsForRoles(): void
    {
        $repository = $this->entityManager->getRepository(AclRole::class);
        /** @var AclRole $role */
        foreach ($repository->findAll() as $role) {
            $response = $this->clientService->getPermissionForOneRole($this->tableService->getSyncAccEntity(), $role->getId());
            if (false === $response) {
                throw new \Exception("Cannot get permission for role " . $role->getDisplayName() . " from ACC-Server");
            }
            if (is_array($response)
                && array_key_exists('error', $response)
                && array_key_exists('update', $response)) {
                if (true !== boolval($response['error']) && true !== boolval($response['update'])) {
                    return;
                }
            }
            $this->tableService->setAuthForOneRole($role, $response);
        }
    }
}
