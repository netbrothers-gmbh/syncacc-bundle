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
use Doctrine\ORM\NonUniqueResultException;
use NetBrothers\SyncAccBundle\Entity\SyncAcc;

/**
 * @method SyncAcc|null find($id, $lockMode = null, $lockVersion = null)
 * @method SyncAcc|null findOneBy(array $criteria, array $orderBy = null)
 * @method SyncAcc[]    findAll()
 * @method SyncAcc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SyncAccRepository extends EntityRepository
{
    /**
     * @param string $requestAction
     * @return SyncAcc|null
     * @throws NonUniqueResultException
     */
    public function findOneByActionName(string $requestAction = 'get-roles'): ?SyncAcc
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.actionName = :val')
            ->setParameter('val', $requestAction)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
