<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MauticPlugin\EcommerceBundle\Entity\Product;

class ProductRepository extends EntityRepository
{
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

        if (!empty($args['filter'])) {
            $qb->andWhere('p.name LIKE :filter')
                ->setParameter('filter', '%' . $args['filter'] . '%');
        }

        if (!empty($args['orderBy'])) {
            $qb->orderBy('p.' . $args['orderBy'], $args['orderByDir'] ?? 'ASC');
        }

        return $qb->getQuery()->getResult();
    }
}
