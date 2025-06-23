<?php

namespace LotteryBundle\Controller\H5;

use LotteryBundle\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LotteryRulesController extends AbstractController
{
    public function __construct(
        private readonly ActivityRepository $activityRepository,
    ) {
    }

    #[Route('/h5/lottery/rules', name: 'h5_lottery_rules', methods: ['GET'])]
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

        return $this->render('@Lottery/h5/rules.html.twig', [
            'activity' => $activity,
        ]);
    }
}