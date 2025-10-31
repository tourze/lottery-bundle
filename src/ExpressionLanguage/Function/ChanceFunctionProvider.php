<?php

namespace LotteryBundle\ExpressionLanguage\Function;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use LotteryBundle\Entity\Activity;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Service\LotteryService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * 机会相关函数
 */
#[AutoconfigureTag(name: 'ecol.function.provider')]
#[WithMonologChannel(channel: 'lottery')]
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
            new ExpressionFunction(
                'getLotteryValidChannelCount',
                fn (...$args) => sprintf('\%s(%s)', 'getLotteryValidChannelCount', implode(', ', is_array($args) ? $args : [])),
                function ($values, ...$args) {
                    $this->logger->debug('getLotteryValidChannelCount', [
                        'values' => $values,
                        'args' => $args,
                    ]);

                    assert(is_array($values), 'Values must be an array');
                    assert(isset($args[0]) && $args[0] instanceof UserInterface, 'First argument must be UserInterface');
                    assert(isset($args[1]) && $args[1] instanceof Activity, 'Second argument must be Activity');
                    /** @var array<string, mixed> $values */
                    /** @var UserInterface $user */
                    $user = $args[0];
                    /** @var Activity $activity */
                    $activity = $args[1];

                    return $this->getLotteryValidChannelCount($values, $user, $activity);
                }
            ),

            new ExpressionFunction(
                'giveLotteryChannel',
                fn (...$args) => sprintf('\%s(%s)', 'giveLotteryChannel', implode(', ', is_array($args) ? $args : [])),
                function ($values, ...$args) {
                    $this->logger->debug('giveLotteryChannel', [
                        'values' => $values,
                        'args' => $args,
                    ]);

                    assert(is_array($values), 'Values must be an array');
                    assert(isset($args[0]) && $args[0] instanceof UserInterface, 'First argument must be UserInterface');
                    assert(isset($args[1]) && $args[1] instanceof Activity, 'Second argument must be Activity');
                    assert(isset($args[2]) && (is_string($args[2]) || $args[2] instanceof \DateTimeInterface), 'Third argument must be string or DateTimeInterface');
                    /** @var array<string, mixed> $values */
                    /** @var UserInterface $user */
                    $user = $args[0];
                    /** @var Activity $activity */
                    $activity = $args[1];
                    /** @var string|\DateTimeInterface $expireTime */
                    $expireTime = $args[2];

                    return $this->giveLotteryChannel($values, $user, $activity, $expireTime);
                }
            ),
        ];
    }

    /**
     * 获取指定用户指定用户的有效抽奖次数
     * 使用例子： getLotteryValidChannelCount(user, activity)
     */
    /**
     * @param array<string, mixed> $values
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
    /**
     * @param array<string, mixed> $values
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
