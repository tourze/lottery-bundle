<?php

namespace LotteryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\ConsigneeRepository;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EnumExtra\Itemable;

#[ORM\Entity(repositoryClass: ConsigneeRepository::class)]
#[ORM\Table(name: 'lottery_consignee', options: ['comment' => '零配件产品表'])]
class Consignee implements \Stringable, Itemable, PlainArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '收货人ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '姓名'])]
    private ?string $realName = null;

    #[ORM\Column(type: Types::STRING, length: 30, options: ['comment' => '手机'])]
    private ?string $mobile = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '地址'])]
    private ?string $address = null;

    #[ORM\OneToOne(targetEntity: Chance::class, inversedBy: 'consignee', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Chance $chance = null;


    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __toString(): string
    {
        if ($this->getId() === null || $this->getId() === 0) {
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

    public function setRealName(string $realName): self
    {
        $this->realName = $realName;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getChance(): ?Chance
    {
        return $this->chance;
    }

    public function setChance(Chance $chance): self
    {
        $this->chance = $chance;

        return $this;
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
    }public function toSelectItem(): array
    {
        return [
            'label' => "{$this->getRealName()} {$this->getMobile()} {$this->getAddress()}",
            'text' => "{$this->getRealName()} {$this->getMobile()} {$this->getAddress()}",
            'value' => $this->getId(),
            'name' => "{$this->getRealName()} {$this->getMobile()} {$this->getAddress()}",
        ];
    }

    public function retrievePlainArray(): array
    {
        return [
            'realName' => $this->getRealName(),
            'mobile' => $this->getMobile(),
            'address' => $this->getAddress(),
        ];
    }
}
