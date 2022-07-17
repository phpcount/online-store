<?php

namespace App\Form\DTO;

use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

class EditCategoryModel
{
    /**
     * @var int
     */
    public $id;

    /**
     * @Assert\NotBlank(message="Please enter a title")
     *
     * @var string
     */
    public $title;

    public static function makeFromCategory(?Category $category): self
    {
        $model = new self();

        if (!$category) {
            return $model;
        }

        $model->id = $category->getId();
        $model->title = $category->getTitle();

        return $model;
    }

    public function makeCategoryFromModel(Category $category): Category
    {
        $category
            ->setTitle($this->title)
        ;

        return $category;
    }
}
