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
use NetBrothers\SyncAccBundle\Entity\AclAllow;

/**
 * Class AclAllowRepository
 * @package NetBrothers\SyncAccBundle\Repository
 *
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
     * @return bool
     */
    public function isRouteAllowed($idAclRole, string $routeName, string $method = null)
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
            if (null !== $method && in_array($method, ['GET', 'POST', 'PATCH', 'DELETE', 'PUT'])) {
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