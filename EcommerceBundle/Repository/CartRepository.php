<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MauticPlugin\EcommerceBundle\Entity\Cart;

class CartRepository extends EntityRepository
{
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

    public function getCartsByCustomerId(int $customerId): array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->where('c.customerId = :customerId')
            ->setParameter('customerId', $customerId)
            ->orderBy('c.cartDate', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
