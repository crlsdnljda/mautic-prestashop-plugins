<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MauticPlugin\EcommerceBundle\Entity\CartLine;

/**
 * @extends ServiceEntityRepository<CartLine>
 */
class CartLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartLine::class);
    }
}
