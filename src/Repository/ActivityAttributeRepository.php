<?php

namespace LotteryBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DoctrineEnhanceBundle\Repository\CommonRepositoryAware;
use LotteryBundle\Entity\ActivityAttribute;

/**
 * @method ActivityAttribute|null find($id, $lockMode = null, $lockVersion = null)
 * @method ActivityAttribute|null findOneBy(array $criteria, array $orderBy = null)
 * @method ActivityAttribute[]    findAll()
 * @method ActivityAttribute[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityAttributeRepository extends ServiceEntityRepository
{
    use CommonRepositoryAware;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityAttribute::class);
    }
}
