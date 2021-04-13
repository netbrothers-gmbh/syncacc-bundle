<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 25.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use NetBrothers\SyncAccBundle\Entity\AclRole;

/**
 * Class AclRoleRepository
 * @package NetBrothers\SyncAccBundle\Repository
 *
 * @method AclRole|null find($id, $lockMode = null, $lockVersion = null)
 * @method AclRole|null findOneBy(array $criteria, array $orderBy = null)
 * @method AclRole[]    findAll()
 * @method AclRole[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AclRoleRepository extends EntityRepository
{

    /**
     * @param int $hierarchyId
     * @return mixed
     */
    public function getRolesUnderHierarchy(int $hierarchyId = 0)
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