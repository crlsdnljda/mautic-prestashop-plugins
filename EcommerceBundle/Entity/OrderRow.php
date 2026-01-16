<?php

namespace MauticPlugin\EcommerceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class OrderRow
{
    private ?int $id = null;
    private ?Order $order = null;
    private ?int $productId = null;
    private ?int $productAttributeId = null;
    private ?string $productName = null;
    private ?int $quantity = null;
    private ?string $unitPrice = null;
    private ?string $unitPriceWithTax = null;
    private ?string $totalPrice = null;
    private ?string $totalPriceWithTax = null;
    private ?\DateTimeInterface $dateAdded = null;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('ecommerce_order_rows')
            ->setCustomRepositoryClass(\MauticPlugin\EcommerceBundle\Repository\OrderRowRepository::class);

        $builder->addId();

        $builder->createManyToOne('order', Order::class)
            ->inversedBy('orderRows')
            ->addJoinColumn('order_id', 'id', true, false, 'CASCADE')
            ->build();

        $builder->createField('productId', Types::INTEGER)
            ->columnName('product_id')
            ->build();

        $builder->createField('productAttributeId', Types::INTEGER)
            ->columnName('product_attribute_id')
            ->nullable()
            ->build();

        $builder->createField('productName', Types::STRING)
            ->columnName('product_name')
            ->length(255)
            ->nullable()
            ->build();

        $builder->createField('quantity', Types::INTEGER)
            ->columnName('quantity')
            ->build();

        $builder->createField('unitPrice', Types::DECIMAL)
            ->columnName('unit_price')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('unitPriceWithTax', Types::DECIMAL)
            ->columnName('unit_price_with_tax')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalPrice', Types::DECIMAL)
            ->columnName('total_price')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalPriceWithTax', Types::DECIMAL)
            ->columnName('total_price_with_tax')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->addDateAdded();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): self
    {
        $this->productId = $productId;
        return $this;
    }

    public function getProductAttributeId(): ?int
    {
        return $this->productAttributeId;
    }

    public function setProductAttributeId(?int $productAttributeId): self
    {
        $this->productAttributeId = $productAttributeId;
        return $this;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(?string $productName): self
    {
        $this->productName = $productName;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string|float|null $unitPrice): self
    {
        $this->unitPrice = $unitPrice !== null ? (string) $unitPrice : null;
        return $this;
    }

    public function getUnitPriceWithTax(): ?string
    {
        return $this->unitPriceWithTax;
    }

    public function setUnitPriceWithTax(string|float|null $unitPriceWithTax): self
    {
        $this->unitPriceWithTax = $unitPriceWithTax !== null ? (string) $unitPriceWithTax : null;
        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(string|float|null $totalPrice): self
    {
        $this->totalPrice = $totalPrice !== null ? (string) $totalPrice : null;
        return $this;
    }

    public function getTotalPriceWithTax(): ?string
    {
        return $this->totalPriceWithTax;
    }

    public function setTotalPriceWithTax(string|float|null $totalPriceWithTax): self
    {
        $this->totalPriceWithTax = $totalPriceWithTax !== null ? (string) $totalPriceWithTax : null;
        return $this;
    }

    public function getDateAdded(): ?\DateTimeInterface
    {
        return $this->dateAdded;
    }

    public function setDateAdded(\DateTimeInterface $dateAdded): self
    {
        $this->dateAdded = $dateAdded;
        return $this;
    }
}
