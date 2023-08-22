<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 25.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use NetBrothers\SyncAccBundle\Repository\SyncAccRepository;

/**
 * Class SyncAcc
 * @package NetBrothers\SyncAccBundle\Entity
 */
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
