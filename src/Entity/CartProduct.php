<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource as Api;
use App\Repository\CartProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CartProductRepository::class)
 *
 * @Api(
 *      collectionOperations={
 *          "get"={
 *                  "normalization_context"={"groups"="cart_product:list"}
 *                },
 *          "post"={
 *                  "normalization_context"={"groups"="cart_product:list:write"}
 *                 }
 *       },
 *      itemOperations={
 *          "get"={
 *                  "normalization_context"={"groups"="cart_product:item"}
 *                },
 *          "delete"={},
 *          "patch"={}
 *      }
 * )
 */
class CartProduct
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"cart_product:list", "cart_product:item", "cart:list", "cart:item"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Cart::class, inversedBy="cartProducts")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"cart_product:list", "cart_product:item"})
     */
    private $cart;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="cartProducts")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"cart_product:list", "cart_product:item", "cart:list", "cart:item"})
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"cart_product:list", "cart_product:item", "cart:list", "cart:item"})
     */
    private $quantity;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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
}
