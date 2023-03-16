<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EstDansRepository;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\BrowserKit\Response;

#[ORM\Entity(repositoryClass: EstDansRepository::class)]
#[ORM\Table(name:"estDans")]
#[ORM\UniqueConstraint(name: "utilisateur_groupe_unique", columns: ["utilisateurs_id", "groupes_id"])]
class EstDans
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Utilisateurs::class)]
    protected $utilisateurs;

    #[ORM\ManyToOne(targetEntity:Groupes::class, inversedBy:"estDans")]
    protected $groupes;

    public function setGroupes(Groupes $groupe): self
    {
        $this->groupes = $groupe;

        return $this;
    }

    public function getGroupes(Groupes $groupe): ?Groupes
    {
        return $this->groupes;
    }

    public function setUtilisateur(Utilisateurs $utilisateur): self
    {
        $this->utilisateurs = $utilisateur;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateurs
    {
        return $this->utilisateurs;
    }

    public function getIdUtilisateur(): ?String
    {
        return $this->utilisateurs->getId();
    }

    public function getNomUtilisateur(): ?String
    {
        return $this->utilisateurs->getNom();
    }

    public function getPrenomUtilisateur(): ?String
    {
        return $this->utilisateurs->getPrenom();
    }

    public function __toString()
    {
        $format = "Groupe (Id: %s, %s, %s)\n";
        return sprintf($format, $this->id, $this->utilisateurs, $this->groupes);
    }

}

?>