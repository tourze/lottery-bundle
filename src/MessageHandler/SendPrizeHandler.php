<?php

namespace LotteryBundle\MessageHandler;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Enum\ChanceStatusEnum;
use LotteryBundle\Message\SendPrizeMessage;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Service\PrizeService;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
#[WithMonologChannel(channel: 'lottery')]
class SendPrizeHandler
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ChanceRepository $chanceRepository,
        private readonly PrizeService $prizeService,
    ) {
    }

    public function __invoke(SendPrizeMessage $message): void
    {
        $chance = $this->chanceRepository->find($message->getChanceId());

        if (null === $chance) {
            $this->logger->error('找不到抽奖机会，不处理', [
                'id' => $message->getChanceId(),
            ]);

            return;
        }

        if (!in_array($chance->getStatus(), [ChanceStatusEnum::WINNING, ChanceStatusEnum::REVIEWED], true)) {
            $this->logger->info('不符合异步发奖的情况，不处理', [
                'id' => $message->getChanceId(),
                'status' => $chance->getStatus(),
            ]);

            return;
        }
        $this->prizeService->sendPrize($chance);
    }
}
