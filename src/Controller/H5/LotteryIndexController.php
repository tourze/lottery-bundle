<?php

namespace LotteryBundle\Controller\H5;

use LotteryBundle\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LotteryIndexController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
    ) {
    }

    #[Route(path: '/h5/lottery', name: 'h5_lottery_index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $activityId = $request->query->get('activity_id');
        
        if (!$activityId) {
            throw $this->createNotFoundException('活动ID不能为空');
        }

        $activity = $this->activityRepository->find($activityId);
        if ($activity === null) {
            throw $this->createNotFoundException('抽奖活动不存在');
        }

        if (!$activity->isValid()) {
            throw $this->createNotFoundException('抽奖活动已失效');
        }

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

        return $this->render('@Lottery/h5/lottery.html.twig', [
            'activity' => $activity,
        ]);
    }
}