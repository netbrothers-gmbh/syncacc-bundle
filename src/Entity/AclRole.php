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
 * Class AclRole
 * @package NetBrothers\SyncAccBundle\Entity
 * @ORM\Entity(repositoryClass="NetBrothers\SyncAccBundle\Repository\AclRoleRepository")
 */
class AclRole
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $displayName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $beschreibung;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"default":0})
     */
    private $hierarchyId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $defaultRoute;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $isHidden;

    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getBeschreibung(): ?string
    {
        return $this->beschreibung;
    }

    public function setBeschreibung(?string $beschreibung): self
    {
        $this->beschreibung = $beschreibung;

        return $this;
    }

    public function getHierarchyId(): ?int
    {
        return $this->hierarchyId;
    }

    public function setHierarchyId(?int $hierarchyId): self
    {
        $this->hierarchyId = $hierarchyId;

        return $this;
    }

    public function getDefaultRoute(): ?string
    {
        return $this->defaultRoute;
    }

    public function setDefaultRoute(?string $defaultRoute): self
    {
        $this->defaultRoute = $defaultRoute;

        return $this;
    }

    public function getIsHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * @return array
     */
    public function getViewDataArray(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "displayName" => $this->displayName,
            "beschreibung" => $this->beschreibung,
            "hierarchyId" => $this->hierarchyId,
            "defaultRoute" => $this->defaultRoute,
            "isHidden"    => ($this->getIsHidden()) ? true : false,
        ];
    }
}