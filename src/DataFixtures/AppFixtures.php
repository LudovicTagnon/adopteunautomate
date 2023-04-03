<?php

namespace App\DataFixtures;

use App\Entity\Utilisateurs;
use App\Entity\Trajets;
use App\Entity\Villes;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        for ($i = 1; $i < 51; $i++) {
            // Utilisateur 1 (chauffeur)
            // Nom : chauffeur.i
            $utilisateur = new Utilisateurs();
            $email = "chauffeur".$i."@gmail.com";
            $utilisateur->setEmail($email);
            $utilisateur->setPassword("password");
            $utilisateur->setNom("chauffeur".$i);
            $utilisateur->setPrenom("prenom".$i);
            $utilisateur->setNumTel("0600000000"); 
            $utilisateur->setVehicule(true);
            $utilisateur->setGenre("Femme");
            $utilisateur->setAutorisationMail(false);
            $manager->persist($utilisateur);
            $manager->flush(); 
        }
        for ($i = 1; $i < 51; $i++) {
            // Utilisateur 2 (passager)
            // Nom : passager.i
            $utilisateur = new Utilisateurs();
            $email = "passager".$i."@free.fr";
            $utilisateur->setEmail($email);
            $utilisateur->setPassword("password");
            $utilisateur->setNom("passager".$i);
            $utilisateur->setPrenom("prenom".$i);
            $utilisateur->setNumTel("0601010101");
            $utilisateur->setVehicule(false);
            $utilisateur->setGenre("Homme");
            $utilisateur->setAutorisationMail(true);
            $manager->persist($utilisateur);
            $manager->flush(); 
        }
        // Voyage 1
        // Nancy -> Metz
        // Dans 25h
        // 2 places
        // 15€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+25 hours'));
        $voyage->setTArrivee(new DateTime('+26 hours'));
        $voyage->setPrix(15.0);
        $voyage->setNbPassagerMax(2);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 1]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 54]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 57]));
        $manager->persist($voyage);
        $manager->flush();
        // Voyage 2
        // Nancy -> Paris
        // Dans 49h
        // 2 places
        // 15€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+49 hours'));
        $voyage->setTArrivee(new DateTime('+53 hours'));
        $voyage->setPrix(30.0);
        $voyage->setNbPassagerMax(4);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 2]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 54]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 75]));
        $manager->persist($voyage);
        $manager->flush();
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
