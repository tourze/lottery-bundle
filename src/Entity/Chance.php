<?php

declare(strict_types=1);

namespace LotteryBundle\Entity;

use BenefitBundle\Model\BenefitResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements ApiArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ChanceRepository::class)]
#[ORM\Table(name: 'lottery_chance', options: ['comment' => '抽奖机会'])]
class Chance implements PlainArrayInterface, ApiArrayInterface, AdminArrayInterface, \Stringable, BenefitResource
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '任务标题', 'default' => ''])]
    #[Assert\Length(max: 100, maxMessage: '任务标题不能超过 {{ limit }} 个字符')]
    private ?string $title = '';

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '开始时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $startTime = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '失效时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $expireTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '使用时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $useTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeInterface $sendTime = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '备注'])]
    #[Assert\Length(max: 255, maxMessage: '备注不能超过 {{ limit }} 个字符')]
    private ?string $remark = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否有效'])]
    #[Assert\Type(type: 'bool')]
    private ?bool $valid = null;

    #[ORM\Column(length: 100, nullable: true, enumType: ChanceStatusEnum::class, options: ['comment' => '状态'])]
    #[Assert\Choice(callback: [ChanceStatusEnum::class, 'cases'])]
    private ChanceStatusEnum $status = ChanceStatusEnum::INIT;

    #[ORM\ManyToOne(targetEntity: Activity::class, cascade: ['persist'], inversedBy: 'chances')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: '必须关联到一个活动')]
    private ?Activity $activity = null;

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    /**
     * 在机会消耗前，我们可以指定机会必中哪个奖池，做得更加灵活.
     */
    #[ORM\ManyToOne(targetEntity: Pool::class)]
    private ?Pool $pool = null;

    /**
     * 在机会被消耗前，我们就可以指定这次机会可以中什么奖品，可以更加灵活咯
     * 目前设计，一次抽奖只会抽中一个奖品，数量也是一
     */
    #[ORM\ManyToOne(targetEntity: Prize::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Prize $prize = null;

    #[ORM\OneToOne(targetEntity: Consignee::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'consignee_id', referencedColumnName: 'id')]
    private ?Consignee $consignee = null;

    /**
     * 关联的库存信息.
     *
     * @var Collection<int, Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'chance')]
    private Collection $stocks;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '发送结果'])]
    #[Assert\Type(type: 'array')]
    private ?array $sendResult = [];

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '奖池上下文'])]
    #[Assert\Type(type: 'array')]
    private ?array $poolContext = [];

    /**
     * @var array<int, array<string, mixed>>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '概率上下文'])]
    #[Assert\Type(type: 'array')]
    private ?array $probabilityContext = [];

    #[ORM\ManyToOne]
    private ?UserInterface $reviewUser = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '审核时间'])]
    #[Assert\Length(max: 100, maxMessage: '审核时间不能超过 {{ limit }} 个字符')]
    private ?string $reviewTime = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1, 'comment' => '乐观锁版本号'])]
    #[Assert\PositiveOrZero(message: '版本号必须是非负整数')]
    private ?int $lockVersion = null;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return ClassUtils::getClass($this) . '-' . $this->getId();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title ?? '';
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getExpireTime(): ?\DateTimeInterface
    {
        return $this->expireTime;
    }

    public function setExpireTime(?\DateTimeInterface $expireTime): void
    {
        $this->expireTime = $expireTime;
    }

    public function getUseTime(): ?\DateTimeInterface
    {
        return $this->useTime;
    }

    public function setUseTime(?\DateTimeInterface $useTime): void
    {
        $this->useTime = $useTime;
    }

    public function getSendTime(): ?\DateTimeInterface
    {
        return $this->sendTime;
    }

    public function setSendTime(?\DateTimeInterface $sendTime): void
    {
        $this->sendTime = $sendTime;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    // 状态相关
    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getStatus(): ?ChanceStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ChanceStatusEnum $status): void
    {
        $this->status = $status;
    }

    // 关联实体相关
    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): void
    {
        $this->activity = $activity;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(?Pool $pool): void
    {
        $this->pool = $pool;
    }

    public function getPrize(): ?Prize
    {
        return $this->prize;
    }

    public function setPrize(?Prize $prize): void
    {
        $this->prize = $prize;
    }

    public function getConsignee(): ?Consignee
    {
        return $this->consignee;
    }

    public function setConsignee(?Consignee $consignee): void
    {
        $this->consignee = $consignee;
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
            $stock->setChance($this);
        }
    }

    public function removeStock(Stock $stock): void
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getChance() === $this) {
                $stock->setChance(null);
            }
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getSendResult(): ?array
    {
        return $this->sendResult;
    }

    /**
     * @param array<string, mixed>|null $sendResult
     */
    public function setSendResult(?array $sendResult): void
    {
        $this->sendResult = $sendResult;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPoolContext(): ?array
    {
        return $this->poolContext;
    }

    /**
     * @param array<string, mixed>|null $poolContext
     */
    public function setPoolContext(?array $poolContext): void
    {
        $this->poolContext = $poolContext;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    public function getProbabilityContext(): ?array
    {
        return $this->probabilityContext;
    }

    /**
     * @param array<int, array<string, mixed>>|null $probabilityContext
     */
    public function setProbabilityContext(?array $probabilityContext): void
    {
        $this->probabilityContext = $probabilityContext;
    }

    // 奖品配置信息
    public function getPrizeConfig(): ?string
    {
        return $this->getPrize()?->getName();
    }

    // 审核相关
    public function getReviewTime(): ?string
    {
        return $this->reviewTime;
    }

    public function setReviewTime(?string $reviewTime): void
    {
        $this->reviewTime = $reviewTime;
    }

    public function getReviewUser(): ?UserInterface
    {
        return $this->reviewUser;
    }

    public function setReviewUser(?UserInterface $reviewUser): void
    {
        $this->reviewUser = $reviewUser;
    }

    // 版本控制相关
    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }

    public function setLockVersion(?int $lockVersion): void
    {
        $this->lockVersion = $lockVersion;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveApiArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'expireTime' => $this->getExpireTime()?->format('Y-m-d H:i:s'),
            'prize' => $this->getPrize()?->retrievePlainArray(),
            'status' => $this->getStatus()?->value,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return $this->retrieveApiArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'expireTime' => $this->getExpireTime()?->format('Y-m-d H:i:s'),
            'useTime' => $this->getUseTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->getValid(),
            'prize' => $this->getPrize()?->getName(),
            'activity' => $this->getActivity()?->getTitle(),
            'status' => $this->getStatus()?->value,
            'remark' => $this->getRemark(),
        ];
    }
}
