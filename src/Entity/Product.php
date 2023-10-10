<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $productName = null;

    #[ORM\Column]
    private ?int $fullPrice = null;

    #[ORM\Column]
    private ?bool $popularity = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(nullable: true)]
    private ?int $reducPrice = null;

    #[ORM\ManyToMany(targetEntity: Command::class, mappedBy: 'product')]
    private Collection $commands;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): ?string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getFullPrice(): ?int
    {
        return $this->fullPrice;
    }

    public function setFullPrice(int $fullPrice): static
    {
        $this->fullPrice = $fullPrice;

        return $this;
    }

    public function isPopularity(): ?bool
    {
        return $this->popularity;
    }

    public function setPopularity(bool $popularity): static
    {
        $this->popularity = $popularity;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getReducPrice(): ?int
    {
        return $this->reducPrice;
    }

    public function setReducPrice(?int $reducPrice): static
    {
        $this->reducPrice = $reducPrice;

        return $this;
    }

    /**
     * @return Collection<int, Command>
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): static
    {
        if (!$this->commands->contains($command)) {
            $this->commands->add($command);
            $command->addProduct($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): static
    {
        if ($this->commands->removeElement($command)) {
            $command->removeProduct($this);
        }

        return $this;
    }
}
