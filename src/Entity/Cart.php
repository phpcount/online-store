<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource as Api;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups as Groups;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 *
 * @Api(
 *      collectionOperations={
 *          "get"={
 *                  "normalization_context"={"groups"="cart:list"}
 *                },
 *          "post"={
 *                  "normalization_context"={"groups"="cart:list:write"}
 *                 }
 *       },
 *      itemOperations={
 *          "get"={
 *                  "normalization_context"={"groups"="cart:item"}
 *                 },
 *          "delete"={}
 *      }
 * )
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({"cart:list", "cart:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"cart:list", "cart:item"})
     */
    private $sessionId;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=CartProduct::class, mappedBy="cart", orphanRemoval=true)
     *
     * @Groups({"cart:list", "cart:item"})
     */
    private $cartProducts;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->cartProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, CartProduct>
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts[] = $cartProduct;
            $cartProduct->setCart($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartProduct->getCart() === $this) {
                $cartProduct->setCart(null);
            }
        }

        return $this;
    }
}
