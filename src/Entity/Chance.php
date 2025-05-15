<?php

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
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;

#[ORM\Entity(repositoryClass: ChanceRepository::class)]
#[ORM\Table(name: 'lottery_chance', options: ['comment' => '抽奖机会'])]
class Chance implements PlainArrayInterface, ApiArrayInterface, AdminArrayInterface, \Stringable, BenefitResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: Activity::class, cascade: ['persist'], inversedBy: 'chances')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '任务标题', 'default' => ''])]
    private ?string $title = '';

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '失效时间'])]
    private ?\DateTimeInterface $expireTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '使用时间'])]
    private ?\DateTimeInterface $useTime = null;

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

    #[ORM\OneToOne(targetEntity: Consignee::class, mappedBy: 'chance', cascade: ['persist', 'remove'])]
    private ?Consignee $consignee = null;

    /**
     * 关联的库存信息.
     *
     * @var Collection<Stock>
     */
    #[ORM\OneToMany(targetEntity: Stock::class, mappedBy: 'chance')]
    private Collection $stocks;

    /**
     * 如果这里为true，同时在开始时间开始收、失效时间结束前这个机会才能使用
     * 在使用后，这个状态会变更为false.
     */
    #[IndexColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否有效'])]
    private ?bool $valid = null;

    #[ORM\Column(length: 100, nullable: true, enumType: ChanceStatusEnum::class, options: ['comment' => '状态'])]
    private ?ChanceStatusEnum $status = ChanceStatusEnum::INIT;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '发送时间'])]
    private ?\DateTimeInterface $sendTime = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '发送结果'])]
    private ?array $sendResult = [];

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '奖池上下文'])]
    private ?array $poolContext = [];

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '概率上下文'])]
    private ?array $probabilityContext = [];

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1, 'comment' => '乐观锁版本号'])]
    private ?int $lockVersion = null;

    #[IndexColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '获取时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '抽奖时间'])]
    private ?\DateTimeInterface $updateTime = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remark = null;

    #[ORM\ManyToOne]
    private ?UserInterface $reviewUser = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '审核时间'])]
    private ?string $reviewTime = null;

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
        $this->stocks = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return ClassUtils::getClass($this) . '-' . $this->getId();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getExpireTime(): ?\DateTimeInterface
    {
        return $this->expireTime;
    }

    public function setExpireTime(?\DateTimeInterface $expireTime): self
    {
        $this->expireTime = $expireTime;

        return $this;
    }

    public function getPrize(): ?Prize
    {
        return $this->prize;
    }

    public function setPrize(?Prize $prize): self
    {
        $this->prize = $prize;

        return $this;
    }

    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(?Pool $pool): self
    {
        $this->pool = $pool;

        return $this;
    }

    public function getConsignee(): ?Consignee
    {
        return $this->consignee;
    }

    public function setConsignee(Consignee $consignee): self
    {
        // set the owning side of the relation if necessary
        if ($consignee->getChance() !== $this) {
            $consignee->setChance($this);
        }

        $this->consignee = $consignee;

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
            $stock->setChance($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getChance() === $this) {
                $stock->setChance(null);
            }
        }

        return $this;
    }

    public function getUseTime(): ?\DateTimeInterface
    {
        return $this->useTime;
    }

    public function setUseTime(?\DateTimeInterface $useTime): self
    {
        $this->useTime = $useTime;

        return $this;
    }

    public function getSendTime(): ?\DateTimeInterface
    {
        return $this->sendTime;
    }

    public function setSendTime(?\DateTimeInterface $sendTime): self
    {
        $this->sendTime = $sendTime;

        return $this;
    }

    public function getSendResult(): ?array
    {
        return $this->sendResult;
    }

    public function setSendResult(?array $sendResult): self
    {
        $this->sendResult = $sendResult;

        return $this;
    }

    public function getPoolContext(): ?array
    {
        return $this->poolContext;
    }

    public function setPoolContext(?array $poolContext): self
    {
        $this->poolContext = $poolContext;

        return $this;
    }

    public function getProbabilityContext(): ?array
    {
        return $this->probabilityContext;
    }

    public function setProbabilityContext(?array $probabilityContext): self
    {
        $this->probabilityContext = $probabilityContext;

        return $this;
    }

    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }

    public function setLockVersion(?int $lockVersion): self
    {
        $this->lockVersion = $lockVersion;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitle(): string
    {
        return strval($this->title);
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    #[ExportColumn(title: '奖品配置')]
    public function getPrizeConfig(): ?string
    {
        if (empty($this->getPrize())) {
            return '';
        }

        return $this->getPrize()->getTypeId();
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): self
    {
        $this->createTime = $createdAt;

        return $this;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updatedAt): self
    {
        $this->updateTime = $updatedAt;

        return $this;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function retrieveApiArray(): array
    {
        return array_merge($this->retrieveAdminArray(), [
            'user' => [
                'id' => $this->getUser()?->getId(),
                'avatar' => $this->getUser()?->getAvatar(),
                'username' => $this->getUser()?->getUsername(),
                'identity' => $this->getUser()?->getIdentity(),
                'nickName' => $this->getUser()?->getNickName(),
                'email' => $this->getUser()?->getEmail(),
                'mobile' => $this->getUser()?->getMobile(),
                'createTime' => $this->getUser()?->getCreateTime(),
                'updateTime' => $this->getUser()?->getUpdateTime(),
                'valid' => $this->getUser()?->isValid(),
            ],
        ]);
    }

    public function retrievePlainArray(): array
    {
        if (empty($this->id)) {
            return [];
        }

        return $this->retrieveApiArray();
    }

    public function retrieveAdminArray(): array
    {
        if (empty($this->id)) {
            return [];
        }

        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'valid' => $this->getValid(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'expireTime' => $this->getExpireTime()?->format('Y-m-d H:i:s'),
            'useTime' => $this->getUseTime()?->format('Y-m-d H:i:s'),
            'prize' => $this->getPrize()?->retrievePlainArray(),
            'consignee' => $this->getConsignee()?->retrievePlainArray(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'remark' => $this->getRemark(),
            'status' => $this->getStatus(),
        ];
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): static
    {
        $this->remark = $remark;

        return $this;
    }

    public function getStatus(): ?ChanceStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ChanceStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getReviewTime(): ?string
    {
        return $this->reviewTime;
    }

    public function setReviewTime(?string $reviewTime): static
    {
        $this->reviewTime = $reviewTime;

        return $this;
    }

    public function getReviewUser(): ?UserInterface
    {
        return $this->reviewUser;
    }

    public function setReviewUser(?UserInterface $reviewUser): static
    {
        $this->reviewUser = $reviewUser;

        return $this;
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
}
