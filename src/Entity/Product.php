<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEdit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVisible;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $size;

    /**
     * @ORM\OneToMany(targetEntity=Option::class, mappedBy="product")
     */
    private $options;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isClone;

    /**
     * @ORM\Column(type="float")
     */
    private $priceTotal;

    public function __construct()
    {
        $this->dateCreated = new DateTime();
        $this->options = new ArrayCollection();
        $this->isClone = false;
        $this->priceTotal = $this->price;
    }

    public function __toString()
    {
        return $this->getName() . ' ('.$this->getPriceTotal().')';
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'price' => $this->getPrice(),
            'priceTotal' => $this->getPriceTotal(),
            'description' => ($this->getDescription()) ? $this->getDescription() : null,
            'color' => ($this->getColor()) ? $this->getColor() : null,
            'size' => ($this->getSize()) ? $this->getSize() : null,
            'imgUrl' => ($this->getImgUrl()) ? $this->getImgUrl() : null,
            'options' => array_map(function ($option){
                return $option->toArray();
            }, $this->getOptions()->toArray()),
            'dateEdit' => ($this->getDateEdit()) ? $this->getDateEdit()->format('d-m-Y H:i') : null,
            'dateCreated' => $this->getDateCreated()->format('d-m-Y H:i'),
            'isVisible' => $this->getIsVisible(),
        ];
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getDateEdit(): ?\DateTimeInterface
    {
        return $this->dateEdit;
    }

    public function setDateEdit(?\DateTimeInterface $dateEdit): self
    {
        $this->dateEdit = $dateEdit;

        return $this;
    }

    public function getIsVisible(): ?bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    public function getImgUrl(): ?string
    {
        return $this->imgUrl;
    }

    public function setImgUrl(?string $imgUrl): self
    {
        $this->imgUrl = $imgUrl;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return Collection|Option[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setProduct($this);
            $price = $this->getPrice();
            foreach ($this->getOptions() as $opt) {
                $price += $opt->getPriceSupp();
            }
            $this->setPriceTotal($price);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getProduct() === $this) {
                $option->setProduct(null);
            }
            $price = $this->getPrice();
            foreach ($this->getOptions() as $opt) {
                $price += $opt->getPriceSupp();
            }
            $this->setPriceTotal($price);
        }

        return $this;
    }

    public function getIsClone(): ?bool
    {
        return $this->isClone;
    }

    public function setIsClone(bool $isClone): self
    {
        $this->isClone = $isClone;

        return $this;
    }

    public function getPriceTotal(): ?float
    {
        return $this->priceTotal;
    }

    public function setPriceTotal(float $priceTotal): self
    {
        $this->priceTotal = $priceTotal;

        return $this;
    }
}
