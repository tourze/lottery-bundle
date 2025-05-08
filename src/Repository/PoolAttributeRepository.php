<?php

namespace LotteryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LotteryBundle\Entity\PoolAttribute;

/**
 * @method PoolAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method PoolAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method PoolAttribute[]    findAll()
 * @method PoolAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PoolAttributeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PoolAttribute::class);
    }
}
