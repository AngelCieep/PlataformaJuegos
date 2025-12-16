<?php

namespace App\Entity;

use App\Repository\JuegoRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection; // AGREGAR

#[ORM\Entity(repositoryClass: JuegoRepository::class)]
class Juego
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255, unique: true)] // AGREGAR unique: true
    private ?string $tokenJuego = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?bool $estado = true;

    // AGREGAR: Relación con Aplicacion
    #[ORM\ManyToOne(inversedBy: 'juegos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Aplicacion $aplicacion = null;

    // AGREGAR: Relación con Partida
    #[ORM\OneToMany(mappedBy: 'juego', targetEntity: Partida::class)]
    private Collection $partidas;

    // AGREGAR: Constructor
    public function __construct()
    {
        $this->partidas = new ArrayCollection();
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

    public function getTokenJuego(): ?string
    {
        return $this->tokenJuego;
    }

    public function setTokenJuego(string $tokenJuego): static
    {
        $this->tokenJuego = $tokenJuego;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
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

    // AGREGAR: Métodos para la relación con Aplicacion
    public function getAplicacion(): ?Aplicacion
    {
        return $this->aplicacion;
    }

    public function setAplicacion(?Aplicacion $aplicacion): static
    {
        $this->aplicacion = $aplicacion;
        return $this;
    }

    // AGREGAR: Métodos para la relación con Partida
    /**
     * @return Collection<int, Partida>
     */
    public function getPartidas(): Collection
    {
        return $this->partidas;
    }

    public function addPartida(Partida $partida): static
    {
        if (!$this->partidas->contains($partida)) {
            $this->partidas->add($partida);
            $partida->setJuego($this);
        }

        return $this;
    }

    public function removePartida(Partida $partida): static
    {
        if ($this->partidas->removeElement($partida)) {
            if ($partida->getJuego() === $this) {
                $partida->setJuego(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nombre;
    }
}