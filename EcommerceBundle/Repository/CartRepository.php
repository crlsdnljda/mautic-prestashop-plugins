<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MauticPlugin\EcommerceBundle\Entity\Cart;

/**
 * @extends ServiceEntityRepository<Cart>
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    public function getCartById(int $cartId, int $shopId): ?array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->select('c.id, c.dateUpdPrestashop')
            ->where('c.cartId = :cartId')
            ->andWhere('c.shopId = :shopId')
            ->setParameter('cartId', $cartId)
            ->setParameter('shopId', $shopId);

        $result = $qb->getQuery()->getArrayResult();
        return !empty($result) ? $result[0] : null;
    }
}
