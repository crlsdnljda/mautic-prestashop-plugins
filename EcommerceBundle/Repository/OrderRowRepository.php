<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MauticPlugin\EcommerceBundle\Entity\OrderRow;

/**
 * @extends ServiceEntityRepository<OrderRow>
 */
class OrderRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderRow::class);
    }
}
