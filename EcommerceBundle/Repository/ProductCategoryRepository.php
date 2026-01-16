<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityRepository;
use MauticPlugin\EcommerceBundle\Entity\ProductCategory;

class ProductCategoryRepository extends EntityRepository
{
    public function getCategoryById(int $categoryId, int $shopId, string $language): ?array
    {
        $qb = $this->createQueryBuilder('pc');

        $qb->select('pc.id')
            ->where('pc.categoryId = :categoryId')
            ->andWhere('pc.shopId = :shopId')
            ->andWhere('pc.language = :language')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('shopId', $shopId)
            ->setParameter('language', $language);

        $result = $qb->getQuery()->getArrayResult();
        return !empty($result) ? $result[0] : null;
    }

    public function getCategoriesByShop(int $shopId, string $language): array
    {
        $qb = $this->createQueryBuilder('pc');

        $qb->where('pc.shopId = :shopId')
            ->andWhere('pc.language = :language')
            ->setParameter('shopId', $shopId)
            ->setParameter('language', $language)
            ->orderBy('pc.levelDepth', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
