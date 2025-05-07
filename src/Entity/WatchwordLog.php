<?php

namespace LotteryBundle\Entity;

use AppBundle\Entity\BizUser;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use LotteryBundle\Repository\WatchwordLogRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '口令使用记录')]
#[Deletable]
#[ORM\Entity(repositoryClass: WatchwordLogRepository::class)]
#[ORM\Table(name: 'lottery_watchword_log', options: ['comment' => '口令使用记录'])]
class WatchwordLog
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
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

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

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[Groups(['admin_curd', 'restful_read', 'restful_read', 'restful_write'])]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[ListColumn(title: '活动')]
    #[FormField(title: '活动')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    #[ListColumn(title: '口令')]
    #[FormField(title: '口令')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Watchword $watchword = null;

    #[ListColumn(title: '用户')]
    #[FormField(title: '用户')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?BizUser $user = null;

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
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

    public function getWatchword(): ?Watchword
    {
        return $this->watchword;
    }

    public function setWatchword(?Watchword $watchword): self
    {
        $this->watchword = $watchword;

        return $this;
    }

    public function getUser(): ?BizUser
    {
        return $this->user;
    }

    public function setUser(?BizUser $user): self
    {
        $this->user = $user;

        return $this;
    }
}
