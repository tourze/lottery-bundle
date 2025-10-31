<?php

declare(strict_types=1);

namespace LotteryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\PoolRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: PoolRepository::class)]
#[ORM\Table(name: 'lottery_pool', options: ['comment' => '奖池'])]
class Pool implements \Stringable, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\Column(type: Types::STRING, length: 60, unique: true, options: ['comment' => '名称'])]
    #[Assert\NotBlank(message: '奖池名称不能为空')]
    #[Assert\Length(max: 60, maxMessage: '奖池名称不能超过 {{ limit }} 个字符')]
    private ?string $title = null;

    /**
     * @var Collection<int, Prize>
     */
    #[ORM\OneToMany(targetEntity: Prize::class, mappedBy: 'pool')]
    private Collection $prizes;

    /**
     * @var Collection<int, Activity>
     */
    #[ORM\ManyToMany(targetEntity: Activity::class, mappedBy: 'pools', fetch: 'EXTRA_LAZY')]
    private Collection $activities;

    /**
     * @var Collection<int, PoolAttribute>
     */
    #[ORM\OneToMany(targetEntity: PoolAttribute::class, mappedBy: 'pool', orphanRemoval: true)]
    private Collection $poolAttributes;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[Assert\Type(type: 'bool', message: '有效状态必须是布尔值')]
    private ?bool $valid = false;

    public function __construct()
    {
        $this->prizes = new ArrayCollection();
        $this->activities = new ArrayCollection();
        $this->poolAttributes = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return $this->getTitle() ?? '';
    }

    // ID相关
    public function getId(): int
    {
        return $this->id;
    }

    // 基本信息相关
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    // 状态相关
    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return Collection<int, Prize>
     */
    public function getPrizes(): Collection
    {
        return $this->prizes;
    }

    public function addPrize(Prize $prize): void
    {
        if (!$this->prizes->contains($prize)) {
            $this->prizes->add($prize);
            $prize->setPool($this);
        }
    }

    public function removePrize(Prize $prize): void
    {
        if ($this->prizes->removeElement($prize)) {
            // set the owning side to null (unless already changed)
            if ($prize->getPool() === $this) {
                $prize->setPool(null);
            }
        }
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): void
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->addPool($this);
        }
    }

    public function removeActivity(Activity $activity): void
    {
        if ($this->activities->removeElement($activity)) {
            $activity->removePool($this);
        }
    }

    /**
     * @return Collection<int, PoolAttribute>
     */
    public function getPoolAttributes(): Collection
    {
        return $this->poolAttributes;
    }

    public function addPoolAttribute(PoolAttribute $poolAttribute): void
    {
        if (!$this->poolAttributes->contains($poolAttribute)) {
            $this->poolAttributes->add($poolAttribute);
            $poolAttribute->setPool($this);
        }
    }

    public function removePoolAttribute(PoolAttribute $poolAttribute): void
    {
        if ($this->poolAttributes->removeElement($poolAttribute)) {
            // set the owning side to null (unless already changed)
            if ($poolAttribute->getPool() === $this) {
                $poolAttribute->setPool(null);
            }
        }
    }

    // 审计信息相关 - 使用 IpTraceableAware trait

    /**
     * @return array<string, mixed>
     */
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
