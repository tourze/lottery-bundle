<?php

namespace LotteryBundle\ExpressionLanguage\Function;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Service\LotteryService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 机会相关函数
 */
#[AutoconfigureTag('ecol.function.provider')]
class ChanceFunctionProvider implements ExpressionFunctionProviderInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly LotteryService $lotteryService,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new ExpressionFunction('getLotteryValidChannelCount', fn (...$args) => sprintf('\%s(%s)', 'getLotteryValidChannelCount', implode(', ', $args)), function ($values, ...$args) {
                $this->logger->debug('getLotteryValidChannelCount', [
                    'values' => $values,
                    'args' => $args,
                ]);

                return $this->getLotteryValidChannelCount($values, ...$args);
            }),

            new ExpressionFunction('giveLotteryChannel', fn (...$args) => sprintf('\%s(%s)', 'giveLotteryChannel', implode(', ', $args)), function ($values, ...$args) {
                $this->logger->debug('giveLotteryChannel', [
                    'values' => $values,
                    'args' => $args,
                ]);

                return $this->giveLotteryChannel($values, ...$args);
            }),
        ];
    }

    /**
     * 获取指定用户指定用户的有效抽奖次数
     * 使用例子： getLotteryValidChannelCount(user, activity)
     */
    public function getLotteryValidChannelCount(array $values, UserInterface $user, Activity $activity): int
    {
        if (!($user instanceof PasswordAuthenticatedUserInterface)) {
            return 0;
        }

        return $this->lotteryService->countValidChance($user, $activity);
    }

    /**
     * 给指定用户赠送抽奖机会
     * 使用例子： giveLotteryChannel(user, activity)
     *
     * @param string|CarbonInterface|\DateTimeInterface $expireTime 过期时间
     */
    public function giveLotteryChannel(
        array $values,
        UserInterface $user,
        Activity $activity,
        string|CarbonInterface|\DateTimeInterface $expireTime,
    ): bool {
        if (!($user instanceof PasswordAuthenticatedUserInterface)) {
            $this->logger->warning('非正常用户信息，不允许发送抽奖机会', [
                'user' => $user,
                'activity' => $user,
                'values' => $values,
            ]);

            return false;
        }

        if (is_string($expireTime)) {
            $expireTime = CarbonImmutable::parse($expireTime);
        }

        $chance = new Chance();
        $chance->setValid(true);
        $chance->setActivity($activity);
        $chance->setUser($user);
        $chance->setStartTime(CarbonImmutable::now());
        $chance->setExpireTime($expireTime);

        try {
            $this->lotteryService->giveChance($user, $chance);

            return true;
        } catch (\Throwable $exception) {
            $this->logger->error('发送抽奖机会时发生异常', [
                'exception' => $exception,
                'chance' => $chance,
            ]);

            return false;
        }
    }
}
