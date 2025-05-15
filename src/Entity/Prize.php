<?php

namespace LotteryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\PrizeRepository;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrinePrecisionBundle\Attribute\PrecisionColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EnumExtra\Itemable;
use Tourze\LockServiceBundle\Model\LockEntity;

#[ORM\Entity(repositoryClass: PrizeRepository::class)]
#[ORM\Table(name: 'lottery_prize', options: ['comment' => '奖品信息'])]
class Prize implements \Stringable, Itemable, PlainArrayInterface, AdminArrayInterface, LockEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::STRING, length: 60, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '奖品描述'])]
    private ?string $content = null;

    // 奖品类型信息
    #[ORM\Column(type: Types::STRING, length: 60, options: ['comment' => '类型'])]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '类型值ID'])]
    private ?string $typeId = null;

    // 奖品数量与概率
    #[ORM\Column(nullable: true, options: ['comment' => '单次派发数量', 'default' => 1])]
    private ?int $amount = 1;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '总数量'])]
    private ?int $quantity = 0;

    #[ORM\Column(nullable: true, options: ['default' => '0', 'comment' => '每日数量'])]
    private ?int $dayLimit = 0;

    /**
     * 也可以简单理解为：第N次必中？
     */
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '概率数'])]
    private ?int $probability = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '概率表达式'])]
    private ?string $probabilityExpression = null;

    /**
     * 记录奖品的成本，有些特殊的抽奖也可能用来作为概率的参数.
     */
    #[PrecisionColumn]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true, options: ['default' => '0.00', 'comment' => '奖品价值'])]
    private ?string $value = null;

    #[ORM\Column(nullable: true, options: ['comment' => '派发后有效天数'])]
    private ?float $expireDay = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '派发后到期时间'])]
    private ?\DateTimeInterface $expireTime = null;

    #[ORM\Column(length: 512, nullable: true, options: ['comment' => '主图'])]
    private ?string $picture = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '选中图片'])]
    private ?string $secondPicture = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '中奖图片'])]
    private ?string $pickPicture = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '地址图片'])]
    private ?string $consigneePicture = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否参与轮播', 'default' => true])]
    private ?bool $canShow = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否在奖品列表展示', 'default' => true])]
    private ?bool $canShowPrize = true;

    /**
     * 如果一个人啥都没中，那么就会必中兜底奖项.
     */
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '兜底奖项'])]
    private ?bool $isDefault = false;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '登记收货地址'])]
    private ?bool $needConsignee = false;

    #[ORM\Column(nullable: true, options: ['comment' => '需要审核', 'default' => false])]
    private ?bool $needReview = false;

    #[ORM\ManyToOne(targetEntity: Pool::class, inversedBy: 'prizes')]
    private ?Pool $pool = null;

    /**
     * @var Collection<Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'prize', orphanRemoval: true)]
    private Collection $stocks;

    /**
     * order值大的排序靠前。有效的值范围是[0, 2^32].
     */
    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    private ?bool $valid = false;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[IndexColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    public function __toString()
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getPool()->getTitle()}-{$this->getName()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    // 基本信息相关
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTypeId(): ?string
    {
        return $this->typeId;
    }

    public function setTypeId(?string $typeId): self
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDayLimit(): ?int
    {
        return $this->dayLimit;
    }

    public function setDayLimit(?int $dayLimit): static
    {
        $this->dayLimit = $dayLimit;

        return $this;
    }

    public function getProbability(): ?int
    {
        return $this->probability;
    }

    public function setProbability(int $probability): self
    {
        $this->probability = $probability;

        return $this;
    }

    public function getProbabilityExpression(): ?string
    {
        return $this->probabilityExpression;
    }

    public function setProbabilityExpression(?string $probabilityExpression): self
    {
        $this->probabilityExpression = $probabilityExpression;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    // 有效期相关
    public function getExpireDay(): ?float
    {
        return $this->expireDay;
    }

    public function setExpireDay(?float $expireDay): static
    {
        $this->expireDay = $expireDay;

        return $this;
    }

    public function getExpireTime(): ?\DateTimeInterface
    {
        return $this->expireTime;
    }

    public function setExpireTime(?\DateTimeInterface $expireTime): static
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    // 显示相关
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getSecondPicture(): ?string
    {
        return $this->secondPicture;
    }

    public function setSecondPicture(?string $secondPicture): self
    {
        $this->secondPicture = $secondPicture;

        return $this;
    }

    public function getPickPicture(): ?string
    {
        return $this->pickPicture;
    }

    public function setPickPicture(?string $pickPicture): void
    {
        $this->pickPicture = $pickPicture;
    }

    public function getConsigneePicture(): ?string
    {
        return $this->consigneePicture;
    }

    public function setConsigneePicture(?string $consigneePicture): void
    {
        $this->consigneePicture = $consigneePicture;
    }

    public function isCanShow(): ?bool
    {
        return $this->canShow;
    }

    public function setCanShow(bool $canShow): static
    {
        $this->canShow = $canShow;

        return $this;
    }
    
    public function getCanShowPrize(): ?bool
    {
        return $this->canShowPrize;
    }

    public function setCanShowPrize(?bool $canShowPrize): void
    {
        $this->canShowPrize = $canShowPrize;
    }

    // 配置相关
    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): self
    {
        $this->isDefault = $isDefault;

        return $this;
    }
    
    public function getNeedConsignee(): ?bool
    {
        return $this->needConsignee;
    }

    public function setNeedConsignee(?bool $needConsignee): self
    {
        $this->needConsignee = $needConsignee;

        return $this;
    }

    public function isNeedReview(): ?bool
    {
        return $this->needReview;
    }

    public function setNeedReview(?bool $needReview): static
    {
        $this->needReview = $needReview;

        return $this;
    }

    // 关联实体相关
    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(?Pool $pool): self
    {
        $this->pool = $pool;

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setPrize($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getPrize() === $this) {
                $stock->setPrize(null);
            }
        }

        return $this;
    }

    // 排序相关
    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

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

    // 接口实现方法
    public function toSelectItem(): array
    {
        return [
            'value' => $this->getId(),
            'label' => $this->getName(),
            'valid' => $this->isValid(),
        ];
    }

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'typeId' => $this->getTypeId(),
            'picture' => $this->getPicture(),
            'secondPicture' => $this->getSecondPicture(),
            'pickPicture' => $this->getPickPicture(),
            'probability' => $this->getProbability(),
            'canShow' => $this->isCanShow(),
            'canShowPrize' => $this->getCanShowPrize(),
            'isDefault' => $this->getIsDefault(),
            'valid' => $this->isValid(),
            'needConsignee' => $this->getNeedConsignee(),
            'needReview' => $this->isNeedReview(),
            'dayLimit' => $this->getDayLimit(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'content' => $this->getContent(),
            'type' => $this->getType(),
            'typeId' => $this->getTypeId(),
            'pool' => $this->getPool()?->getTitle(),
            'picture' => $this->getPicture(),
            'secondPicture' => $this->getSecondPicture(),
            'pickPicture' => $this->getPickPicture(),
            'probability' => $this->getProbability(),
            'canShow' => $this->isCanShow(),
            'canShowPrize' => $this->getCanShowPrize(),
            'isDefault' => $this->getIsDefault(),
            'needConsignee' => $this->getNeedConsignee(),
            'needReview' => $this->isNeedReview(),
            'dayLimit' => $this->getDayLimit(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
        ];
    }
    
    public function retrieveLockResource(): string
    {
        return $this->getId() . '';
    }
}
