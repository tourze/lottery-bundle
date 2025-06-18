<?php

namespace LotteryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\ActivityRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\ResourceManageBundle\Model\ResourceIdentity;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'lottery_activity', options: ['comment' => '抽奖活动'])]
class Activity implements \Stringable, PlainArrayInterface, AdminArrayInterface, ResourceIdentity
{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    // 基本信息
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '标题'])]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '规则文本'])]
    private ?string $textRule = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '结束时间'])]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后兑奖时间'])]
    private ?\DateTimeImmutable $lastRedeemTime = null;
    
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '头图'])]
    private ?string $headPhoto = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '找不到机会提醒文案'])]
    private ?string $noChanceText = '您已没有抽奖机会';

    // 分享相关
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '分享路径'])]
    private ?string $sharePath = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '分享标题'])]
    private ?string $shareTitle = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '分享图片'])]
    private ?string $sharePicture = null;

    // 关联实体
    /**
     * @var Collection<Chance>
     */
    #[Ignore]
    #[ORM\OneToMany(targetEntity: Chance::class, mappedBy: 'activity')]
    private Collection $chances;

    /**
     * @var Collection<Pool>
     */
    #[ORM\ManyToMany(targetEntity: Pool::class, inversedBy: 'activities', fetch: 'EXTRA_LAZY')]
    private Collection $pools;

    /**
     * @var Collection<ActivityAttribute>
     */
    #[ORM\OneToMany(targetEntity: ActivityAttribute::class, mappedBy: 'activity', orphanRemoval: true)]
    private Collection $attributes;

    // 状态信息
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    // 审计信息
    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->chances = new ArrayCollection();
        $this->pools = new ArrayCollection();
        $this->attributes = new ArrayCollection();
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
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTextRule(): ?string
    {
        return $this->textRule;
    }

    public function setTextRule(?string $textRule): self
    {
        $this->textRule = $textRule;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getLastRedeemTime(): ?\DateTimeImmutable
    {
        return $this->lastRedeemTime;
    }

    public function setLastRedeemTime(?\DateTimeImmutable $lastRedeemTime): void
    {
        $this->lastRedeemTime = $lastRedeemTime;
    }

    public function getHeadPhoto(): ?string
    {
        return $this->headPhoto;
    }

    public function setHeadPhoto(?string $headPhoto): self
    {
        $this->headPhoto = $headPhoto;

        return $this;
    }

    public function getNoChanceText(): ?string
    {
        return $this->noChanceText;
    }

    public function setNoChanceText(?string $noChanceText): static
    {
        $this->noChanceText = $noChanceText;

        return $this;
    }

    // 分享相关
    public function getShareTitle(): ?string
    {
        return $this->shareTitle;
    }

    public function setShareTitle(?string $shareTitle): void
    {
        $this->shareTitle = $shareTitle;
    }

    public function getSharePicture(): ?string
    {
        return $this->sharePicture;
    }

    public function setSharePicture(?string $sharePicture): void
    {
        $this->sharePicture = $sharePicture;
    }

    public function getSharePath(): ?string
    {
        return $this->sharePath;
    }

    public function setSharePath(?string $sharePath): void
    {
        $this->sharePath = $sharePath;
    }

    // 关联实体相关
    /**
     * @return Collection<int, Chance>
     */
    public function getChances(): Collection
    {
        return $this->chances;
    }

    public function addChance(Chance $chance): self
    {
        if (!$this->chances->contains($chance)) {
            $this->chances[] = $chance;
            $chance->setActivity($this);
        }

        return $this;
    }

    public function removeChance(Chance $chance): self
    {
        if ($this->chances->removeElement($chance)) {
            // set the owning side to null (unless already changed)
            if ($chance === $this) {
                $chance->setActivity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pool>
     */
    public function getPools(): Collection
    {
        return $this->pools;
    }

    public function addPool(Pool $pool): self
    {
        if (!$this->pools->contains($pool)) {
            $this->pools[] = $pool;
            $pool->addActivity($this);
        }

        return $this;
    }

    public function removePool(Pool $pool): self
    {
        if ($this->pools->removeElement($pool)) {
            $pool->removeActivity($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, ActivityAttribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addActivityAttribute(ActivityAttribute $activityAttribute): self
    {
        if (!$this->attributes->contains($activityAttribute)) {
            $this->attributes[] = $activityAttribute;
            $activityAttribute->setActivity($this);
        }

        return $this;
    }

    public function removeActivityAttribute(ActivityAttribute $activityAttribute): self
    {
        if ($this->attributes->removeElement($activityAttribute)) {
            // set the owning side to null (unless already changed)
            if ($activityAttribute->getActivity() === $this) {
                $activityAttribute->setActivity(null);
            }
        }

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

    // 审计信息相关
    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'headPhoto' => $this->getHeadPhoto(),
            'textRule' => $this->getTextRule(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'lastRedeemTime' => $this->getLastRedeemTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'headPhoto' => $this->getHeadPhoto(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
        ];
    }

    public function getResourceId(): string
    {
        return $this->getId() . '';
    }

    public function getResourceLabel(): string
    {
        return $this->getTitle();
    }
}
