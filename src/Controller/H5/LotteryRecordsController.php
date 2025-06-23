<?php

namespace LotteryBundle\Controller\H5;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LotteryRecordsController extends AbstractController
{
    #[Route('/h5/lottery/records', name: 'h5_lottery_records', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $activityId = $request->query->get('activity_id');
        
        return $this->render('@Lottery/h5/records.html.twig', [
            'activity_id' => $activityId,
        ]);
    }
}