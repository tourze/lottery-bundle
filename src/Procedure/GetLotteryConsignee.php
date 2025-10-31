<?php

namespace LotteryBundle\Procedure;

use LotteryBundle\Entity\Chance;
use LotteryBundle\Entity\Consignee;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\DoctrineHelper\CacheHelper;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Model\JsonRpcRequest;
use Tourze\JsonRPCCacheBundle\Procedure\CacheableProcedure;

#[MethodTag(name: '抽奖模块')]
#[MethodDoc(summary: '获取中奖地收获址详情')]
#[MethodExpose(method: 'GetLotteryConsignee')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetLotteryConsignee extends CacheableProcedure
{
    #[MethodParam(description: '活动ID')]
    public int $chanceId;

    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly ChanceRepository $chanceRepository,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $chance = $this->chanceRepository->findOneBy([
            'id' => $this->chanceId,
            'user' => $this->security->getUser(),
        ]);
        if (null === $chance) {
            throw new ApiException('抽奖信息错误');
        }

        $result = $this->normalizer->normalize($chance->getConsignee(), 'array', ['groups' => 'restful_read']);

        /** @var array<string, mixed> */
        return is_array($result) ? $result : [];
    }

    public function getCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        if (null === $params) {
            $key = $this::class . '-no-params';
        } else {
            $key = $this->buildParamCacheKey($params);
        }

        if (null !== $this->security->getUser()) {
            $key .= '-' . $this->security->getUser()->getUserIdentifier();
        }

        return $key;
    }

    public function getCacheDuration(JsonRpcRequest $request): int
    {
        return 60 * 60;
    }

    public function getCacheTags(JsonRpcRequest $request): iterable
    {
        yield CacheHelper::getClassTags(Consignee::class);
    }
}
