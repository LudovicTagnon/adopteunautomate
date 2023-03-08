<?php

namespace App\DataFixtures;

use App\Entity\Villes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $fichierVillesFrance = "src/Donnees/villesFrance3.csv";
        $villesFrance=file($fichierVillesFrance);
        foreach ($villesFrance as $numligne=>$villeFrance) {
            $ville = new Villes();
            $villeCP = explode(",",$villeFrance);
            $ville->setNomVille($villeCP[1]);
            $ville->setCP((int)$villeCP[0]);
            $manager->persist($ville);
            $manager->flush();
        }
        /* // Ajout de la ville de Nancy
        $ville = new Villes();
        $ville->setNomVille("Nancy");
        $ville->setCP(54000);
        $manager->persist($ville);
        $manager->flush();
        // Ajout de la ville de Metz
        $ville = new Villes();
        $ville->setNomVille("Metz");
        $ville->setCP(57000);
        $manager->persist($ville);
        $manager->flush(); */
    }
}
