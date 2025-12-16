<?php

namespace App\Entity;

use App\Repository\AplicacionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection; // AGREGAR ESTAS 2 LÍNEAS

#[ORM\Entity(repositoryClass: AplicacionRepository::class)]
class Aplicacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, unique: true)] 
    private ?string $apiKey = null;

    #[ORM\Column]
    private ?bool $estado = null;

    // AGREGAR ESTA PROPIEDAD (relación con Juego)
    #[ORM\OneToMany(mappedBy: 'aplicacion', targetEntity: Juego::class)]
    private Collection $juegos;

    // AGREGAR ESTE CONSTRUCTOR
    public function __construct()
    {
        $this->juegos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function isEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): static
    {
        $this->estado = $estado;

        return $this;
    }

    // AGREGAR ESTOS 4 MÉTODOS PARA LA RELACIÓN

    /**
     * @return Collection<int, Juego>
     */
    public function getJuegos(): Collection
    {
        return $this->juegos;
    }

    public function addJuego(Juego $juego): static
    {
        if (!$this->juegos->contains($juego)) {
            $this->juegos->add($juego);
            $juego->setAplicacion($this);
        }

        return $this;
    }

    public function removeJuego(Juego $juego): static
    {
        if ($this->juegos->removeElement($juego)) {
            // set the owning side to null (unless already changed)
            if ($juego->getAplicacion() === $this) {
                $juego->setAplicacion(null);
            }
        }

        return $this;
    }

    // AGREGAR ESTE MÉTODO PARA MOSTRAR EN FORMULARIOS
    public function __toString(): string
    {
        return $this->nombre;
    }
}