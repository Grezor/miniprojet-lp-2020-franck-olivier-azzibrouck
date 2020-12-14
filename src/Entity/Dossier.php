<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DossierRepository::class)
 */
class Dossier
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
    private $libelle;

    /**
     * @ORM\ManyToOne(targetEntity=Dossier::class, inversedBy="dossiers")
     */
    private $id_dossier;

    /**
     * @ORM\OneToMany(targetEntity=Dossier::class, mappedBy="id_dossier")
     */
    private $dossiers;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="dossiers")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Fichier::class, mappedBy="dossier")
     */
    private $fichiers;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;




    public function __construct()
    {
        $this->date = new DateTime('now');
        $this->dossiers = new ArrayCollection();
        $this->fichiers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getIdDossier(): ?self
    {
        return $this->id_dossier;
    }

    public function setIdDossier(?self $id_dossier): self
    {
        $this->id_dossier = $id_dossier;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getDossiers(): Collection
    {
        return $this->dossiers;
    }

    public function addDossier(self $dossier): self
    {
        if (!$this->dossiers->contains($dossier)) {
            $this->dossiers[] = $dossier;
            $dossier->setIdDossier($this);
        }

        return $this;
    }

    public function removeDossier(self $dossier): self
    {
        if ($this->dossiers->removeElement($dossier)) {
            // set the owning side to null (unless already changed)
            if ($dossier->getIdDossier() === $this) {
                $dossier->setIdDossier(null);
            }
        }

        return $this;
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

    public function __toString()
    {
        return $this->libelle;
    }

    /**
     * @return Collection|Fichier[]
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichier $fichier): self
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers[] = $fichier;
            $fichier->setDossier($this);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): self
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getDossier() === $this) {
                $fichier->setDossier(null);
            }
        }

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
