<?php

namespace MauticPlugin\EcommerceBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\Product;
use MauticPlugin\EcommerceBundle\Repository\ProductRepository;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class ProductModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRepository(): ProductRepository
    {
        return $this->em->getRepository(Product::class);
    }

    public function getEntity(?int $id = null): Product
    {
        if ($id !== null) {
            return $this->getRepository()->find($id) ?? new Product();
        }

        return new Product();
    }

    public function saveEntity(Product $entity): void
    {
        $entity->setDateModified(new \DateTime());
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function deleteEntity(Product $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function getProductById(int $productId, int $shopId, int $productAttributeId, string $language): array
    {
        return $this->getRepository()->getProductById($productId, $shopId, $productAttributeId, $language);
    }

    public function getProducts(array $args = []): array
    {
        return $this->getRepository()->getProducts($args);
    }
}
