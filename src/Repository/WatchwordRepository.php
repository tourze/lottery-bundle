<?php

namespace LotteryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use LotteryBundle\Entity\Watchword;

/**
 * @method Watchword|null find($id, $lockMode = null, $lockVersion = null)
 * @method Watchword|null findOneBy(array $criteria, array $orderBy = null)
 * @method Watchword[]    findAll()
 * @method Watchword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WatchwordRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Watchword::class);
    }
}
