<?php

namespace App\DataFixtures;

use App\Entity\Villes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Ajout de la ville de Nancy
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
        $manager->flush();
    }
}
