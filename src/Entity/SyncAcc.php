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
 * Class SyncAcc
 * @package NetBrothers\SyncAccBundle\Entity
 * @ORM\Entity(repositoryClass="NetBrothers\SyncAccBundle\Repository\SyncAccRepository")
 */
class SyncAcc
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $actionName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastCall;

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

    public function setLastCall(\DateTimeInterface $lastCall): self
    {
        $this->lastCall = $lastCall;

        return $this;
    }
}