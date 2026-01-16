<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use MauticPlugin\EcommerceBundle\Entity\Product;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getProductById(int $productId, int $shopId, int $productAttributeId, string $language): array
    {
        $qb = $this->createQueryBuilder('p');

        $qb->select('p.id')
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

    public function getProducts(array $args = []): array
    {
        $qb = $this->createQueryBuilder('p');

        if (isset($args['shopId'])) {
            $qb->andWhere('p.shopId = :shopId')
               ->setParameter('shopId', $args['shopId']);
        }

        if (isset($args['language'])) {
            $qb->andWhere('p.language = :language')
               ->setParameter('language', $args['language']);
        }

        return $qb->getQuery()->getResult();
    }
}
