<?php

declare(strict_types=1);

namespace LotteryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\PrizeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrinePrecisionBundle\Attribute\PrecisionColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Itemable;
use Tourze\LockServiceBundle\Model\LockEntity;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: PrizeRepository::class)]
#[ORM\Table(name: 'lottery_prize', options: ['comment' => '奖品信息'])]
class Prize implements \Stringable, Itemable, PlainArrayInterface, AdminArrayInterface, LockEntity
{
    use TimestampableAware;
    use BlameableAware;

    /** @phpstan-ignore-next-line */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 60, options: ['comment' => '名称'])]
    #[Assert\NotBlank(message: '奖品名称不能为空')]
    #[Assert\Length(max: 60, maxMessage: '奖品名称不能超过 {{ limit }} 个字符')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '奖品描述'])]
    #[Assert\Length(max: 65535, maxMessage: '奖品描述不能超过 {{ limit }} 个字符')]
    private ?string $content = null;

    // 奖品类型信息
    #[ORM\Column(type: Types::STRING, length: 60, options: ['comment' => '类型'])]
    #[Assert\NotBlank(message: '奖品类型不能为空')]
    #[Assert\Length(max: 60, maxMessage: '奖品类型不能超过 {{ limit }} 个字符')]
    private string $type;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '类型值ID'])]
    #[Assert\Length(max: 255, maxMessage: '类型值 ID 不能超过 {{ limit }} 个字符')]
    private ?string $typeId = null;

    // 奖品数量与概率
    #[ORM\Column(nullable: true, options: ['comment' => '单次派发数量', 'default' => 1])]
    #[Assert\PositiveOrZero(message: '派发数量必须是非负整数')]
    private ?int $amount = 1;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '总数量'])]
    #[Assert\PositiveOrZero(message: '总数量必须是非负整数')]
    private int $quantity = 0;

    #[ORM\Column(nullable: true, options: ['default' => '0', 'comment' => '每日数量'])]
    #[Assert\PositiveOrZero(message: '每日数量必须是非负整数')]
    private ?int $dayLimit = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '概率数'])]
    #[Assert\PositiveOrZero(message: '概率数必须是非负整数')]
    private int $probability = 0;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '概率表达式'])]
    #[Assert\Length(max: 65535, maxMessage: '概率表达式不能超过 {{ limit }} 个字符')]
    private ?string $probabilityExpression = null;

    #[PrecisionColumn]
    #[ORM\Column(
        type: Types::DECIMAL,
        precision: 10,
        scale: 2,
        nullable: true,
        options: ['default' => '0.00', 'comment' => '奖品价值']
    )]
    #[Assert\Length(max: 12, maxMessage: '奖品价值不能超过 {{ limit }} 个字符')]
    #[Assert\PositiveOrZero(message: '奖品价值必须是非负数')]
    #[Assert\Regex(pattern: '/^\d{1,8}(\.\d{1,2})?$/', message: '奖品价值格式不正确，最多8位整数和2位小数')]
    private ?string $value = null;

    #[ORM\Column(nullable: true, options: ['comment' => '派发后有效天数'])]
    #[Assert\PositiveOrZero(message: '有效天数必须是非负数')]
    private ?float $expireDay = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '派发后到期时间'])]
    #[Assert\Type(type: '\DateTimeImmutable', message: '到期时间必须是有效的日期时间')]
    private ?\DateTimeImmutable $expireTime = null;

    #[ORM\Column(length: 512, nullable: true, options: ['comment' => '主图'])]
    #[Assert\Length(max: 512, maxMessage: '主图路径不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '主图路径格式不正确')]
    private ?string $picture = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '选中图片'])]
    #[Assert\Length(max: 255, maxMessage: '选中图片路径不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '选中图片路径格式不正确')]
    private ?string $secondPicture = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '中奖图片'])]
    #[Assert\Length(max: 255, maxMessage: '中奖图片路径不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '中奖图片路径格式不正确')]
    private ?string $pickPicture = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '地址图片'])]
    #[Assert\Length(max: 255, maxMessage: '地址图片路径不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '地址图片路径格式不正确')]
    private ?string $consigneePicture = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否参与轮播', 'default' => true])]
    #[Assert\Type(type: 'bool', message: '是否参与轮播必须是布尔值')]
    private bool $canShow = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否在奖品列表展示', 'default' => true])]
    #[Assert\Type(type: 'bool', message: '是否在奖品列表展示必须是布尔值')]
    private ?bool $canShowPrize = true;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '兜底奖项'])]
    #[Assert\Type(type: 'bool', message: '兜底奖项必须是布尔值')]
    private bool $isDefault = false;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '登记收货地址'])]
    #[Assert\Type(type: 'bool', message: '登记收货地址必须是布尔值')]
    private ?bool $needConsignee = false;

    #[ORM\Column(nullable: true, options: ['comment' => '需要审核', 'default' => false])]
    #[Assert\Type(type: 'bool', message: '需要审核必须是布尔值')]
    private ?bool $needReview = false;

    #[ORM\ManyToOne(targetEntity: Pool::class, inversedBy: 'prizes')]
    private ?Pool $pool = null;

    /**
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'prize', orphanRemoval: true)]
    private Collection $stocks;

    #[IndexColumn]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    #[Assert\PositiveOrZero(message: '次序值必须是非负整数')]
    private ?int $sortNumber = 0;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[Assert\Type(type: 'bool', message: '有效状态必须是布尔值')]
    private ?bool $valid = false;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (null === $this->getId() || 0 === $this->getId()) {
            return '';
        }

        $pool = $this->getPool();
        $poolTitle = $pool?->getTitle() ?? 'Unknown Pool';
        $prizeName = $this->getName() ?? 'Unknown Prize';

        return "{$poolTitle}-{$prizeName}";
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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getTypeId(): ?string
    {
        return $this->typeId;
    }

    public function setTypeId(?string $typeId): void
    {
        $this->typeId = $typeId;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getDayLimit(): ?int
    {
        return $this->dayLimit;
    }

    public function setDayLimit(?int $dayLimit): void
    {
        $this->dayLimit = $dayLimit;
    }

    public function getProbability(): ?int
    {
        return $this->probability;
    }

    public function setProbability(int $probability): void
    {
        $this->probability = $probability;
    }

    public function getProbabilityExpression(): ?string
    {
        return $this->probabilityExpression;
    }

    public function setProbabilityExpression(?string $probabilityExpression): void
    {
        $this->probabilityExpression = $probabilityExpression;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    // 有效期相关
    public function getExpireDay(): ?float
    {
        return $this->expireDay;
    }

    public function setExpireDay(?float $expireDay): void
    {
        $this->expireDay = $expireDay;
    }

    public function getExpireTime(): ?\DateTimeImmutable
    {
        return $this->expireTime;
    }

    public function setExpireTime(?\DateTimeImmutable $expireTime): void
    {
        $this->expireTime = $expireTime;
    }

    // 显示相关
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): void
    {
        $this->picture = $picture;
    }

    public function getSecondPicture(): ?string
    {
        return $this->secondPicture;
    }

    public function setSecondPicture(?string $secondPicture): void
    {
        $this->secondPicture = $secondPicture;
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

    public function setCanShow(bool $canShow): void
    {
        $this->canShow = $canShow;
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

    public function setIsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
    }

    public function getNeedConsignee(): ?bool
    {
        return $this->needConsignee;
    }

    public function setNeedConsignee(?bool $needConsignee): void
    {
        $this->needConsignee = $needConsignee;
    }

    public function isNeedReview(): ?bool
    {
        return $this->needReview;
    }

    public function setNeedReview(?bool $needReview): void
    {
        $this->needReview = $needReview;
    }

    // 关联实体相关
    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(?Pool $pool): void
    {
        $this->pool = $pool;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): void
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks->add($stock);
            $stock->setPrize($this);
        }
    }

    public function removeStock(Stock $stock): void
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getPrize() === $this) {
                $stock->setPrize(null);
            }
        }
    }

    // 排序相关
    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): void
    {
        $this->sortNumber = $sortNumber;
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

    // 审计信息相关
    // 接口实现方法
    /**
     * @return array<string, mixed>
     */
    public function toSelectItem(): array
    {
        return [
            'value' => $this->getId(),
            'label' => $this->getName(),
            'valid' => $this->isValid(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
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

    /**
     * @return array<string, mixed>
     */
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
