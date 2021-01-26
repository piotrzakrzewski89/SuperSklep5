<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
     * @ORM\OneToOne(targetEntity=SellingItem::class, mappedBy="category", cascade={"persist", "remove"})
     */
    private $sellingItem;

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

    public function getSellingItem(): ?SellingItem
    {
        return $this->sellingItem;
    }

    public function setSellingItem(?SellingItem $sellingItem): self
    {
        // unset the owning side of the relation if necessary
        if ($sellingItem === null && $this->sellingItem !== null) {
            $this->sellingItem->setCategory(null);
        }

        // set the owning side of the relation if necessary
        if ($sellingItem !== null && $sellingItem->getCategory() !== $this) {
            $sellingItem->setCategory($this);
        }

        $this->sellingItem = $sellingItem;

        return $this;
    }
}
