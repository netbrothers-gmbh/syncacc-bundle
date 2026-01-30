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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use NetBrothers\SyncAccBundle\Entity\AclAllow;
use NetBrothers\SyncAccBundle\Entity\AclRole;
use Symfony\Component\Security\Core\User\UserInterface;

final class AccService
{
    private ?UserInterface $user = null;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param string $routeName
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isRouteProtectedByAcc(string $routeName): bool
    {
        return $this->entityManager
            ->getRepository(AclAllow::class)
            ->isRouteProtectedByAcc($routeName);
    }

    /**
     * @param UserInterface|null $user
     * @param string|null $routeName
     * @param string|null $method
     * @return bool
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isAllowedByUserRoles(
        ?UserInterface $user = null,
        ?string $routeName = null,
        ?string $method = null
    ): bool
    {
        if ( null === $routeName) {
            return false;
        }
        if (true !== $this->isRouteProtectedByAcc($routeName)) {
            return true;
        }
        if (null === $user) {
            return false;
        }
        $userRoles = $user->getRoles();
        if (count($userRoles) > 0) {
            $repo = $this->entityManager->getRepository(AclAllow::class);
            return $repo->isRouteAllowed($userRoles, $routeName, $method);
        }
        return false;
    }

    /**
     * @param int $idAclRole
     * @param string|null $routeName
     * @param string|null $method
     * @return bool
     */
    public function isAllowed(int $idAclRole, ?string $routeName = null, ?string $method = null): bool
    {
        if (null === $routeName) {
            return false;
        }
        $repo = $this->entityManager->getRepository(AclAllow::class);
        return $repo->isRouteAllowed($idAclRole, $routeName, $method);
    }

    /**
     * @param UserInterface|null $user
     * @return array|ArrayCollection
     */
    public function getRolesAllowedByUser(?UserInterface $user = null)
    {
        if (empty($user) || !method_exists($user, 'getAclRole')) {
            return new ArrayCollection();
        }
        $hierarchyId = $user->getAclRole()->getHierarchyId();
        return $this->entityManager
            ->getRepository(AclRole::class)
            ->getRolesUnderHierarchy($hierarchyId);
    }
}
