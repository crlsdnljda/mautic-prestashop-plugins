<?php

namespace MauticPlugin\EcommerceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class CartLine
{
    private ?int $id = null;
    private ?Cart $cart = null;
    private ?int $productId = null;
    private ?int $productAttributeId = null;
    private ?int $quantity = null;
    private ?string $price = null;
    private ?\DateTimeInterface $dateAdded = null;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('ecommerce_cart_lines')
            ->setCustomRepositoryClass(\MauticPlugin\EcommerceBundle\Repository\CartLineRepository::class);

        $builder->addId();

        $builder->createManyToOne('cart', Cart::class)
            ->inversedBy('cartLines')
            ->addJoinColumn('cart_id', 'id', true, false, 'CASCADE')
            ->build();

        $builder->createField('productId', Types::INTEGER)
            ->columnName('product_id')
            ->build();

        $builder->createField('productAttributeId', Types::INTEGER)
            ->columnName('product_attribute_id')
            ->nullable()
            ->build();

        $builder->createField('quantity', Types::INTEGER)
            ->columnName('quantity')
            ->build();

        $builder->createField('price', Types::DECIMAL)
            ->columnName('price')
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

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        $this->cart = $cart;
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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string|float|null $price): self
    {
        $this->price = $price !== null ? (string) $price : null;
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
