<?php

namespace App\Entity;


#use Assert\Choice;

use App\Entity\Villes;
use App\Entity\Adopte;
use App\Entity\EstAccepte;
use Doctrine\DBAL\Types\Types;
use Monolog\DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrajetsRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\DateTime;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;



#[ORM\Entity(repositoryClass: TrajetsRepository::class)]
class Trajets
{
    /**
     * @Assert\Callback(callback="validateAttributes")
     * Controle de la validité du nombre de passagers courants
     */
    public function validateAttributes(ExecutionContextInterface $context, $payload)
    {
        $nbPassagerCourant = $this->getNbPassagerCourant();
        $nbPassagerMax = $this->getNbPassagerMax();
        $T_arrivee= $this->getTArrivee();     
        $T_depart=$this->getTDepart();
        //

        if ($nbPassagerCourant >= $nbPassagerMax) {
            $context->buildViolation('Le nombre de passagers courant doit être inférieur au nombre de passagers maximal.')
                ->atPath('nbPassagersCourant')
                ->addViolation();
        }

        //$demain = new DateTime('tomorrow');
        $demain = new DateTime('+24 hours');
        //$demain->modify('+24 hours');
        if ($T_depart < $demain) {
            $context->buildViolation('Le départ doit avoir lieu dans plus de 24h.')
                ->atPath('T_depart')
                ->addViolation();
        }

        // si l'heure d'arrivée est renseignée
        if ($T_arrivee !=null){
            // si elle est avant le départ
            if ($T_arrivee <= $T_depart) {
                $context->buildViolation('L\'arrivée a lieu après le départ. Merci de rectifier.')
                    ->atPath('T_arrivee')
                    ->addViolation();
            }
        }
/*
        if ($T_depart < 'tomorrow') {
            $context->buildViolation('Le départ doit avoir lieu dans plus de 24h.')
                ->atPath('T_depart')
                ->addViolation();
        }
        */
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #liste limitéé
    #[Assert\Choice(['ouvert', 'bloqué', 'terminé', 'annulé '])]
    private ?string $etat = 'ouvert';

    # 24h de délai
    #[ORM\Column]
    #[Assert\GreaterThan('+24 hours')]
    //#[Assert\GreaterThan( value="today",
    // message="La date doit être supérieure ou égale à la date du jour.")]
    private ?\DateTime $T_depart = null;

    // arrivée après le départ
    #[ORM\Column(nullable: true)]
    //#[Assert\GreaterThan('$T_depart')]
    private ?\DateTime $T_arrivee = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?float $prix = null;

    # voyages en voiture: moins de 8 passagers
    #[ORM\Column]
    #[Assert\Range(min: 1, max: 8)]
    private ?int $nb_passager_max = null;

    # places prises: entre 0 et nb_passager_max
    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private ?int $nb_passager_courant = 0;

    # le voyage est public a priori
    #[ORM\Column]
    private ?bool $public = true;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $renseignement = null;

    #[ORM\ManyToOne(inversedBy: 'trajets',)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $publie = null;

    #[ORM\ManyToOne(inversedBy: 'demarrant', cascade:["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Villes $demarrea = null;

    #[ORM\ManyToOne(inversedBy: 'arrivant', cascade:["persist"])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Villes $arrivea = null;
/*  relations non fonctionnelles 23 03
    #[ORM\OneToMany( mappedBy: 'adopte', targetEntity: Adopte::class, cascade:["persist", "remove"])]
    private Collection $adopte;
    
    #[ORM\OneToMany(mappedBy: 'estAccepte', targetEntity: EstAccepte::class, cascade:["persist", "remove"])]
    private Collection $estAccepte;
    */
    #[ORM\OneToMany(targetEntity: Adopte::class, mappedBy: 'adopte')]
    private $adopte;

   

    #[ORM\OneToMany(mappedBy: 'trajets', targetEntity: Groupes::class)]
    private Collection $groupes;
    public function __construct()
    {
        $this->groupes     = new ArrayCollection();
        $this->adopte      = new ArrayCollection();
       // $this->estAccepte  = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getTDepart(): ?\DateTime
    {
        return $this->T_depart;
    }

    public function getJourDepartString(): ?string
    {
        return $this->T_depart->format('d-m-y');
    }

    public function getHeureDepartString(): ?string
    {
        return $this->T_depart->format('H-i');
    }

    public function getJourArriveeString(): ?string
    {
        return $this->T_arrivee->format('d-m-y');
    }

    public function getHeureArriveeString(): ?string
    {
        $heure = "nr";
        
        if($this->T_arrivee != null){
            $heure = $this->T_arrivee->format('H-i');
        }
        
        return $heure;
    }

    public function getTempsTrajetString(): ?String
    {
        if ($this->T_arrivee == null)
        { $this->T_arrivee = $this->T_depart ;
        $this->T_arrivee->modify('+12 hours');
        }
        $temps = "00-00";
        if($this->T_arrivee != null){
            $temps = date_diff($this->T_depart,$this->T_arrivee)->format('%H-%I');
        
        }
        return $temps;
    }

    public function setTDepart(\DateTime $T_depart): self
    {
        $this->T_depart = $T_depart;

        return $this;
    }

    
    public function getTArrivee(): ?\DateTime
    {
        return $this->T_arrivee;
    }

    public function setTArrivee(?\DateTime $T_arrivee): self
    {
        $this->T_arrivee = $T_arrivee;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNbPassagerMax(): ?int
    {
        return $this->nb_passager_max;
    }

    public function setNbPassagerMax(int $nb_passager_max): self
    {
        $this->nb_passager_max = $nb_passager_max;

        return $this;
    }

    public function getNbPassagerCourant(): ?int
    {
        return $this->nb_passager_courant;
    }

    public function setNbPassagerCourant(int $nb_passager_courant): self
    {
        $this->nb_passager_courant = $nb_passager_courant;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getRenseignement(): ?string
    {
        return $this->renseignement;
    }

    public function setRenseignement(?string $renseignement): self
    {
        $this->renseignement = $renseignement;

        return $this;
    }

    public function getPublie(): ?Utilisateurs
    {
        return $this->publie;
    }

    public function setPublie(?Utilisateurs $publie): self
    {
        $this->publie = $publie;

        return $this;
    }

    public function getDemarreA(): ?Villes
    {
        return $this->demarrea;
    }

    public function setDemarreA(?Villes $demarrea): self
    {
        $this->demarrea = $demarrea;

        return $this;
    }

    public function getArriveA(): ?Villes
    {
        return $this->arrivea;
    }

    public function setArriveA(?Villes $arrivea): self
    {
        $this->arrivea = $arrivea;

        return $this;
    }

    public function addDepart(Villes $ville): self
    {
        if (!$this->demarrea->contains($ville)) {
            $this->demarrea = $ville;
            $ville->addDemarrant($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupes>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupes $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setTrajets($this);
        }

        return $this;
    }

    public function removeGroupe(Groupes $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getTrajets() === $this) {
                $groupe->setTrajets(null);
            }
        }

        return $this;
    }
    public function getPublic(): ?bool
    {
        return $this->public;
    }

    
   
    public function getAdopte(): Collection
    {
        return $this->adopte;
    }

    public function incrementNbPassagerCourant(): self
{
    $this->nb_passager_courant++;
    $this->nb_passager_max--;
    return $this;
}

public function decrementNbPassagerCourant(): self
{
    if($this->nb_passager_courant!=0){
        $this->nb_passager_courant--;
        $this->nb_passager_max++;
    }
    return $this;
}

public function __toString(): string
{
    $format = 'd-m-Y H:i:s'; // set the format to use for the date/time values
    $createurTrajet = $this->publie->getNom()." ".$this->publie->getPrenom();
    $createurTrajetTel = $this->publie->getNumTel();
    $departureCity = $this->demarrea ? $this->demarrea->getnomVille() : '';
    $arrivalCity = $this->arrivea ? $this->arrivea->getnomVille() : '';
    $departureDate = $this->T_depart ? $this->T_depart->format($format) : '';
    $arrivalDate = $this->T_arrivee ? $this->T_arrivee->format($format) : '';

    return sprintf('%s vers %s [%s - %s] par %s (tel : %s) ', $departureCity, $arrivalCity, $departureDate, $arrivalDate, $createurTrajet,$createurTrajetTel);
}

}
