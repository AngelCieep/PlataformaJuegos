<?php

namespace App\Entity;

use App\Repository\PartidaRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User; // AGREGAR
use App\Entity\Juego; // AGREGAR

#[ORM\Entity(repositoryClass: PartidaRepository::class)]
class Partida
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $puntos = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $fecha = null;

    // AGREGAR: Relación con User
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $usuario = null;

    // AGREGAR: Relación con Juego
    #[ORM\ManyToOne(inversedBy: 'partidas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Juego $juego = null;

    public function __construct()
    {
        $this->fecha = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPuntos(): ?int
    {
        return $this->puntos;
    }

    public function setPuntos(int $puntos): static
    {
        $this->puntos = $puntos;
        return $this;
    }

    public function getFecha(): ?\DateTimeImmutable
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeImmutable $fecha): static
    {
        $this->fecha = $fecha;
        return $this;
    }

    // AGREGAR: Métodos para la relación con User
    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): static
    {
        $this->usuario = $usuario;
        return $this;
    }

    // AGREGAR: Métodos para la relación con Juego
    public function getJuego(): ?Juego
    {
        return $this->juego;
    }

    public function setJuego(?Juego $juego): static
    {
        $this->juego = $juego;
        return $this;
    }

    public function __toString(): string
    {
        return sprintf('Partida %d - %s', $this->id, $this->fecha->format('Y-m-d H:i'));
    }
}