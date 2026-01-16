<?php

namespace MauticPlugin\EcommerceBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\ProductCategory;

class ProductCategoryRepository
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function find(int $id): ?ProductCategory
    {
        return $this->em->getRepository(ProductCategory::class)->find($id);
    }

    public function getCategoryById(int $categoryId, int $shopId, string $language): ?array
    {
        $qb = $this->em->createQueryBuilder();

        $qb->select('pc.id')
            ->from(ProductCategory::class, 'pc')
            ->where('pc.categoryId = :categoryId')
            ->andWhere('pc.shopId = :shopId')
            ->andWhere('pc.language = :language')
            ->setParameter('categoryId', $categoryId)
            ->setParameter('shopId', $shopId)
            ->setParameter('language', $language);

        $result = $qb->getQuery()->getArrayResult();
        return !empty($result) ? $result[0] : null;
    }
}
