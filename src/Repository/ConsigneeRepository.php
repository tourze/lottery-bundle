<?php

namespace LotteryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LotteryBundle\Entity\Consignee;

/**
 * @method Consignee|null find($id, $lockMode = null, $lockVersion = null)
 * @method Consignee|null findOneBy(array $criteria, array $orderBy = null)
 * @method Consignee[]    findAll()
 * @method Consignee[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsigneeRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consignee::class);
    }
}
