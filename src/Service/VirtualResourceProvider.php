<?php

namespace LotteryBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\ResourceManageBundle\Model\ResourceIdentity;
use Tourze\ResourceManageBundle\Service\ResourceProvider;

class VirtualResourceProvider implements ResourceProvider
{
    public const CODE = 'virtual';

    public function getCode(): string
    {
        return self::CODE;
    }

    public function getLabel(): string
    {
        return '虚拟';
    }

    public function getIdentities(): ?iterable
    {
        return null;
    }

    public function findIdentity(string $identity): ?ResourceIdentity
    {
        return null;
    }

    public function sendResource(UserInterface $user, ?ResourceIdentity $identity, string $amount, int|float|null $expireDay = null, ?\DateTimeInterface $expireTime = null): void
    {
        // 虚拟奖，什么都不用做
    }
}
