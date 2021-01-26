<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=FichierRepository::class)
 */
class Fichier
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
    private $Libelle;



    /**
     * @ORM\Column(type="float")
     */
    private $taille=0.0;

    /**
     * @ORM\ManyToOne(targetEntity=Dossier::class, inversedBy="fichiers")
     */
    private $dossier;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;



    public function __construct()
    {
        $this->date= new DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->Libelle;
    }

    public function setLibelle(string $Libelle): self
    {
        $this->Libelle = $Libelle;

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

    public function getDossier(): ?Dossier
    {
        return $this->dossier;
    }

    public function setDossier(?Dossier $dossier): self
    {
        $this->dossier = $dossier;

        return $this;
    }


    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->libelle;
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
    function convertisseur($octet)
    {
//        // Array contenant les differents unités
//        $unite = array('octet','ko','Mo','go');
        $mo = round($octet/(1024*1024),2);
        return $mo;

    }

}
