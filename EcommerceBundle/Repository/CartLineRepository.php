<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CartLineRepository extends EntityRepository
{
    public function deleteByCartId(int $cartId): void
    {
        $qb = $this->createQueryBuilder('cl');

        $qb->delete()
            ->where('cl.cart = :cartId')
            ->setParameter('cartId', $cartId)
            ->getQuery()
            ->execute();
    }
}
