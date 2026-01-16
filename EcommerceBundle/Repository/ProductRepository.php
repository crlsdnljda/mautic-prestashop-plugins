<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\Product;

class ProductRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?Product
    {
        return $this->em->getRepository(Product::class)->find($id);
    }

    public function getProductById(int $productId, int $shopId, int $productAttributeId, string $language): array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('p.id')
            ->from(Product::class, 'p')
            ->where('p.productId = :productId')
            ->andWhere('p.shopId = :shopId')
            ->andWhere('p.productAttributeId = :productAttributeId')
            ->andWhere('p.language = :language')
            ->setParameter('productId', $productId)
            ->setParameter('shopId', $shopId)
            ->setParameter('productAttributeId', $productAttributeId)
            ->setParameter('language', $language);

        return $qb->getQuery()->getArrayResult();
    }
}
