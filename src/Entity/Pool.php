<?php

namespace LotteryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\PoolRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: PoolRepository::class)]
#[ORM\Table(name: 'lottery_pool', options: ['comment' => '奖池'])]
class Pool implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::STRING, length: 60, unique: true, options: ['comment' => '名称'])]
    private ?string $title = null;

    /**
     * @var Collection<Prize>
     */
    #[ORM\OneToMany(targetEntity: Prize::class, mappedBy: 'pool')]
    private Collection $prizes;

    /**
     * @var Collection<Activity>
     */
    #[ORM\ManyToMany(targetEntity: Activity::class, mappedBy: 'pools', fetch: 'EXTRA_LAZY')]
    private Collection $activities;

    /**
     * @var Collection<PoolAttribute>
     */
    #[ORM\OneToMany(targetEntity: PoolAttribute::class, mappedBy: 'pool', orphanRemoval: true)]
    private Collection $poolAttributes;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;


    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->prizes = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->poolAttributes = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return $this->getTitle();
    }

    // ID相关
    public function getId(): ?int
    {
        return $this->id;
    }

    // 基本信息相关
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    // 状态相关
    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * @return Collection<int, Prize>
     */
    public function getPrizes(): Collection
    {
        return $this->prizes;
    }

    public function addPrize(Prize $prize): self
    {
        if (!$this->prizes->contains($prize)) {
            $this->prizes[] = $prize;
            $prize->setPool($this);
        }

        return $this;
    }

    public function removePrize(Prize $prize): self
    {
        if ($this->prizes->removeElement($prize)) {
            // set the owning side to null (unless already changed)
            if ($prize->getPool() === $this) {
                $prize->setPool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->addPool($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            $activity->removePool($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, PoolAttribute>
     */
    public function getPoolAttributes(): Collection
    {
        return $this->poolAttributes;
    }

    public function addPoolAttribute(PoolAttribute $poolAttribute): self
    {
        if (!$this->poolAttributes->contains($poolAttribute)) {
            $this->poolAttributes[] = $poolAttribute;
            $poolAttribute->setPool($this);
        }

        return $this;
    }

    public function removePoolAttribute(PoolAttribute $poolAttribute): self
    {
        if ($this->poolAttributes->removeElement($poolAttribute)) {
            // set the owning side to null (unless already changed)
            if ($poolAttribute->getPool() === $this) {
                $poolAttribute->setPool(null);
            }
        }

        return $this;
    }

    // 审计信息相关
    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'title' => $this->getTitle(),
        ];
    }
}
