<?php

namespace MauticPlugin\EcommerceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class Cart
{
    private ?int $id = null;
    private ?int $cartId = null;
    private ?int $shopId = null;
    private ?int $customerId = null;
    private ?int $guestId = null;
    private ?string $language = null;
    private ?string $totalPrice = null;
    private ?string $totalPriceWithTax = null;
    private ?\DateTimeInterface $cartDate = null;
    private ?\DateTimeInterface $dateAdded = null;
    private ?\DateTimeInterface $dateModified = null;
    private ?\DateTimeInterface $dateUpdPrestashop = null;
    private Collection $cartLines;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
        $this->cartLines = new ArrayCollection();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('ecommerce_carts')
            ->setCustomRepositoryClass(\MauticPlugin\EcommerceBundle\Repository\CartRepository::class);

        $builder->addId();

        $builder->createField('cartId', Types::INTEGER)
            ->columnName('cart_id')
            ->build();

        $builder->createField('shopId', Types::INTEGER)
            ->columnName('shop_id')
            ->build();

        $builder->createField('customerId', Types::INTEGER)
            ->columnName('customer_id')
            ->nullable()
            ->build();

        $builder->createField('guestId', Types::INTEGER)
            ->columnName('guest_id')
            ->nullable()
            ->build();

        $builder->createField('language', Types::STRING)
            ->columnName('language')
            ->length(10)
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

        $builder->createField('cartDate', Types::DATETIME_MUTABLE)
            ->columnName('cart_date')
            ->nullable()
            ->build();

        $builder->createField('dateUpdPrestashop', Types::DATETIME_MUTABLE)
            ->columnName('date_upd_prestashop')
            ->nullable()
            ->build();

        $builder->addDateAdded();
        $builder->addField('dateModified', Types::DATETIME_MUTABLE, ['columnName' => 'date_modified', 'nullable' => true]);

        $builder->createOneToMany('cartLines', CartLine::class)
            ->mappedBy('cart')
            ->build();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCartId(): ?int
    {
        return $this->cartId;
    }

    public function setCartId(int $cartId): self
    {
        $this->cartId = $cartId;
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

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(?int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    public function getGuestId(): ?int
    {
        return $this->guestId;
    }

    public function setGuestId(?int $guestId): self
    {
        $this->guestId = $guestId;
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

    public function getCartDate(): ?\DateTimeInterface
    {
        return $this->cartDate;
    }

    public function setCartDate(?\DateTimeInterface $cartDate): self
    {
        $this->cartDate = $cartDate;
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

    public function getCartLines(): Collection
    {
        return $this->cartLines;
    }

    public function addCartLine(CartLine $cartLine): self
    {
        if (!$this->cartLines->contains($cartLine)) {
            $this->cartLines->add($cartLine);
            $cartLine->setCart($this);
        }
        return $this;
    }

    public function removeCartLine(CartLine $cartLine): self
    {
        if ($this->cartLines->removeElement($cartLine)) {
            if ($cartLine->getCart() === $this) {
                $cartLine->setCart(null);
            }
        }
        return $this;
    }
}
