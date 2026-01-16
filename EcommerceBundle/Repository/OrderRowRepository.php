<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityRepository;

class OrderRowRepository extends EntityRepository
{
    public function deleteByOrderId(int $orderId): void
    {
        $qb = $this->createQueryBuilder('or');

        $qb->delete()
            ->where('or.order = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->execute();
    }
}
