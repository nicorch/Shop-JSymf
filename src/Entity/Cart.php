<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CartRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEdited;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\Column(type="float")
     */
    private $taxes;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class)
     */
    private $products;

    /**
     * @ORM\Column(type="float")
     */
    private $ttc;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->dateCreated = new DateTime();
        
        $this->total = 0;
        $this->taxes = 0;
        $this->ttc = 0;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'total' => $this->getTotal(),
            'taxes' => $this->getTaxes(),
            'ttc' => $this->getTtc(),
            'dateEdited' => ($this->getDateEdited()) ? $this->getDateEdited()->format('d-m-Y H:i') : null,
            'dateCreated' => $this->getDateCreated()->format('d-m-Y H:i'),
            'products' => array_map(function ($product){
                return $product->toArray();
            }, $this->getProducts()->toArray()),
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTimeInterface $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getDateEdited(): ?\DateTimeInterface
    {
        return $this->dateEdited;
    }

    public function setDateEdited(?\DateTimeInterface $dateEdited): self
    {
        $this->dateEdited = $dateEdited;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getTaxes(): ?float
    {
        return $this->taxes;
    }

    public function setTaxes(float $taxes): self
    {
        $this->taxes = $taxes;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }
        $totalP = 0;
        foreach ($this->getProducts() as $prd) {
            $totalP += $prd->getPrice();
        }
        $this->setTotal($totalP);
        $this->setTaxes(($totalP*0.20));
        $this->setTtc($this->getTotal() + $this->getTaxes());
        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $price = $product->getPrice();
        $this->products->removeElement($product);
        $this->setTotal($this->getTotal() - $price);
        $this->setTaxes(($this->getTotal()*0.20));
        $this->setTtc($this->getTotal() + $this->getTaxes());

        return $this;
    }

    public function getTtc(): ?float
    {
        return $this->ttc;
    }

    public function setTtc(float $ttc): self
    {
        $this->ttc = $ttc;

        return $this;
    }
}
