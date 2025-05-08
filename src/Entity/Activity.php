<?php

namespace LotteryBundle\Entity;

use AntdCpBundle\Builder\Field\BraftEditor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\ActivityRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\CurdAction;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Field\RichTextField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\ResourceManageBundle\Model\ResourceIdentity;

#[AsPermission(title: '抽奖活动')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'lottery_activity', options: ['comment' => '抽奖活动'])]
class Activity implements \Stringable, PlainArrayInterface, AdminArrayInterface, ResourceIdentity
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    #[FormField(title: '分享路径')]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '分享路径'])]
    private ?string $sharePath = null;

    #[FormField(title: '分享标题')]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '分享标题'])]
    private ?string $shareTitle = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[FormField(title: '分享图片')]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '分享图片'])]
    private ?string $sharePicture = null;

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

    public function retrieveWechatShareFriendConfig(): array
    {
        return [
            'shareTitle' => $this->getShareTitle(),
            'sharePicture' => $this->getSharePicture(),
            'sharePath' => $this->getSharePath(),
        ];
    }

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[FormField]
    #[Keyword]
    #[Groups(['restful_read'])]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '标题'])]
    private string $title = '';

    /**
     * @BraftEditor()
     */
    #[RichTextField]
    #[FormField]
    #[Keyword]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '规则文本'])]
    private ?string $textRule = null;

    /**
     * @var Collection<Chance>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: Chance::class)]
    private Collection $chances;

    /**
     * @var Collection<Pool>
     */
    #[FormField(title: '奖池')]
    #[ListColumn(title: '奖池')]
    #[ORM\ManyToMany(targetEntity: Pool::class, inversedBy: 'activities', fetch: 'EXTRA_LAZY')]
    private Collection $pools;

    /**
     * @var Collection<ActivityAttribute>
     */
    #[ListColumn(title: '属性')]
    #[CurdAction(label: '属性', drawerWidth: 1000)]
    #[ORM\OneToMany(mappedBy: 'activity', targetEntity: ActivityAttribute::class, orphanRemoval: true)]
    private Collection $attributes;

    #[Filterable]
    #[FormField(span: 9)]
    #[Groups(['restful_read'])]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[Filterable]
    #[FormField(span: 9)]
    #[Groups(['restful_read'])]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    #[FormField(span: 9)]
    #[Groups(['restful_read'])]
    #[ListColumn(sorter: true)]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '最后兑奖时间'])]
    private ?\DateTimeInterface $lastRedeemTime = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[Groups(['admin_curd', 'restful_read'])]
    #[FormField(title: '头图')]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '头图'])]
    private ?string $headPhoto = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '找不到机会提醒文案'])]
    private ?string $noChanceText = '您已没有抽奖机会';

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
    }

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
        }

        return $this;
    }

    public function removePool(Pool $pool): self
    {
        $this->pools->removeElement($pool);

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getLastRedeemTime(): ?\DateTimeInterface
    {
        return $this->lastRedeemTime;
    }

    public function setLastRedeemTime(?\DateTimeInterface $lastRedeemTime): void
    {
        $this->lastRedeemTime = $lastRedeemTime;
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
            if ($activityAttribute === $this) {
                $activityAttribute->setActivity(null);
            }
        }

        return $this;
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

    public function retrievePlainArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'headPhoto' => $this->getHeadPhoto(),
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'textRule' => $this->getTextRule(),
            'shareTitle' => $this->getShareTitle(),
            'sharePicture' => $this->getSharePicture(),
        ];
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

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'shareTitle' => $this->getShareTitle(),
            'sharePicture' => $this->getSharePicture(),
            'title' => $this->getTitle(),
            'textRule' => $this->getTextRule(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'lastRedeemTime' => $this->getLastRedeemTime()?->format('Y-m-d H:i:s'),
            'headPhoto' => $this->getHeadPhoto(),
        ];
    }

    public function getResourceId(): string
    {
        return $this->getId();
    }

    public function getResourceLabel(): string
    {
        return $this->getTitle();
    }

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
}
