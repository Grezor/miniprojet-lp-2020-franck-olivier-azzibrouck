<?php

namespace App\Entity;

use App\Repository\ChoixformuleRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChoixformuleRepository::class)
 */
class Choixformule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="choixformules")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Formule::class, inversedBy="choixformules")
     */
    private $formule;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $taille_disponible;

    /**
     * @ORM\Column(type="boolean")
     */
    private $valide;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    public function __construct($user,$formule)
    {
        $this->user=$user;
        $this->formule= $formule;
        $this->date=new DateTime('now');
        $this->valide= false;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFormule(): ?Formule
    {
        return $this->formule;
    }

    public function setFormule(?Formule $formule): self
    {
        $this->formule = $formule;

        return $this;
    }

    public function getTailleDisponible(): ?float
    {
        return $this->taille_disponible;
    }

    public function setTailleDisponible(?float $taille_disponible): self
    {
        $this->taille_disponible = $taille_disponible;

        return $this;
    }

    public function getValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): self
    {
        $this->valide = $valide;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
