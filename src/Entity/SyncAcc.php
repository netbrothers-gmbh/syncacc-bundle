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
use NetBrothers\SyncAccBundle\Repository\SyncAccRepository;

#[ORM\Entity(repositoryClass: SyncAccRepository::class)]
class SyncAcc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $actionName;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastCall = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getLastCall(): ?\DateTimeInterface
    {
        return $this->lastCall;
    }

    public function setLastCall(?\DateTimeInterface $lastCall = null): self
    {
        $this->lastCall = $lastCall;
        return $this;
    }
}
