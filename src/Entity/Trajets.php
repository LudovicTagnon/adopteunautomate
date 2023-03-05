<?php

namespace App\Entity;

#use Assert\Choice;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrajetsRepository;
use Symfony\Component\Validator\Constraints\DateTime;
//use Symfony\Component\Validator\Constraints\DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Monolog\DateTimeImmutable;

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

        if ($nbPassagerCourant >= $nbPassagerMax) {
            $context->buildViolation('Le nombre de passagers courant doit être inférieur au nombre de passagers maximal.')
                ->atPath('nbPassagersCourant')
                ->addViolation();
        }
    }

    //public function setLessthan(): void
    //{
    //    $this->nb_passager_courant <$this->nb_passager_max;
    //}


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
    #[Assert\GreaterThan('tomorrow')]
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
    //#[Assert\PositiveOrZero($nb_passager_max - $nb_passager_courant)]
    //#[Assert\Range(min: 0, max: $this.$nb_passager_max)]
    //#[Assert\LessThanOrEqual('$this.$nb_passager_max')]
    private ?int $nb_passager_courant = 0;

    # le voyage est public a priori
    #[ORM\Column]
    private ?bool $public = true;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $renseignement = null;

    #[ORM\ManyToOne(inversedBy: 'trajets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateurs $publie = null;

    #[ORM\ManyToOne(inversedBy: 'demarrant')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Villes $demarrea = null;

    #[ORM\ManyToOne(inversedBy: 'arrivant')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Villes $arrivea = null;
/*
    #[ORM\OneToOne(inversedBy: 'depart_de', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Villes $demarre_de = null;

    #[ORM\OneToOne(inversedBy: 'arrivee_de', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Villes $arrive_a = null;
*/
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
/*
    public function getDemarreDe(): ?Villes
    {
        return $this->demarre_de;
    }

    public function setDemarreDe(Villes $demarre_de): self
    {
        $this->demarre_de = $demarre_de;

        return $this;
    }

    public function getArriveA(): ?Villes
    {
        return $this->arrive_a;
    }

    public function setArriveA(Villes $arrive_a): self
    {
        $this->arrive_a = $arrive_a;

        return $this;
    }
*/

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

}
