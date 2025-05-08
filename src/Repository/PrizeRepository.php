<?php

namespace LotteryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LotteryBundle\Entity\Prize;

/**
 * @method Prize|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prize|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prize[]    findAll()
 * @method Prize[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrizeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prize::class);
    }
}
