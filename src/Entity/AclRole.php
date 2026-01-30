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

namespace NetBrothers\SyncAccBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use NetBrothers\SyncAccBundle\Dto\AclRoleDto;
use NetBrothers\SyncAccBundle\Repository\AclRoleRepository;

#[ORM\Entity(repositoryClass: AclRoleRepository::class)]
class AclRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $displayName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $beschreibung = null;

    #[ORM\Column(nullable: true, options: ['default' => 0])]
    private int $hierarchyId = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $defaultRoute = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isHidden = false;

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
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

    public function getIsHidden(): bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    public function getDto(): AclRoleDto
    {
        return new AclRoleDto(
            $this->getId(),
            $this->getName(),
            $this->getDisplayName() ?? '',
            $this->getBeschreibung() ?? '',
            $this->getHierarchyId(),
            $this->getDefaultRoute() ?? '',
            $this->getIsHidden(),
        );
    }

    /**
     * @deprecated Use `getDto()` instead.
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
            "isHidden"    => $this->getIsHidden(),
        ];
    }
}
