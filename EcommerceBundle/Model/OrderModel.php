<?php

namespace MauticPlugin\EcommerceBundle\Model;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\EcommerceBundle\Entity\Order;
use MauticPlugin\EcommerceBundle\Entity\OrderRow;
use MauticPlugin\EcommerceBundle\Repository\OrderRepository;

class OrderModel
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRepository(): OrderRepository
    {
        return $this->em->getRepository(Order::class);
    }

    public function getEntity(?int $id = null): Order
    {
        if ($id !== null) {
            return $this->getRepository()->find($id) ?? new Order();
        }

        return new Order();
    }

    public function saveEntity(Order $entity): void
    {
        $entity->setDateModified(new \DateTime());
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function deleteEntity(Order $entity): void
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function getOrderById(int $orderId, int $shopId): ?array
    {
        return $this->getRepository()->getOrderById($orderId, $shopId);
    }

    public function createOrderRow(): OrderRow
    {
        return new OrderRow();
    }

    public function saveOrderRow(OrderRow $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function deleteOrderRowsByOrder(Order $order): void
    {
        foreach ($order->getOrderRows() as $row) {
            $this->em->remove($row);
        }
        $this->em->flush();
    }
}
