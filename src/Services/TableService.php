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

namespace NetBrothers\SyncAccBundle\Services;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use NetBrothers\SyncAccBundle\Entity\AclAllow;
use NetBrothers\SyncAccBundle\Entity\AclRole;
use NetBrothers\SyncAccBundle\Entity\SyncAcc;
use NetBrothers\SyncAccBundle\Repository\SyncAccRepository;

final class TableService
{
    private ?SyncAcc $syncAccEntity = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getSyncAccEntity(): ?SyncAcc
    {
        return $this->syncAccEntity;
    }

    /**
     * @throws \Exception
     */
    public function truncateTables(): void
    {
        $con = $this->entityManager->getConnection();
        $con->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $platform  = $con->getDatabasePlatform();
        $tables = ['sync_acc', 'acl_role', 'acl_allow'];
        foreach ($tables as $table) {
            $con->executeStatement($platform->getTruncateTableSQL($table));
        }
        $con->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * @param string $requestAction
     * @throws \Exception
     */
    public function setSyncAccEntity(string $requestAction = 'get-roles'): void
    {
        if (!in_array($requestAction, ['get-roles', 'get-acl'])) {
            throw new \Exception(sprintf('Requested action %s is not defined', $requestAction));
        }
        /** @var SyncAccRepository $repository */
        $repository = $this->entityManager->getRepository(SyncAcc::class);
        $this->syncAccEntity = $repository->findOneByActionName($requestAction);
        if (is_null($this->syncAccEntity)) {
            $this->syncAccEntity = new SyncAcc();
            $lastCall = new \DateTime('2000-01-01 00:00:00');
            $this->syncAccEntity->setActionName($requestAction)->setLastCall($lastCall);
        }
    }

    /**
     * @throws \Exception
     */
    public function updateSyncAccEntity(): void
    {
        $dateTime = new \DateTime("now");
        $this->syncAccEntity->setLastCall($dateTime);
        $this->entityManager->persist($this->syncAccEntity);
        $this->entityManager->flush();
    }

    /**
     * @param $response
     * @throws Exception
     */
    public function setRoles($response): void
    {
        $con = $this->entityManager->getConnection();
        $platform = $con->getDatabasePlatform();
        $con->executeQuery('SET FOREIGN_KEY_CHECKS = 0;');
        $con->executeStatement($platform->getTruncateTableSQL('acl_role'));
        $con->executeQuery('SET FOREIGN_KEY_CHECKS = 1;');
        if (is_array($response) ) {
            if (array_key_exists('roles', $response)) {
                foreach ($response['roles'] as $roleArray) {
                    $this->addOneRole($roleArray);
                }
            } else {
                throw new \Exception("Could not find key roles");
            }
            $this->entityManager->flush();
        } elseif (is_object($response)) {
            foreach ($response->roles as $role) {
                $roleArray = (array)$role;
                $this->addOneRole($roleArray);
            }
            $this->entityManager->flush();
        } else {
            throw new \Exception("Could not read response.");
        }
    }

    private function addOneRole(array $role): void
    {
        $newRole = new AclRole();
        foreach ($role as $k => $p) {
            if ($k === "Id" || $k === 'id') {
                continue;
            } elseif ($k === 'IdRole' || $k === 'idRole') {
                $newRole->setId($p);
            } else {
                $method = 'set' . $k;
                if (method_exists(AclRole::class, $method)) {
                    $newRole->{$method}($p);
                }
            }
        }
        $this->entityManager->persist($newRole);
    }

    /**
     * @param AclRole $aclRole
     * @param $response
     * @throws \Exception
     */
    public function setAuthForOneRole(AclRole $aclRole, $response): void
    {
        $repository = $this->entityManager->getRepository(AclAllow::class);
        $oldAuths = $repository->findBy(['idAclRole' => $aclRole->getId()]);
        if (count($oldAuths) > 0) {
            foreach ($oldAuths as $oldAuth) {
                $this->entityManager->remove($oldAuth);
            }
            $this->entityManager->flush();
        }
        if (is_array($response) ) {
            if (array_key_exists('resources', $response)) {
                foreach ($response['resources'] as $aclArray) {
                    $this->addOneAcl($aclRole, $aclArray);
                }
            } else {
                throw new \Exception("Could not find key roles");
            }
            $this->entityManager->flush();
        } elseif (is_object($response)) {
            foreach ($response->resources as $action) {
                $aclArray = (array)$action;
                $this->addOneAcl($aclRole, $aclArray);
            }
            $this->entityManager->flush();
        } else {
            throw new \Exception("Could not read response.");
        }
    }

    private function addOneAcl(AclRole $aclRole, array $action): void
    {
        $newAcl = new AclAllow();
        $newAcl
            ->setIdAclRole($aclRole->getId())
            ->setControllerName($action['routeName'])
            ->setActionName($action['routeName'])
            ->setMethod($action['method'])
            ->setReasonType($action['needsReason'])
        ;
        $this->entityManager->persist($newAcl);
    }
}
