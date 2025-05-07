<?php

namespace LotteryBundle\Procedure\Chance;

use AntdCpBundle\Builder\Action\ApiCallAction;
use AppBundle\Procedure\Base\ApiCallActionProcedure;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Service\PrizeService;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLogBundle\Attribute\Log;
use Tourze\JsonRPCSecurityBundle\Attribute\MethodPermission;

#[Log]
#[MethodTag('抽奖模块')]
#[MethodDoc('重新发送中奖奖品')]
#[MethodExpose(AdminResendLottery::NAME)]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[MethodPermission(permission: Chance::class . '::renderSendAction', title: '补发')]
class AdminResendLottery extends ApiCallActionProcedure
{
    public const NAME = 'AdminResendLottery';

    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly PrizeService $prizeService,
    ) {
    }

    public function getAction(): ApiCallAction
    {
        return ApiCallAction::gen()
            ->setLabel('补发')
            ->setConfirmText('是否确认要补发奖品？注意不要重复发送')
            ->setApiName(AdminResendLottery::NAME);
    }

    public function execute(): array
    {
        $that = $this->chanceRepository->findOneBy(['id' => $this->id]);
        if (!$that) {
            throw new ApiException('找不到机会');
        }
        if ($that->getSendTime()) {
            throw new ApiException('已发送，不要重复发送');
        }

        try {
            $this->prizeService->sendPrize($that);
        } catch (\Throwable $exception) {
            throw new ApiException("后台发奖失败：{$exception->getMessage()}", 0, [], $exception);
        }

        return [
            '__message' => '发奖成功',
        ];
    }
}
