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
use NetBrothers\SyncAccBundle\Entity\SyncAcc;
/**
 * Class SyncAccRepository
 * @package NetBrothers\SyncAccBundle\Repository
 *
 * @method SyncAcc|null find($id, $lockMode = null, $lockVersion = null)
 * @method SyncAcc|null findOneBy(array $criteria, array $orderBy = null)
 * @method SyncAcc[]    findAll()
 * @method SyncAcc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SyncAccRepository extends EntityRepository
{

    /**
     * @param string $requestAction
     * @return mixed
     * @throws NonUniqueResultException
     */
    public function findOneByActionName($requestAction = 'get-roles')
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.actionName = :val')
            ->setParameter('val', $requestAction)
            ->getQuery()
            ->getOneOrNullResult();
    }
}