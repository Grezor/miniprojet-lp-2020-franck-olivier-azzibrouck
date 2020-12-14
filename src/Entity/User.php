<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="Cette adresse mail n'est plus disponible")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     */
    private $prenom;

    /**
     * @Assert\Length(4)
     * @Assert\NotBlank()
     */
    protected $captcha;

    protected $formule;

    /**
     * @ORM\OneToMany(targetEntity=Dossier::class, mappedBy="user")
     */
    private $dossiers;



    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Choixformule::class, mappedBy="user")
     */
    private $choixformules;

    public function __construct()
    {
        $this->dossiers = new ArrayCollection();
        $this->choixformules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getCaptcha(): ?string
    {
        return $this->captcha;
    }

    public function setCaptcha(string $captcha): self
    {
        $this->captcha = $captcha;

        return $this;
    }

    /**
     * @return Collection|Dossier[]
     */
    public function getDossiers(): Collection
    {
        return $this->dossiers;
    }

    public function addDossier(Dossier $dossier): self
    {
        if (!$this->dossiers->contains($dossier)) {
            $this->dossiers[] = $dossier;
            $dossier->setUser($this);
        }

        return $this;
    }

    public function removeDossier(Dossier $dossier): self
    {
        if ($this->dossiers->removeElement($dossier)) {
            // set the owning side to null (unless already changed)
            if ($dossier->getUser() === $this) {
                $dossier->setUser(null);
            }
        }

        return $this;
    }


    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->email;
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
            $choixformule->setUser($this);
        }

        return $this;
    }

    public function removeChoixformule(Choixformule $choixformule): self
    {
        if ($this->choixformules->removeElement($choixformule)) {
            // set the owning side to null (unless already changed)
            if ($choixformule->getUser() === $this) {
                $choixformule->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormule()
    {
        return $this->formule;
    }

    /**
     * @param mixed $formule
     */
    public function setFormule($formule): void
    {
        $this->formule = $formule;
    }


}
