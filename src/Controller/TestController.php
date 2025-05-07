<?php

namespace LotteryBundle\Controller;

use AppBundle\Entity\BizUser;
use AppBundle\Repository\BizUserRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use LotteryBundle\Entity\Chance;
use LotteryBundle\Repository\ActivityRepository;
use LotteryBundle\Repository\ChanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/lottery/chance/{id}/{limit}', name: 'test-lottery-gen-chance')]
    public function genChance(
        int $id,
        int $limit,
        BizUserRepository $userRepository,
        ActivityRepository $activityRepository,
        ChanceRepository $chanceRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $activity = $activityRepository->findOneBy(['id' => $id]);
        $qb = $userRepository
            ->createQueryBuilder('a')
            ->where('a.valid = true')
            ->setMaxResults($limit);

        foreach ($qb->getQuery()->getResult() as $item) {
            /** @var BizUser $item */
            $chance = new Chance();
            $chance->setValid(true);
            $chance->setUser($item);
            $chance->setActivity($activity);
            $chance->setStartTime(Carbon::now());
            $chance->setExpireTime(Carbon::now()->add('days', 2));

            $entityManager->persist($chance);
            $entityManager->flush();
        }

        return $this->json(['limit' => $limit]);
    }
}
