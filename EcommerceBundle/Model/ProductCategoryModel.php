<?php

namespace MauticPlugin\EcommerceBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\ProductCategory;
use MauticPlugin\EcommerceBundle\Repository\ProductCategoryRepository;

class ProductCategoryModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRepository(): ProductCategoryRepository
    {
        return $this->em->getRepository(ProductCategory::class);
    }

    public function getEntity(?int $id = null): ProductCategory
    {
        if ($id !== null) {
            return $this->getRepository()->find($id) ?? new ProductCategory();
        }

        return new ProductCategory();
    }

    public function saveEntity(ProductCategory $entity): void
    {
        $entity->setDateModified(new \DateTime());
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function deleteEntity(ProductCategory $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function getCategoryById(int $categoryId, int $shopId, string $language): ?array
    {
        return $this->getRepository()->getCategoryById($categoryId, $shopId, $language);
    }

    public function getCategoriesByShop(int $shopId, string $language): array
    {
        return $this->getRepository()->getCategoriesByShop($shopId, $language);
    }
}
