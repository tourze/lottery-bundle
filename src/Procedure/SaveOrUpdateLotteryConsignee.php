<?php

namespace LotteryBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Consignee;
use LotteryBundle\Repository\ChanceRepository;
use LotteryBundle\Repository\ConsigneeRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodTag(name: '抽奖模块')]
#[MethodDoc(summary: '保存抽奖地址')]
#[MethodExpose(method: 'SaveOrUpdateLotteryConsignee')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class SaveOrUpdateLotteryConsignee extends LockableProcedure
{
    #[MethodParam(description: '抽奖机会id')]
    public int $chanceId;

    #[MethodParam(description: '抽奖地址ID')]
    public int $consigneeId = 0;

    #[MethodParam(description: '姓名')]
    public string $realName;

    #[MethodParam(description: '手机号')]
    public string $mobile;

    #[MethodParam(description: '地址')]
    public string $address;

    public function __construct(
        private readonly ChanceRepository $chanceRepository,
        private readonly ConsigneeRepository $consigneeRepository,
        private readonly Security $security,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function execute(): array
    {
        $chance = $this->chanceRepository->findOneBy([
            'id' => $this->chanceId,
            'user' => $this->security->getUser(),
        ]);
        if ($chance === null) {
            throw new ApiException('抽奖信息错误');
        }

        $consignee = $this->consigneeRepository->find($this->consigneeId);
        if ($consignee === null) {
            $consignee = $this->consigneeRepository->findOneBy([
                'chance' => $chance,
            ]);
            if ($consignee === null) {
                $consignee = new Consignee();
            }
        }

        $consignee->setChance($chance);
        $consignee->setRealName($this->realName);
        $consignee->setMobile($this->mobile);
        $consignee->setAddress($this->address);
        $this->entityManager->persist($consignee);
        $this->entityManager->flush();

        return [
            '__message' => '保存成功',
        ];
    }
}
