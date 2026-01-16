<?php

namespace MauticPlugin\EcommerceBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class ProductCategory
{
    private ?int $id = null;
    private ?int $categoryId = null;
    private ?int $shopId = null;
    private ?int $parentId = null;
    private ?string $name = null;
    private ?string $language = null;
    private ?int $levelDepth = null;
    private ?bool $isRoot = false;
    private ?\DateTimeInterface $dateAdded = null;
    private ?\DateTimeInterface $dateModified = null;

    public function __construct()
    {
        $this->dateAdded = new \DateTime();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('ecommerce_product_categories')
            ->setCustomRepositoryClass(\MauticPlugin\EcommerceBundle\Repository\ProductCategoryRepository::class);

        $builder->addId();

        $builder->createField('categoryId', Types::INTEGER)
            ->columnName('category_id')
            ->build();

        $builder->createField('shopId', Types::INTEGER)
            ->columnName('shop_id')
            ->build();

        $builder->createField('parentId', Types::INTEGER)
            ->columnName('parent_id')
            ->nullable()
            ->build();

        $builder->createField('name', Types::STRING)
            ->columnName('name')
            ->length(255)
            ->build();

        $builder->createField('language', Types::STRING)
            ->columnName('language')
            ->length(10)
            ->build();

        $builder->createField('levelDepth', Types::INTEGER)
            ->columnName('level_depth')
            ->nullable()
            ->build();

        $builder->createField('isRoot', Types::BOOLEAN)
            ->columnName('is_root')
            ->nullable()
            ->build();

        $builder->addDateAdded();
        $builder->addField('dateModified', Types::DATETIME_MUTABLE, ['columnName' => 'date_modified', 'nullable' => true]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): self
    {
        $this->categoryId = $categoryId;
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

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function setParentId(?int $parentId): self
    {
        $this->parentId = $parentId;
        return $this;
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

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getLevelDepth(): ?int
    {
        return $this->levelDepth;
    }

    public function setLevelDepth(?int $levelDepth): self
    {
        $this->levelDepth = $levelDepth;
        return $this;
    }

    public function getIsRoot(): ?bool
    {
        return $this->isRoot;
    }

    public function setIsRoot(?bool $isRoot): self
    {
        $this->isRoot = $isRoot;
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
