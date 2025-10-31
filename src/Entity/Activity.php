<?php

declare(strict_types=1);

namespace LotteryBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\ActivityRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\ResourceManageBundle\Model\ResourceIdentity;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ActivityRepository::class)]
#[ORM\Table(name: 'lottery_activity', options: ['comment' => '抽奖活动'])]
class Activity implements \Stringable, PlainArrayInterface, AdminArrayInterface, ResourceIdentity
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    // 基本信息
    #[ORM\Column(type: Types::STRING, length: 120, unique: true, options: ['comment' => '标题'])]
    #[Assert\NotBlank(message: '活动标题不能为空')]
    #[Assert\Length(max: 120, maxMessage: '活动标题不能超过 {{ limit }} 个字符')]
    private string $title = '';

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '规则文本'])]
    #[Assert\Length(max: 65535, maxMessage: '规则文本不能超过 {{ limit }} 个字符')]
    private ?string $textRule = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '开始时间'])]
    #[Assert\NotNull(message: '开始时间不能为空')]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '结束时间'])]
    #[Assert\NotNull(message: '结束时间不能为空')]
    #[Assert\GreaterThan(propertyPath: 'startTime', message: '结束时间必须晚于开始时间')]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '最后兑奖时间'])]
    #[Assert\Type(type: \DateTimeInterface::class)]
    private ?\DateTimeImmutable $lastRedeemTime = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '头图'])]
    #[Assert\Length(max: 255, maxMessage: '头图路径不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '头图路径格式不正确')]
    private ?string $headPhoto = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '找不到机会提醒文案'])]
    #[Assert\Length(max: 100, maxMessage: '提醒文案不能超过 {{ limit }} 个字符')]
    private ?string $noChanceText = '您已没有抽奖机会';

    // 分享相关
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '分享路径'])]
    #[Assert\Length(max: 100, maxMessage: '分享路径不能超过 {{ limit }} 个字符')]
    private ?string $sharePath = null;

    #[ORM\Column(length: 100, nullable: true, options: ['comment' => '分享标题'])]
    #[Assert\Length(max: 100, maxMessage: '分享标题不能超过 {{ limit }} 个字符')]
    private ?string $shareTitle = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '分享图片'])]
    #[Assert\Length(max: 255, maxMessage: '分享图片路径不能超过 {{ limit }} 个字符')]
    #[Assert\Url(message: '分享图片路径格式不正确')]
    private ?string $sharePicture = null;

    // 关联实体
    /**
     * @var Collection<int, Chance>
     */
    #[Ignore]
    #[ORM\OneToMany(targetEntity: Chance::class, mappedBy: 'activity')]
    private Collection $chances;

    /**
     * @var Collection<int, Pool>
     */
    #[ORM\ManyToMany(targetEntity: Pool::class, inversedBy: 'activities', fetch: 'EXTRA_LAZY')]
    private Collection $pools;

    // 状态信息
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[Assert\Type(type: 'bool')]
    private ?bool $valid = false;

    // 审计信息

    public function __construct()
    {
        $this->chances = new ArrayCollection();
        $this->pools = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (0 === $this->getId()) {
            return '';
        }

        return $this->getTitle();
    }

    // ID相关
    public function getId(): int
    {
        return $this->id;
    }

    // 基本信息相关
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTextRule(): ?string
    {
        return $this->textRule;
    }

    public function setTextRule(?string $textRule): void
    {
        $this->textRule = $textRule;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): void
    {
        $this->endTime = $endTime;
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

    public function setHeadPhoto(?string $headPhoto): void
    {
        $this->headPhoto = $headPhoto;
    }

    public function getNoChanceText(): ?string
    {
        return $this->noChanceText;
    }

    public function setNoChanceText(?string $noChanceText): void
    {
        $this->noChanceText = $noChanceText;
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

    public function addChance(Chance $chance): void
    {
        if (!$this->chances->contains($chance)) {
            $this->chances->add($chance);
            $chance->setActivity($this);
        }
    }

    public function removeChance(Chance $chance): void
    {
        if ($this->chances->removeElement($chance)) {
            // set the owning side to null (unless already changed)
            if ($chance->getActivity() === $this) {
                $chance->setActivity(null);
            }
        }
    }

    /**
     * @return Collection<int, Pool>
     */
    public function getPools(): Collection
    {
        return $this->pools;
    }

    public function addPool(Pool $pool): void
    {
        if (!$this->pools->contains($pool)) {
            $this->pools->add($pool);
            $pool->addActivity($this);
        }
    }

    public function removePool(Pool $pool): void
    {
        if ($this->pools->removeElement($pool)) {
            $pool->removeActivity($this);
        }
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

    // 审计信息相关 - 使用 IpTraceableAware trait

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
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

    /**
     * @return array<string, mixed>
     */
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
