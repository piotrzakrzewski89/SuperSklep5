<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
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
    private $file_path;

    /**
     * @ORM\ManyToOne(targetEntity=SellingItem::class, inversedBy="images")
     * @ORM\JoinColumn(nullable=false)
     */
    private $selling_item;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->file_path;
    }

    public function setFilePath(string $file_path): self
    {
        $this->file_path = $file_path;

        return $this;
    }

    public function getSellingItem(): ?SellingItem
    {
        return $this->selling_item;
    }

    public function setSellingItem(?SellingItem $selling_item): self
    {
        $this->selling_item = $selling_item;

        return $this;
    }
}
