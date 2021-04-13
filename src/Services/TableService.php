<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 25.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Services;

use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use NetBrothers\SyncAccBundle\Entity\AclAllow;
use NetBrothers\SyncAccBundle\Entity\AclRole;
use NetBrothers\SyncAccBundle\Entity\SyncAcc;

/**
 * Class TableService
 * @package NetBrothers\SyncAccBundle\Services
 */
class TableService
{

    /** @var SyncAcc */
    private $syncAccEntity = null;

    /**
     * @return SyncAcc
     */
    public function getSyncAccEntity(): ?SyncAcc
    {
        return $this->syncAccEntity;
    }

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TableService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \Exception
     */
    public function truncateTables()
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
     */
    public function setSyncAccEntity($requestAction = 'get-roles')
    {
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
    public function updateSyncAccEntity()
    {
        $dateTime = new \DateTime("now");
        $this->syncAccEntity->setLastCall($dateTime);
        $this->entityManager->persist($this->syncAccEntity);
        $this->entityManager->flush();
    }

    /**
     * @param $response
     * @throws ConnectionException
     * @throws Exception
     */
    public function setRoles($response)
    {
        $con = $this->entityManager->getConnection();
        $con->beginTransaction();
        try {
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
                $con->commit();

            } elseif (is_object($response)) {
                foreach ($response->roles as $role) {
                    $roleArray = (array)$role;
                    $this->addOneRole($roleArray);
                }
                $this->entityManager->flush();
                $con->commit();
            } else {
                throw new \Exception("Could not read response.");
            }
        } catch (\Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * @param array $role
     */
    private function addOneRole(array $role)
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
     */
    public function setAuthForOneRole(AclRole $aclRole, $response)
    {
        $repository = $this->entityManager->getRepository(AclAllow::class);
        $oldAuths = $repository->findBy(['idAclRole' => $aclRole->getId()]);
        if (0 < count($oldAuths)) {
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

    /**
     * @param array $aclRole
     */
    private function addOneAcl(AclRole $aclRole, array $action)
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