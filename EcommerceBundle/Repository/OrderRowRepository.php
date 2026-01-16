<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\OrderRow;

class OrderRowRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?OrderRow
    {
        return $this->em->getRepository(OrderRow::class)->find($id);
    }
}
