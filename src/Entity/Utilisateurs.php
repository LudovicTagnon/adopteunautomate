<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;


    #[ORM\Column(type: "string", length: 50, nullable: true, options: ["default" => "inconnu"])]
    private ?string $nom;


    #[ORM\Column(type: "string", length: 255, nullable: true, options: ["default" => "inconnu"])]
    #[Assert\NotBlank]
    private $prenom;


    #[ORM\Column(type: "string", length: 20, nullable: true, options: ["default" => "Unknown"])]
    private ?string $num_tel;

    #[ORM\Column(type: "boolean", options: ["default" => 0])]
    private ?bool $vehicule;

    #[ORM\Column(type: "string", length: 10, nullable: true)]
    private ?string $genre = null;


    #[ORM\Column(type: "boolean")]
    private ?bool $autorisation_mail;

    #[ORM\Column(type: "blob", nullable: true)]
    private $fichier_photo;

    #[ORM\Column(type: "integer")]
    #[Assert\PositiveOrZero]
    private ?int $cumul_notes = 0;

    #[ORM\Column(type: "integer")]
    #[Assert\PositiveOrZero]
    private ?int $nb_notes = 0;

    #[ORM\Column(type: "boolean")]
    private ?bool $compte_actif = true;

    #[ORM\OneToMany(mappedBy: 'utilisateurs', targetEntity: Groupes::class, orphanRemoval: true)]
    private $groupes;

    #[ORM\OneToMany(mappedBy: 'publie', targetEntity: Trajets::class)]
    private Collection $trajets;

    public function __construct()
    {
        $this->trajets = new ArrayCollection();
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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->num_tel;
    }

    public function setNumTel(?string $num_tel): self
    {
        $this->num_tel = $num_tel;

        return $this;
    }

    public function getVehicule(): ?bool
    {
        return $this->vehicule;
    }

    public function setVehicule(?bool $vehicule): self
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(?string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAutorisationMail(): ?bool
    {
        return $this->autorisation_mail;
    }

    public function setAutorisationMail(?bool $autorisation_mail): self
    {
        $this->autorisation_mail = $autorisation_mail;

        return $this;
    }

    public function getFichierPhoto()
    {
        return $this->fichier_photo;
    }

    public function setFichierPhoto($fichier_photo): self
    {
        $this->fichier_photo = $fichier_photo;

        return $this;
    }
    public function getCumulNotes(): ?int
    {
        return $this->cumul_notes;
    }

    public function setCumulNotes(?int $cumul_notes): self
    {
        $this->cumul_notes = $cumul_notes;

        return $this;
    }

    public function getNbNotes(): ?int
    {
        return $this->nb_notes;
    }

    public function setNbNotes(?int $nb_notes): self
    {
        $this->nb_notes = $nb_notes;

        return $this;
    }
    public function getCompteActif(): ?bool
    {
        return $this->compte_actif;
    }

    public function setCompteActif(?bool $compte_actif): self
    {
        $this->compte_actif = $compte_actif;

        return $this;
    }

    /**
     * @return Collection<int, Trajets>
     */
    public function getTrajets(): Collection
    {
        return $this->trajets;
    }

    public function addTrajet(Trajets $trajet): self
    {
        if (!$this->trajets->contains($trajet)) {
            $this->trajets->add($trajet);
            $trajet->setPublie($this);
        }

        return $this;
    }

    public function removeTrajet(Trajets $trajet): self
    {
        if ($this->trajets->removeElement($trajet)) {
            // set the owning side to null (unless already changed)
            if ($trajet->getPublie() === $this) {
                $trajet->setPublie(null);
            }
        }

        return $this;
    }

}
