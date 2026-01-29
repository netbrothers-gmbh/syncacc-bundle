<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 24.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Services;


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
    private string $requestAction = 'get-roles';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly HttpClientService $clientService,
        private readonly TableService $tableService
    ) {
    }

    /**
     * @param string $requestAction
     * @throws \Exception|TransportExceptionInterface
     */
    public function execute(string $requestAction = 'get-roles'): void
    {
        if ($requestAction === 'all') {
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

        if ($requestAction === 'all') {
            $this->execute('get-acl');
        }
    }

    /**
     * @return string|null
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getBuildName(): ?string
    {
        return $this->clientService->getBuildName();
    }

    /**
     * @throws Exception
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function getRoles(): void
    {
        $response = $this->clientService->getRoles($this->tableService->getSyncAccEntity());
        if (null === $response) {
            throw new \RuntimeException("Cannot get roles from ACC-Server or response was empty.");
        }
        if ($this->isUpdateSkipped($response)) {
            return;
        }
        $this->tableService->setRoles($response);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    private function getPermissionsForRoles(): void
    {
        $repository = $this->entityManager->getRepository(AclRole::class);
        /** @var AclRole $role */
        foreach ($repository->findAll() as $role) {
            $response = $this->clientService->getPermissionForOneRole($this->tableService->getSyncAccEntity(), $role->getId());
            if (null === $response) {
                // Log or handle the error for a specific role, but don't stop the whole process
                continue;
            }
            if ($this->isUpdateSkipped($response)) {
                continue;
            }
            $this->tableService->setAuthForOneRole($role, $response);
        }
    }

    private function isUpdateSkipped(array $response): bool
    {
        return isset($response['error'], $response['update']) &&
            !$response['error'] &&
            !$response['update'];
    }
}
