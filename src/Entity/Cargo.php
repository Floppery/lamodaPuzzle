<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CargoRepository")
 * @Serializer\ExclusionPolicy("all")
 */
class Cargo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"cargo_details", "cargo_list"})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups({"cargo_details", "cargo_list"})
     */
    private $title;

    /**
     * @var ArrayCollection|PersistentCollection|null
     * @ORM\OneToMany(targetEntity="CargoItem", mappedBy="cargo", cascade={"persist", "remove"})
     * @Serializer\Expose
     * @Serializer\Groups({"cargo_details"})
     */
    private $item;

    /**
     * Cargo constructor.
     */
    public function __construct()
    {
        $this->item = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Cargo
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return Collection|null
     */
    public function getItem(): ?Collection
    {
        return $this->item;
    }

    /**
     * @param ArrayCollection $item
     * @return Cargo
     */
    public function setItem(ArrayCollection $item): self
    {
        $this->item = $item;
        return $this;
    }

    /**
     * @param CargoItem $item
     * @return Cargo
     */
    public function addItem(CargoItem $item): self
    {
        $this->item[] = $item;
        return $this;
    }

    /**
     * @param CargoItem $item
     * @return Cargo
     */
    public function removeItem(CargoItem $item): self
    {
        $this->item->removeElement($item);
        return $this;
    }
}