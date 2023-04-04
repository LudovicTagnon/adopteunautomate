<?php

namespace App\DataFixtures;

use App\Entity\Utilisateurs;
use App\Entity\Trajets;
use App\Entity\Villes;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
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
            //$utilisateur->setPassword("password");
            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, 'password');
            $utilisateur->setPassword($hashedPassword);
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
            //$utilisateur->setPassword("password");
            $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, 'password');
            $utilisateur->setPassword($hashedPassword);
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
        // Voyage 3
        // Auxerre -> Avignon
        // Dans 70h
        // 3 places
        // 50€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+70 hours'));
        $voyage->setTArrivee(new DateTime('+80 hours'));
        $voyage->setPrix(50.0);
        $voyage->setNbPassagerMax(3);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 4]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 89]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 84]));
        $manager->persist($voyage);
        $manager->flush();
        // Voyage 4
        // Angouleme -> Annecy
        // Dans 60h
        // 2 places
        // 15€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+60 hours'));
        $voyage->setTArrivee(new DateTime('+73 hours'));
        $voyage->setPrix(30.0);
        $voyage->setNbPassagerMax(2);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 5]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 17]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 74]));
        $manager->persist($voyage);
        $manager->flush();
        // Voyage 5
        // Bar-le-duc -> Beauvais
        // Dans 49h
        // 2 places
        // 15€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+49 hours'));
        $voyage->setTArrivee(new DateTime('+53 hours'));
        $voyage->setPrix(100.0);
        $voyage->setNbPassagerMax(7);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 6]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 55]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 60]));
        $manager->persist($voyage);
        $manager->flush();
        // Voyage 6
        // Bordeaux -> Bourg-en-bresse
        // Dans 49h
        // 1 places
        // 40€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+95 hours'));
        $voyage->setTArrivee(new DateTime('+97 hours'));
        $voyage->setPrix(40.0);
        $voyage->setNbPassagerMax(1);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 7]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 33]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 1]));
        $manager->persist($voyage);
        $manager->flush();
        // Voyage 7
        // Nancy -> Paris
        // Dans 49h
        // 2 places
        // 15€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+70 hours'));
        $voyage->setTArrivee(new DateTime('+75 hours'));
        $voyage->setPrix(10.0);
        $voyage->setNbPassagerMax(6);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 8]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 12]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 95]));
        $manager->persist($voyage);
        $manager->flush();
        // Voyage 8
        // Blois -> Bobigny
        // Dans 49h
        // 2 places
        // 15€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+60 hours'));
        $voyage->setTArrivee(new DateTime('+70 hours'));
        $voyage->setPrix(60.0);
        $voyage->setNbPassagerMax(6);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 9]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 41]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 93]));
        $manager->persist($voyage);
        $manager->flush();
        // Voyage 9
        // Dijon -> Epinal
        // Dans 49h
        // 2 places
        // 15€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+90 hours'));
        $voyage->setTArrivee(new DateTime('+95 hours'));
        $voyage->setPrix(1000.0);
        $voyage->setNbPassagerMax(4);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 11]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 21]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 88]));
        $manager->persist($voyage);
        $manager->flush();
        // Voyage 10
        // Evry -> Foix
        // Dans 49h
        // 2 places
        // 15€
        // Public
        $voyage = new Trajets();
        $voyage->setEtat('ouvert');
        $voyage->setTDepart(new DateTime('+39 hours'));
        $voyage->setTArrivee(new DateTime('+44 hours'));
        $voyage->setPrix(80.0);
        $voyage->setNbPassagerMax(4);
        $voyage->setPublic(true);
        $voyage->setPublie($manager->getRepository(Utilisateurs::class)->find(['id' => 2]));
        $voyage->setDemarreA($manager->getRepository(Villes::class)->find(['id' => 91]));
        $voyage->setArriveA($manager->getRepository(Villes::class)->find(['id' => 10]));
        $manager->persist($voyage);
        $manager->flush();
    }
}
