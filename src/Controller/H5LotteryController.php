<?php

namespace LotteryBundle\Controller;

use LotteryBundle\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * H5抽奖活动控制器
 */
#[Route('/h5/lottery', name: 'h5_lottery_')]
class H5LotteryController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
    ) {
    }

    /**
     * 抽奖活动主页面
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $activityId = $request->query->get('activity_id');
        
        if (!$activityId) {
            throw $this->createNotFoundException('活动ID不能为空');
        }

        // 获取活动信息
        $activity = $this->activityRepository->find($activityId);
        if ($activity === null) {
            throw $this->createNotFoundException('抽奖活动不存在');
        }

        // 检查活动是否有效
        if (!$activity->isValid()) {
            throw $this->createNotFoundException('抽奖活动已失效');
        }

        // 检查活动时间
        $now = new \DateTime();
        if ($activity->getStartTime() > $now) {
            return $this->render('@Lottery/h5/not_started.html.twig', [
                'activity' => $activity,
                'start_time' => $activity->getStartTime(),
            ]);
        }

        if ($activity->getEndTime() < $now) {
            return $this->render('@Lottery/h5/ended.html.twig', [
                'activity' => $activity,
                'end_time' => $activity->getEndTime(),
            ]);
        }

        // 渲染抽奖页面
        return $this->render('@Lottery/h5/lottery.html.twig', [
            'activity' => $activity,
        ]);
    }

    /**
     * 收货地址填写页面
     */
    #[Route('/address', name: 'address', methods: ['GET'])]
    public function address(Request $request): Response
    {
        $chanceId = $request->query->get('chance_id');
        
        if (!$chanceId) {
            throw $this->createNotFoundException('抽奖记录ID不能为空');
        }

        // 这里可以添加验证chance是否属于当前用户的逻辑
        
        return $this->render('@Lottery/h5/address.html.twig', [
            'chance_id' => $chanceId,
        ]);
    }

    /**
     * 中奖记录页面
     */
    #[Route('/records', name: 'records', methods: ['GET'])]
    public function records(Request $request): Response
    {
        $activityId = $request->query->get('activity_id');
        
        return $this->render('@Lottery/h5/records.html.twig', [
            'activity_id' => $activityId,
        ]);
    }

    /**
     * 活动规则页面
     */
    #[Route('/rules', name: 'rules', methods: ['GET'])]
    public function rules(Request $request): Response
    {
        $activityId = $request->query->get('activity_id');
        
        if (!$activityId) {
            throw $this->createNotFoundException('活动ID不能为空');
        }

        $activity = $this->activityRepository->find($activityId);
        if ($activity === null) {
            throw $this->createNotFoundException('抽奖活动不存在');
        }

        return $this->render('@Lottery/h5/rules.html.twig', [
            'activity' => $activity,
        ]);
    }
}
