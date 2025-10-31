<?php

declare(strict_types=1);

namespace LotteryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\PoolAttributeRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: PoolAttributeRepository::class)]
#[ORM\Table(name: 'lottery_pool_attribute', options: ['comment' => '奖池属性'])]
#[ORM\UniqueConstraint(name: 'idx_uniq_pool_name', columns: ['pool_id', 'name'])]
class PoolAttribute implements \Stringable, AdminArrayInterface
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '属性'])]
    #[Assert\NotBlank(message: '属性名称不能为空')]
    #[Assert\Length(max: 100, maxMessage: '属性名称不能超过 {{ limit }} 个字符')]
    private ?string $name = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '内容'])]
    #[Assert\NotBlank(message: '属性值不能为空')]
    #[Assert\Length(max: 255, maxMessage: '属性值不能超过 {{ limit }} 个字符')]
    private ?string $value = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    #[Assert\Length(max: 255, maxMessage: '备注不能超过 {{ limit }} 个字符')]
    private ?string $remark = null;

    #[ORM\ManyToOne(targetEntity: Pool::class, inversedBy: 'poolAttributes')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull(message: '必须关联到一个奖池')]
    private ?Pool $pool = null;

    public function __toString(): string
    {
        if (null === $this->getId() || '0' === $this->getId()) {
            return '';
        }

        return "{$this->getName()}:{$this->getValue()}";
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): void
    {
        $this->remark = $remark;
    }

    public function getPool(): ?Pool
    {
        return $this->pool;
    }

    public function setPool(?Pool $pool): void
    {
        $this->pool = $pool;
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'remark' => $this->getRemark(),
        ];
    }
}
