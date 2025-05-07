<?php

namespace LotteryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use LotteryBundle\Entity\Chance;

/**
 * @method Chance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chance[]    findAll()
 * @method Chance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChanceRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chance::class);
    }
}
