<?php

namespace App\Entity;

use App\Repository\FormuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FormuleRepository::class)
 */
class Formule
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $libelle;

    /**
     * @ORM\Column(type="float")
     */
    private $taille;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="formule")
     */
    private $users;


    /**
     * @ORM\OneToMany(targetEntity=Choixformule::class, mappedBy="formule")
     */
    private $choixformules;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->choixformules = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?int
    {
        return $this->libelle;
    }

    public function setLibelle(int $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTaille(): ?float
    {
        return $this->taille;
    }

    public function setTaille(float $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setFormule($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getFormule() === $this) {
                $user->setFormule(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->libelle;
    }



    /**
     * @return Collection|Choixformule[]
     */
    public function getChoixformules(): Collection
    {
        return $this->choixformules;
    }

    public function addChoixformule(Choixformule $choixformule): self
    {
        if (!$this->choixformules->contains($choixformule)) {
            $this->choixformules[] = $choixformule;
            $choixformule->setFormule($this);
        }

        return $this;
    }

    public function removeChoixformule(Choixformule $choixformule): self
    {
        if ($this->choixformules->removeElement($choixformule)) {
            // set the owning side to null (unless already changed)
            if ($choixformule->getFormule() === $this) {
                $choixformule->setFormule(null);
            }
        }

        return $this;
    }
}
