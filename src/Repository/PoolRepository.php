<?php

namespace LotteryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use LotteryBundle\Entity\Pool;

/**
 * @method Pool|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pool|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pool[]    findAll()
 * @method Pool[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PoolRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pool::class);
    }
}
