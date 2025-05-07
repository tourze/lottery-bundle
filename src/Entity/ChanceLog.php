<?php

namespace LotteryBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineUserAgentBundle\Attribute\CreateUserAgentColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '抽奖机会流转记录')]
#[Deletable]
#[ORM\Entity]
#[ORM\Table(name: 'ims_draw_activity_member_extra_join_chance_flow_log', options: ['comment' => '抽奖机会流转记录'])]
class ChanceLog
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[Groups(['restful_read', 'api_tree', 'admin_curd', 'api_list'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[IndexColumn]
    #[ORM\Column(name: 'member_id', type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '用户ID'])]
    private ?int $memberId = null;

    #[ORM\Column(name: 'type', type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '流水类型，1 增加、2 扣减'])]
    private ?int $type = null;

    #[IndexColumn]
    #[ORM\Column(name: 'draw_id', type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '抽奖活动ID'])]
    private ?int $drawId = null;

    #[IndexColumn]
    #[ORM\Column(name: 'pool_id', type: Types::INTEGER, nullable: true, options: ['default' => 0, 'comment' => '奖池ID'])]
    private ?int $poolId = null;

    #[ORM\Column(name: 'add_num', type: Types::INTEGER, nullable: false, options: ['default' => 0, 'comment' => '增加次数'])]
    private ?int $addNum = null;

    #[ORM\Column(name: 'remark', type: Types::STRING, length: 100, nullable: true, options: ['default' => '', 'comment' => '表更备注'])]
    private ?string $remark = null;

    #[CreateUserAgentColumn]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '创建时UA'])]
    private ?string $createdFromUa = null;

    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[ListColumn(order: 99)]
    #[CreateIpColumn]
    #[ORM\Column(length: 45, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    public function setCreatedBy(?string $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function getMemberId(): ?int
    {
        return $this->memberId;
    }

    public function setMemberId(int $memberId): static
    {
        $this->memberId = $memberId;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDrawId(): ?int
    {
        return $this->drawId;
    }

    public function setDrawId(int $drawId): static
    {
        $this->drawId = $drawId;

        return $this;
    }

    public function getPoolId(): ?int
    {
        return $this->poolId;
    }

    public function setPoolId(int $poolId): static
    {
        $this->poolId = $poolId;

        return $this;
    }

    public function getAddNum(): ?int
    {
        return $this->addNum;
    }

    public function setAddNum(int $addNum): static
    {
        $this->addNum = $addNum;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(string $remark): static
    {
        $this->remark = $remark;

        return $this;
    }

    public function getCreatedFromUa(): ?string
    {
        return $this->createdFromUa;
    }

    public function setCreatedFromUa(?string $createdFromUa): static
    {
        $this->createdFromUa = $createdFromUa;

        return $this;
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

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): void
    {
        $this->createdFromIp = $createdFromIp;
    }
}
