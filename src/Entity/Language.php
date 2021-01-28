<?php

namespace App\Entity;

use App\Repository\LanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LanguageRepository::class)
 */
class Language
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
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\OneToOne(targetEntity=Category::class, mappedBy="language")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=SellingItem::class, mappedBy="language", orphanRemoval=true)
     */
    private $sellingItems;

    /**
     * @ORM\Column(type="boolean")
     */
    private $publication;

    /**
     * @ORM\OneToOne(targetEntity=Statuses::class, mappedBy="language", cascade={"persist", "remove"})
     */
    private $statuses;

    public function __construct()
    {
        $this->sellingItems = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        // set the owning side of the relation if necessary
        if ($category->getLanguage() !== $this) {
            $category->setLanguage($this);
        }

        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|SellingItem[]
     */
    public function getSellingItems(): Collection
    {
        return $this->sellingItems;
    }

    public function addSellingItem(SellingItem $sellingItem): self
    {
        if (!$this->sellingItems->contains($sellingItem)) {
            $this->sellingItems[] = $sellingItem;
            $sellingItem->setLanguage($this);
        }

        return $this;
    }

    public function removeSellingItem(SellingItem $sellingItem): self
    {
        if ($this->sellingItems->removeElement($sellingItem)) {
            // set the owning side to null (unless already changed)
            if ($sellingItem->getLanguage() === $this) {
                $sellingItem->setLanguage(null);
            }
        }

        return $this;
    }

    public function getPublication(): ?bool
    {
        return $this->publication;
    }

    public function setPublication(bool $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getStatuses(): ?Statuses
    {
        return $this->statuses;
    }

    public function setStatuses(Statuses $statuses): self
    {
        // set the owning side of the relation if necessary
        if ($statuses->getLanguage() !== $this) {
            $statuses->setLanguage($this);
        }

        $this->statuses = $statuses;

        return $this;
    }
}
