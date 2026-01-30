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
use NetBrothers\SyncAccBundle\Entity\AclRole;

/**
 * @method AclRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AclRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AclRole[]    findAll()
 * @method AclRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AclRoleRepository extends EntityRepository
{
    /**
     * @param int $hierarchyId
     * @return array
     */
    public function getRolesUnderHierarchy(int $hierarchyId = 0): array
    {
        $qb = $this->createQueryBuilder('acl_role')
            ->andWhere('acl_role.isHidden = :isHidden')
            ->setParameter('isHidden', false);
        if ($hierarchyId < 10000) {
            $qb->andWhere('acl_role.hierarchyId < :hierarchyId')
                ->setParameter('hierarchyId', $hierarchyId);
        }
        return $qb
            ->addOrderBy('acl_role.hierarchyId', 'DESC')
            ->addOrderBy('acl_role.beschreibung', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
