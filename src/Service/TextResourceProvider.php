<?php

namespace LotteryBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\ResourceManageBundle\Model\ResourceIdentity;
use Tourze\ResourceManageBundle\Service\ResourceProvider;

class TextResourceProvider implements ResourceProvider
{
    public const CODE = 'text';

    public function getCode(): string
    {
        return self::CODE;
    }

    public function getLabel(): string
    {
        return '文本(安慰奖)';
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
        // 安慰奖，什么都不用做
    }
}
