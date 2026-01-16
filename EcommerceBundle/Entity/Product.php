<?php

namespace MauticPlugin\EcommerceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class Product
{
    private ?int $id = null;
    private ?string $name = null;
    private ?string $shortDescription = null;
    private ?string $longDescription = null;
    private ?int $productId = null;
    private ?int $shopId = null;
    private ?int $productAttributeId = null;
    private ?string $price = null;
    private ?string $language = null;
    private ?string $reference = null;
    private ?string $taxPercent = null;
    private ?string $url = null;
    private ?string $imageUrl = null;
    private ?\DateTimeInterface $dateAdded = null;
    private ?\DateTimeInterface $dateModified = null;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('ecommerce_products')
            ->setCustomRepositoryClass(\MauticPlugin\EcommerceBundle\Repository\ProductRepository::class);

        $builder->addId();

        $builder->createField('name', Types::STRING)
            ->columnName('name')
            ->length(255)
            ->build();

        $builder->createField('shortDescription', Types::TEXT)
            ->columnName('short_description')
            ->nullable()
            ->build();

        $builder->createField('longDescription', Types::TEXT)
            ->columnName('long_description')
            ->nullable()
            ->build();

        $builder->createField('productId', Types::INTEGER)
            ->columnName('product_id')
            ->build();

        $builder->createField('shopId', Types::INTEGER)
            ->columnName('shop_id')
            ->build();

        $builder->createField('productAttributeId', Types::INTEGER)
            ->columnName('product_attribute_id')
            ->nullable()
            ->build();

        $builder->createField('price', Types::DECIMAL)
            ->columnName('price')
            ->precision(10)
            ->scale(2)
            ->build();

        $builder->createField('language', Types::STRING)
            ->columnName('language')
            ->length(10)
            ->build();

        $builder->createField('reference', Types::STRING)
            ->columnName('reference')
            ->length(100)
            ->nullable()
            ->build();

        $builder->createField('taxPercent', Types::DECIMAL)
            ->columnName('tax_percent')
            ->precision(5)
            ->scale(2)
            ->nullable()
            ->build();

        $builder->createField('url', Types::STRING)
            ->columnName('url')
            ->length(500)
            ->nullable()
            ->build();

        $builder->createField('imageUrl', Types::STRING)
            ->columnName('image_url')
            ->length(255)
            ->nullable()
            ->build();

        $builder->addDateAdded();
        $builder->addField('dateModified', Types::DATETIME_MUTABLE, ['columnName' => 'date_modified', 'nullable' => true]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(?string $longDescription): self
    {
        $this->longDescription = $longDescription;
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

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string|float $price): self
    {
        $this->price = (string) $price;
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
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

    public function getTaxPercent(): ?string
    {
        return $this->taxPercent;
    }

    public function setTaxPercent(string|float|null $taxPercent): self
    {
        $this->taxPercent = $taxPercent !== null ? (string) $taxPercent : null;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
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
}
