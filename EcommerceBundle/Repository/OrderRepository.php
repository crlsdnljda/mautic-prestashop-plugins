<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\Order;

class OrderRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?Order
    {
        return $this->em->getRepository(Order::class)->find($id);
    }

    public function getOrderById(int $orderId, int $shopId): ?array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('o.id, o.dateUpdPrestashop')
            ->from(Order::class, 'o')
            ->where('o.orderId = :orderId')
            ->andWhere('o.shopId = :shopId')
            ->setParameter('orderId', $orderId)
            ->setParameter('shopId', $shopId);

        $result = $qb->getQuery()->getArrayResult();
        return !empty($result) ? $result[0] : null;
    }
}
