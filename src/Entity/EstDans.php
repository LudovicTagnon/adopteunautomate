<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
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

    public function __toString()
    {
        $format = "Groupe (Id: %s, %s, %s)\n";
        return sprintf($format, $this->id, $this->utilisateurs, $this->groupes);
    }
}

?>