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
     */
    protected $captcha;

    protected $formule;

    /**
     * @ORM\OneToMany(targetEntity=Dossier::class, mappedBy="user")
     */
    private $dossiers;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activation_token;

    /**
     * @return mixed
     */
    public function getActivationToken()
    {
        return $this->activation_token;
    }

    /**
     * @param mixed $activation_token
     */
    public function setActivationToken($activation_token): void
    {
        $this->activation_token = $activation_token;
    }

    /**
     * @ORM\Column(type="boolean",name="isVerified")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToMany(targetEntity=Choixformule::class, mappedBy="user")
     */
    private $choixformules;

    /**
     * @ORM\OneToMany(targetEntity=Email::class, mappedBy="admin")
     */
    private $emails;

    public function __construct()
    {
        $this->dossiers = new ArrayCollection();
        $this->choixformules = new ArrayCollection();
        $this->emails = new ArrayCollection();
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

    public function setIsVerified(bool $isVerified)
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


    /**
     * @return array
     * notre tableau de captcha
     */
    public function captcha():array
    {
        $captcha=array("Mcd1","hy3A","b7m8","kfY5");
        return $captcha;
    }

    /**
     * @return Collection|Email[]
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(Email $email): self
    {
        if (!$this->emails->contains($email)) {
            $this->emails[] = $email;
            $email->setAdmin($this);
        }

        return $this;
    }

    public function removeEmail(Email $email): self
    {
        if ($this->emails->removeElement($email)) {
            // set the owning side to null (unless already changed)
            if ($email->getAdmin() === $this) {
                $email->setAdmin(null);
            }
        }

        return $this;
    }


}
