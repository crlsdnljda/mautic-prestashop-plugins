<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\Cart;

class CartRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?Cart
    {
        return $this->em->getRepository(Cart::class)->find($id);
    }

    public function getCartById(int $cartId, int $shopId): ?array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('c.id, c.dateUpdPrestashop')
            ->from(Cart::class, 'c')
            ->where('c.cartId = :cartId')
            ->andWhere('c.shopId = :shopId')
            ->setParameter('cartId', $cartId)
            ->setParameter('shopId', $shopId);

        $result = $qb->getQuery()->getArrayResult();
        return !empty($result) ? $result[0] : null;
    }
}
