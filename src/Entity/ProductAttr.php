<?php

namespace App\Entity;

use App\Repository\ProductAttrRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductAttrRepository::class)
 */
class ProductAttr
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $quantity;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="products")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=ProductSize::class, inversedBy="sizes")
     */
    private $productSize;

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getProductSize(): ?ProductSize
    {
        return $this->productSize;
    }

    public function setProductSize(?ProductSize $productSize): self
    {
        $this->productSize = $productSize;

        return $this;
    }
}
