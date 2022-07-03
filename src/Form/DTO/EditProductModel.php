<?php

namespace App\Form\DTO;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class EditProductModel
{
    /**
     *
     * @var int
     */
    public $id;
    
    /**
     * @Assert\NotBlank(message="Please enter a title")
     * @var string
     */
    public $title;

    /**
     * @Assert\NotBlank(message="Please enter a price")
     * @Assert\PositiveOrZero
     * @Assert\GreaterThanOrEqual(value="0")
     * @var float
     */
    public $price;

    /**
     * @Assert\File(
     * maxSize="5024k", 
     * mimeTypes={"image/jpg", "image/jpeg", "image/png", "image/webp"},
     * mimeTypesMessage="Please upload a valid image"
     * )
     * @var UploadedFile|null
     */
    public $newImage;

    /**
     * @Assert\NotBlank(message="Please enter a quantity")
     * @var int
     */
    public $quantity;

    /**
     *
     * @var string
     * 
     */
    public $description;

    /**
     * @Assert\NotBlank(message="Please select a category")
     * @var Category
     */
    public $category;

    /**
     *
     * @var bool
     */
    public $isPublished;


    /**
     *
     * @var bool
     */
    public $isDeleted;

    public static function makeFromProduct(?Product $product): self
    {
        $model = new self();

        if (!$product) {
            return $model;
        }

        $model->id = $product->getId();
        $model->title = $product->getTitle();
        $model->price = $product->getPrice();
        $model->quantity = $product->getQuantity();
        $model->description = $product->getDescription();
        $model->isPublished = $product->getIsPublished();
        $model->isDeleted = $product->getIsDeleted();

        return $model;
    }

    /**
     *
     * @param Product $product
     * @return Product
     */
    public function makeProductFromModel(Product $product): Product
    {
        $product
            ->setCategory($this->category)
            ->setTitle($this->title)
            ->setPrice($this->price)
            ->setQuantity($this->quantity)
            ->setDescription($this->description)
            ->setIsPublished($this->isPublished)
            ->setIsDeleted($this->isDeleted)
        ;

        return $product;
    }

}
