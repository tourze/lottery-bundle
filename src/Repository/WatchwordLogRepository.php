<?php

namespace LotteryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LotteryBundle\Entity\WatchwordLog;

/**
 * @method WatchwordLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method WatchwordLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method WatchwordLog[]    findAll()
 * @method WatchwordLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WatchwordLogRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WatchwordLog::class);
    }
}
