<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OptionRepository::class)
 * @ORM\Table(name="`option`")
 */
class Option
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_supp;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_perc;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="options")
     */
    private $product;

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'type' => $this->getType(),
            'price_supp' => ($this->getPriceSupp()) ? $this->getPriceSupp() : null,
            'price_perc' => ($this->getPricePerc()) ? $this->getPricePerc() : null,
        ];
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getPriceSupp(): ?float
    {
        return $this->price_supp;
    }

    public function setPriceSupp(?float $price_supp): self
    {
        $this->price_supp = $price_supp;

        return $this;
    }

    public function getPricePerc(): ?float
    {
        return $this->price_perc;
    }

    public function setPricePerc(?float $price_perc): self
    {
        $this->price_perc = $price_perc;

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
}
