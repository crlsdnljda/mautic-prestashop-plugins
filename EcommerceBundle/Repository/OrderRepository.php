<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MauticPlugin\EcommerceBundle\Entity\Order;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function getOrderById(int $orderId, int $shopId): ?array
    {
        $qb = $this->createQueryBuilder('o');

        $qb->select('o.id, o.dateUpdPrestashop')
            ->where('o.orderId = :orderId')
            ->andWhere('o.shopId = :shopId')
            ->setParameter('orderId', $orderId)
            ->setParameter('shopId', $shopId);

        $result = $qb->getQuery()->getArrayResult();
        return !empty($result) ? $result[0] : null;
    }
}
