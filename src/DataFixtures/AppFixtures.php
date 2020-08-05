<?php

namespace App\DataFixtures;

use App\Entity\Job;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Casting;
use App\Entity\Department;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\Provider\MovieDbProvider;
use App\Service\Slugger;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Cette classe est créée à l'install du Bundle
 * La commande php bin/console doctrine:fixtures:load
 * exécute la méthode load() de toute classe qui étend de Fixture
 * 
 * On peut créer ce genre de classe via bin/console make:fixtures
 */
class AppFixtures extends Fixture
{

    private $encoder;
    private $slugger;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, Slugger $slugger)
    {
        $this->encoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        // On instancie Faker
        // on pourrait passer en version française ($faker = \Faker\Factory::create('fr_FR');)
        $faker = \Faker\Factory::create();
        // On ajoute notre Provider à Faker
        $faker->addProvider(new MovieDbProvider($faker));
        // On peut définir le point de départ du pseudo-hasard
        // Permet d'avoir toujours les mêmes données
        $faker->seed('hasard');

        // On crée toutes les données de manière à pouvoir les relier entre elles
        // donc danc un certain ordre (Genre avant Movie pour relier Genre à Movie)

        // 10 genres
        $genresList = [];
        for ($i = 1; $i <= 10; $i++) {
            $genre = new Genre();
            $genre->setName($faker->unique()->movieGenre);
            $genre->setCreatedAt(new \DateTime());
            $manager->persist($genre);

            $genresList[] = $genre;
        }

        // 20 films
        // Préparons un tableau pour stocker les films
        $moviesList = [];
        for ($i = 1; $i <= 20; $i++) {
            $movie = new Movie();
            $movie->setTitle($faker->unique()->movieTitle);
            // On stock le slug
            $movie->setSlug($this->slugger->slugify($movie->getTitle()));
            $movie->setCreatedAt($faker->datetime);
            // Associons des genres au hasard
            // en s'assurant de ne pas avoir de doublons
            $genresAdded = [];
            $nbGenres = mt_rand(1,3);
            for($g = 1; $g <= $nbGenres; $g++) {
                do {
                    // On prend un genre au hasard dans la liste des genres
                    $randomGenre = $genresList[array_rand($genresList)];
                }
                while(in_array($randomGenre, $genresAdded));
                
                // On l'ajoute au film
                $movie->addGenre($randomGenre);
                $genresAdded[] = $randomGenre;
            }
            // On ajoute le film à la liste des films
            $moviesList[] = $movie;

            $manager->persist($movie);
        }

        // 200 personnes

        // Préparons un tableau pour stocker les personnes
        $personsList = [];
        for ($i = 1; $i <= 200; $i++) {
            $person = new Person();
            $person->setName($faker->unique()->firstName . ' '. $faker->lastName);
            $person->setCreatedAt(new \DateTime());
            // On persist
            $manager->persist($person);

            $personsList[] = $person;
        }

        // 100 castings
        for ($i = 0; $i < 100; $i++) {
            $casting = new Casting();
            $casting->setRole($faker->movieRole);
            $casting->setCreditOrder(mt_rand(1, 100));
            $casting->setCreatedAt(new \DateTime());
            // On va chercher un film au hasard dans la liste des films créée au-dessus
            $randomMovie = $moviesList[array_rand($moviesList)];
            $casting->setMovie($randomMovie);
            // On va chercher une personne au hasard dans la liste des personnes créée au-dessus
            $randomPerson = $personsList[array_rand($personsList)];
            $personsAdded[] = $randomPerson;
            $casting->setPerson($randomPerson);
            // On persist
            $manager->persist($casting);
        }

        // Departement
        // Préparons un tableau pour stocker les départements
        $departmentsList = [];
        for ($i = 0; $i < 5; $i++) {
            $department = new Department();
            $department->setName($faker->unique()->movieDepartment);
            $department->setCreatedAt(new \DateTime());
            $manager->persist($department);

            $departmentsList[] = $department;
        }

        // 25 Jobs
        // Préparons un tableau pour stocker les jobs
        $jobsList = [];
        for ($i = 0; $i < 25; $i++) {
            $job = new Job();
            // On recupérer un job depuis notre provider
            $movieJob = $faker->unique()->movieJob;
            $job->setName($movieJob['name']);
            // On va chercher le department qui correspond au job ajouté
            foreach($departmentsList as $department) {
                $departmentName = $department->getName();
                if ($departmentName === $movieJob['department']) {
                    $jobDepartment = $department;
                }
            }
            $job->setDepartment($jobDepartment);
            $job->setCreatedAt(new \DateTime());
            $manager->persist($job);

            $jobsList[] = $job;
        }

        // Les rôles 'en dur'
        $roleAdmin = new Role();
        $roleAdmin->setName('ROLE_ADMIN');
        $roleAdmin->setLabel('Administrateur');
        $manager->persist($roleAdmin);

        $roleUser = new Role();
        $roleUser->setName('ROLE_USER');
        $roleUser->setLabel('Utilisateur');
        $manager->persist($roleUser);

        // Nos users 'en durs'
        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setPassword($this->encoder->encodePassword($admin, 'admin'));
        $admin->addUserRole($roleAdmin);
        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@user.com');
        $user->setPassword($this->encoder->encodePassword($user, 'user'));
        $user->addUserRole($roleUser);
        $manager->persist($user);

        $manager->flush();
    }
}
