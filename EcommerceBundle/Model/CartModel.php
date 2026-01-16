<?php

namespace MauticPlugin\EcommerceBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\Cart;
use MauticPlugin\EcommerceBundle\Entity\CartLine;
use MauticPlugin\EcommerceBundle\Repository\CartRepository;

class CartModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRepository(): CartRepository
    {
        return $this->em->getRepository(Cart::class);
    }

    public function getEntity(?int $id = null): Cart
    {
        if ($id !== null) {
            return $this->getRepository()->find($id) ?? new Cart();
        }

        return new Cart();
    }

    public function saveEntity(Cart $entity): void
    {
        $entity->setDateModified(new \DateTime());
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function deleteEntity(Cart $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function getCartById(int $cartId, int $shopId): ?array
    {
        return $this->getRepository()->getCartById($cartId, $shopId);
    }

    public function createCartLine(): CartLine
    {
        return new CartLine();
    }

    public function saveCartLine(CartLine $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function deleteCartLinesByCart(Cart $cart): void
    {
        foreach ($cart->getCartLines() as $line) {
            $this->em->remove($line);
        }
        $this->em->flush();
    }
}
