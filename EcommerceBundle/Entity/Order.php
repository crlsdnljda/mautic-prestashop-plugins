<?php

namespace MauticPlugin\EcommerceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class Order
{
    private ?int $id = null;
    private ?int $orderId = null;
    private ?int $shopId = null;
    private ?int $cartId = null;
    private ?int $customerId = null;
    private ?string $language = null;
    private ?string $reference = null;
    private ?string $payment = null;
    private ?string $totalPaid = null;
    private ?string $totalPaidWithTax = null;
    private ?string $totalProducts = null;
    private ?string $totalProductsWithTax = null;
    private ?string $totalShipping = null;
    private ?string $totalShippingWithTax = null;
    private ?string $totalDiscounts = null;
    private ?string $totalDiscountsWithTax = null;
    private ?int $currentState = null;
    private ?\DateTimeInterface $orderDate = null;
    private ?\DateTimeInterface $dateAdded = null;
    private ?\DateTimeInterface $dateModified = null;
    private ?\DateTimeInterface $dateUpdPrestashop = null;
    private Collection $orderRows;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
        $this->orderRows = new ArrayCollection();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('ecommerce_orders')
            ->setCustomRepositoryClass(\MauticPlugin\EcommerceBundle\Repository\OrderRepository::class);

        $builder->addId();

        $builder->createField('orderId', Types::INTEGER)
            ->columnName('order_id')
            ->build();

        $builder->createField('shopId', Types::INTEGER)
            ->columnName('shop_id')
            ->build();

        $builder->createField('cartId', Types::INTEGER)
            ->columnName('cart_id')
            ->nullable()
            ->build();

        $builder->createField('customerId', Types::INTEGER)
            ->columnName('customer_id')
            ->nullable()
            ->build();

        $builder->createField('language', Types::STRING)
            ->columnName('language')
            ->length(10)
            ->nullable()
            ->build();

        $builder->createField('reference', Types::STRING)
            ->columnName('reference')
            ->length(50)
            ->nullable()
            ->build();

        $builder->createField('payment', Types::STRING)
            ->columnName('payment')
            ->length(100)
            ->nullable()
            ->build();

        $builder->createField('totalPaid', Types::DECIMAL)
            ->columnName('total_paid')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalPaidWithTax', Types::DECIMAL)
            ->columnName('total_paid_with_tax')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalProducts', Types::DECIMAL)
            ->columnName('total_products')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalProductsWithTax', Types::DECIMAL)
            ->columnName('total_products_with_tax')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalShipping', Types::DECIMAL)
            ->columnName('total_shipping')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalShippingWithTax', Types::DECIMAL)
            ->columnName('total_shipping_with_tax')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalDiscounts', Types::DECIMAL)
            ->columnName('total_discounts')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('totalDiscountsWithTax', Types::DECIMAL)
            ->columnName('total_discounts_with_tax')
            ->precision(10)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('currentState', Types::INTEGER)
            ->columnName('current_state')
            ->nullable()
            ->build();

        $builder->createField('orderDate', Types::DATETIME_MUTABLE)
            ->columnName('order_date')
            ->nullable()
            ->build();

        $builder->createField('dateUpdPrestashop', Types::DATETIME_MUTABLE)
            ->columnName('date_upd_prestashop')
            ->nullable()
            ->build();

        $builder->addDateAdded();
        $builder->addField('dateModified', Types::DATETIME_MUTABLE, ['columnName' => 'date_modified', 'nullable' => true]);

        $builder->createOneToMany('orderRows', OrderRow::class)
            ->mappedBy('order')
            ->build();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;
        return $this;
    }

    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    public function setCartId(?int $cartId): self
    {
        $this->cartId = $cartId;
        return $this;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(?string $payment): self
    {
        $this->payment = $payment;
        return $this;
    }

    public function getTotalPaid(): ?string
    {
        return $this->totalPaid;
    }

    public function setTotalPaid(string|float|null $totalPaid): self
    {
        $this->totalPaid = $totalPaid !== null ? (string) $totalPaid : null;
        return $this;
    }

    public function getTotalPaidWithTax(): ?string
    {
        return $this->totalPaidWithTax;
    }

    public function setTotalPaidWithTax(string|float|null $totalPaidWithTax): self
    {
        $this->totalPaidWithTax = $totalPaidWithTax !== null ? (string) $totalPaidWithTax : null;
        return $this;
    }

    public function getTotalProducts(): ?string
    {
        return $this->totalProducts;
    }

    public function setTotalProducts(string|float|null $totalProducts): self
    {
        $this->totalProducts = $totalProducts !== null ? (string) $totalProducts : null;
        return $this;
    }

    public function getTotalProductsWithTax(): ?string
    {
        return $this->totalProductsWithTax;
    }

    public function setTotalProductsWithTax(string|float|null $totalProductsWithTax): self
    {
        $this->totalProductsWithTax = $totalProductsWithTax !== null ? (string) $totalProductsWithTax : null;
        return $this;
    }

    public function getTotalShipping(): ?string
    {
        return $this->totalShipping;
    }

    public function setTotalShipping(string|float|null $totalShipping): self
    {
        $this->totalShipping = $totalShipping !== null ? (string) $totalShipping : null;
        return $this;
    }

    public function getTotalShippingWithTax(): ?string
    {
        return $this->totalShippingWithTax;
    }

    public function setTotalShippingWithTax(string|float|null $totalShippingWithTax): self
    {
        $this->totalShippingWithTax = $totalShippingWithTax !== null ? (string) $totalShippingWithTax : null;
        return $this;
    }

    public function getTotalDiscounts(): ?string
    {
        return $this->totalDiscounts;
    }

    public function setTotalDiscounts(string|float|null $totalDiscounts): self
    {
        $this->totalDiscounts = $totalDiscounts !== null ? (string) $totalDiscounts : null;
        return $this;
    }

    public function getTotalDiscountsWithTax(): ?string
    {
        return $this->totalDiscountsWithTax;
    }

    public function setTotalDiscountsWithTax(string|float|null $totalDiscountsWithTax): self
    {
        $this->totalDiscountsWithTax = $totalDiscountsWithTax !== null ? (string) $totalDiscountsWithTax : null;
        return $this;
    }

    public function getCurrentState(): ?int
    {
        return $this->currentState;
    }

    public function setCurrentState(?int $currentState): self
    {
        $this->currentState = $currentState;
        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(?\DateTimeInterface $orderDate): self
    {
        $this->orderDate = $orderDate;
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

    public function getDateModified(): ?\DateTimeInterface
    {
        return $this->dateModified;
    }

    public function setDateModified(?\DateTimeInterface $dateModified): self
    {
        $this->dateModified = $dateModified;
        return $this;
    }

    public function getDateUpdPrestashop(): ?\DateTimeInterface
    {
        return $this->dateUpdPrestashop;
    }

    public function setDateUpdPrestashop(?\DateTimeInterface $dateUpdPrestashop): self
    {
        $this->dateUpdPrestashop = $dateUpdPrestashop;
        return $this;
    }

    public function getOrderRows(): Collection
    {
        return $this->orderRows;
    }

    public function addOrderRow(OrderRow $orderRow): self
    {
        if (!$this->orderRows->contains($orderRow)) {
            $this->orderRows->add($orderRow);
            $orderRow->setOrder($this);
        }
        return $this;
    }

    public function removeOrderRow(OrderRow $orderRow): self
    {
        if ($this->orderRows->removeElement($orderRow)) {
            if ($orderRow->getOrder() === $this) {
                $orderRow->setOrder(null);
            }
        }
        return $this;
    }
}
