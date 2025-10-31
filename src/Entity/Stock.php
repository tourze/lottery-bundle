<?php

declare(strict_types=1);

namespace LotteryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\StockRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: StockRepository::class)]
#[ORM\Table(name: 'lottery_stock', options: ['comment' => '库存记录'])]
class Stock implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    /** @phpstan-ignore-next-line */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[SnowflakeColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '序列号'])]
    #[Assert\NotBlank(message: '序列号不能为空')]
    #[Assert\Length(max: 100, maxMessage: '序列号不能超过 {{ limit }} 个字符')]
    private ?string $sn = null;

    #[ORM\ManyToOne(targetEntity: Prize::class, inversedBy: 'stocks')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull(message: '必须关联到一个奖品')]
    private ?Prize $prize = null;

    #[ORM\ManyToOne(targetEntity: Chance::class, inversedBy: 'stocks')]
    private ?Chance $chance = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1, 'comment' => '乐观锁版本号'])]
    #[Assert\PositiveOrZero(message: '版本号必须是非负整数')]
    private ?int $lockVersion = null;

    public function __toString(): string
    {
        $prize = $this->getPrize();
        $prizeName = $prize?->getName() ?? 'Unknown Prize';
        $sn = $this->getSn() ?? 'Unknown SN';

        return "{$prizeName} {$sn}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSn(): ?string
    {
        return $this->sn;
    }

    public function setSn(string $sn): void
    {
        $this->sn = $sn;
    }

    public function getPrize(): ?Prize
    {
        return $this->prize;
    }

    public function setPrize(?Prize $prize): void
    {
        $this->prize = $prize;
    }

    public function getChance(): ?Chance
    {
        return $this->chance;
    }

    public function setChance(?Chance $chance): void
    {
        $this->chance = $chance;
    }

    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }

    public function setLockVersion(?int $lockVersion): void
    {
        $this->lockVersion = $lockVersion;
    }
}
