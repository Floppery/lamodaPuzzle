<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Entity(repositoryClass="App\Repository\CargoItemRepository")
 */
class CargoItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     * @Serializer\Groups({"cargo_item_details", "cargo_item_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Serializer\Expose
     * @Serializer\Groups({"cargo_item_details", "cargo_item_list"})
     */
    private $title;

    /**
     * @var Cargo|null
     * @ORM\ManyToOne(targetEntity="Cargo", inversedBy="item")
     * @ORM\JoinColumn(name="cargo_id", referencedColumnName="id")
     */
    protected $cargo;

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
     * @return CargoItem
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Cargo|null
     */
    public function getCargo(): ?Cargo
    {
        return $this->cargo;
    }

    /**
     * @param Cargo $cargo
     * @return CargoItem
     */
    public function setCargo(Cargo $cargo = null): self
    {
        $this->cargo = $cargo;
        return $this;
    }
}
