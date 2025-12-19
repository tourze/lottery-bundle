<?php

namespace LotteryBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Consignee;
use LotteryBundle\Param\SaveOrUpdateLotteryConsigneeParam;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Repository\ConsigneeRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use Tourze\JsonRPC\Core\Result\ArrayResult;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '抽奖模块')]
#[MethodDoc(summary: '保存抽奖地址')]
#[MethodExpose(method: 'SaveOrUpdateLotteryConsignee')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class SaveOrUpdateLotteryConsignee extends LockableProcedure
{
    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly ConsigneeRepository $consigneeRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @phpstan-param SaveOrUpdateLotteryConsigneeParam $param
     */
    public function execute(SaveOrUpdateLotteryConsigneeParam|RpcParamInterface $param): ArrayResult
    {
        $chance = $this->chanceRepository->findOneBy([
            'id' => $param->chanceId,
            'user' => $this->security->getUser(),
        ]);
        if (null === $chance) {
            throw new ApiException('抽奖信息错误');
        }

        $consignee = $this->consigneeRepository->find($param->consigneeId);
        if (null === $consignee) {
            $consignee = $this->consigneeRepository->findOneBy([
                'chance' => $chance,
            ]);
            if (null === $consignee) {
                $consignee = new Consignee();
            }
        }

        $consignee->setChance($chance);
        $consignee->setRealName($param->realName);
        $consignee->setMobile($param->mobile);
        $consignee->setAddress($param->address);
        $this->entityManager->persist($consignee);
        $this->entityManager->flush();

        return new ArrayResult([
            '__message' => '保存成功',
        ]);
    }
}
