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

use Doctrine\ORM\Mapping as ORM;
use NetBrothers\SyncAccBundle\Repository\AclAllowRepository;

#[ORM\Entity(repositoryClass: AclAllowRepository::class)]
class AclAllow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $idAclRole;

    #[ORM\Column(length: 255)]
    private string $controllerName;

    #[ORM\Column(length: 255)]
    private string $actionName;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $method;

    #[ORM\Column(nullable: true)]
    private ?int $reasonType = null;

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
