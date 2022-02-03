<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 25.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AclAllow
 * @package NetBrothers\SyncAccBundle\Entity
 * @ORM\Entity(repositoryClass="NetBrothers\SyncAccBundle\Repository\AclAllowRepository")
 */
class AclAllow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private int $idAclRole;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $controllerName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $actionName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private string $method;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private int $reasonType;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAclRole(): ?int
    {
        return $this->idAclRole;
    }

    public function setIdAclRole(int $idAclRole): self
    {
        $this->idAclRole = $idAclRole;

        return $this;
    }

    public function getControllerName(): ?string
    {
        return $this->controllerName;
    }

    public function setControllerName(string $controllerName): self
    {
        $this->controllerName = $controllerName;

        return $this;
    }

    public function getActionName(): ?string
    {
        return $this->actionName;
    }

    public function setActionName(string $actionName): self
    {
        $this->actionName = $actionName;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(?string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getReasonType(): ?int
    {
        return $this->reasonType;
    }

    public function setReasonType(?int $reasonType): self
    {
        $this->reasonType = $reasonType;

        return $this;
    }
}
