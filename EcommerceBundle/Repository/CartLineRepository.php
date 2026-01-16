<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\CartLine;

class CartLineRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?CartLine
    {
        return $this->em->getRepository(CartLine::class)->find($id);
    }
}
