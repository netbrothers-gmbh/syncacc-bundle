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

namespace NetBrothers\SyncAccBundle\Repository;

use Doctrine\ORM\EntityRepository;
use NetBrothers\SyncAccBundle\Entity\AclAllow;

/**
 * @method AclAllow|null find($id, $lockMode = null, $lockVersion = null)
 * @method AclAllow|null findOneBy(array $criteria, array $orderBy = null)
 * @method AclAllow[]    findAll()
 * @method AclAllow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AclAllowRepository extends EntityRepository
{
    /**
     * @param string $routeName
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isRouteProtectedByAcc(string $routeName): bool
    {
        $qb = $this->createQueryBuilder('aclAllow');
        $qb->select(
            $qb->expr()->count('aclAllow')
        )
            ->andWhere('aclAllow.actionName = :actionName')
            ->setParameter('actionName', $routeName)
        ;
        $query = $qb->getQuery();
        $count =  $query->getSingleScalarResult();
        return ($count > 0);
    }

    /**
     * @param array|int $idAclRole
     * @param string $routeName
     * @param string|null $method
     * @return bool
     */
    public function isRouteAllowed(array|int $idAclRole, string $routeName, ?string $method = null): bool
    {
        try {
            $qb = $this->createQueryBuilder('aclAllow');
            $qb->select($qb->expr()->count('aclAllow'))
                ->andWhere('aclAllow.actionName = :actionName')
                ->setParameter('actionName', $routeName)
            ;
            if (is_array($idAclRole)) {
                $qb->andWhere(
                    $qb->expr()->in('aclAllow.idAclRole', $idAclRole)
                );
            } else {
                $qb->andWhere('aclAllow.idAclRole = :idAclRole')
                    ->setParameter('idAclRole', $idAclRole)
                ;
            }
            if (in_array($method, ['GET', 'POST', 'PATCH', 'DELETE', 'PUT'])) {
                $qb->andWhere('aclAllow.method = :method')->setParameter('method', $method);
            }
            $query = $qb->getQuery();
            $count =  $query->getSingleScalarResult();
            return ($count > 0);
        } catch (\Exception $exception) {
            return false;
        }
    }
}
