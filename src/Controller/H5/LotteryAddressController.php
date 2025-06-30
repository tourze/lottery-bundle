<?php

namespace LotteryBundle\Controller\H5;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LotteryAddressController extends AbstractController
{
    #[Route(path: '/h5/lottery/address', name: 'h5_lottery_address', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $chanceId = $request->query->get('chance_id');
        
        if (!$chanceId) {
            throw $this->createNotFoundException('抽奖记录ID不能为空');
        }

        return $this->render('@Lottery/h5/address.html.twig', [
            'chance_id' => $chanceId,
        ]);
    }
}