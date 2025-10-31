<?php

declare(strict_types=1);

namespace LotteryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\ConsigneeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Itemable;

/**
 * @implements PlainArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: ConsigneeRepository::class)]
#[ORM\Table(name: 'lottery_consignee', options: ['comment' => '零配件产品表'])]
class Consignee implements \Stringable, Itemable, PlainArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    /** @phpstan-ignore-next-line */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '收货人ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '姓名'])]
    #[Assert\NotBlank(message: '姓名不能为空')]
    #[Assert\Length(max: 100, maxMessage: '姓名不能超过 {{ limit }} 个字符')]
    private ?string $realName = null;

    #[ORM\Column(type: Types::STRING, length: 30, options: ['comment' => '手机'])]
    #[Assert\NotBlank(message: '手机号不能为空')]
    #[Assert\Length(max: 30, maxMessage: '手机号不能超过 {{ limit }} 个字符')]
    #[Assert\Regex(pattern: '/^1[3-9]\d{9}$/', message: '手机号格式不正确')]
    #[Assert\Type(type: 'string', message: '手机号必须是字符串')]
    private ?string $mobile = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '地址'])]
    #[Assert\NotBlank(message: '地址不能为空')]
    #[Assert\Length(max: 255, maxMessage: '地址不能超过 {{ limit }} 个字符')]
    private ?string $address = null;

    #[ORM\OneToOne(targetEntity: Chance::class, mappedBy: 'consignee')]
    #[Assert\NotNull(message: '必须关联到一个抽奖机会')]
    #[Assert\Valid]
    private ?Chance $chance = null;

    public function __toString(): string
    {
        if (null === $this->getId() || 0 === $this->getId()) {
            return '';
        }

        return "{$this->getRealName()} {$this->getMobile()} {$this->getAddress()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRealName(): ?string
    {
        return $this->realName;
    }

    public function setRealName(string $realName): void
    {
        $this->realName = $realName;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getChance(): ?Chance
    {
        return $this->chance;
    }

    public function setChance(?Chance $chance): void
    {
        $this->chance = $chance;
    }

    /**
     * @return array<string, mixed>
     */
    public function toSelectItem(): array
    {
        return [
            'label' => "{$this->getRealName()} {$this->getMobile()} {$this->getAddress()}",
            'text' => "{$this->getRealName()} {$this->getMobile()} {$this->getAddress()}",
            'value' => $this->getId(),
            'name' => "{$this->getRealName()} {$this->getMobile()} {$this->getAddress()}",
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return [
            'realName' => $this->getRealName(),
            'mobile' => $this->getMobile(),
            'address' => $this->getAddress(),
        ];
    }
}
