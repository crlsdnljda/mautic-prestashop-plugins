<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MauticPlugin\EcommerceBundle\Entity\Order;

class OrderRepository extends EntityRepository
{
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

    public function getOrdersByCustomerId(int $customerId): array
    {
        $qb = $this->createQueryBuilder('o');

        $qb->where('o.customerId = :customerId')
            ->setParameter('customerId', $customerId)
            ->orderBy('o.orderDate', 'DESC');

        return $qb->getQuery()->getResult();
    }
}
