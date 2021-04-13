<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 25.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Services;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use http\Client\Curl\User;
use NetBrothers\SyncAccBundle\Entity\AclAllow;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class AccService
 * @package NetBrothers\SyncAccBundle\Services
 */
class AccService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var UserInterface */
    private $user = null;

    /** @param UserInterface $user */
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * AccService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @param string|null $routeName
     * @param string|null $method
     * @return bool
     */
    public function isAllowedByUserRoles(UserInterface $user, string $routeName = null, string $method = null): bool
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
        if (0 < count($userRoles)) {
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
    public function isAllowed(int $idAclRole, string $routeName = null, string $method = null): bool
    {
        if (null === $routeName) {
            return false;
        }
        $repo = $this->entityManager->getRepository(AclAllow::class);
        return $repo->isRouteAllowed($idAclRole, $routeName, $method);
    }

    /**
     * @param UserInterface|null $user
     * @return array|ArrayCollection|int|string
     */
    public function getRolesAllowedByUser(UserInterface $user = null)
    {
        if (null === $user || '' === $user) {
            return new ArrayCollection();
        }
        $hierarchyId = $user->getAclRole()->getHierarchyId();
        return $this->entityManager
            ->getRepository(AclRole::class)
            ->getRolesUnderHierarchy($hierarchyId);
    }
}