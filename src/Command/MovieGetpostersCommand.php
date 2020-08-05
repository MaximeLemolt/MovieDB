<?php

namespace App\Command;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MovieGetpostersCommand extends Command
{
    protected static $defaultName = 'app:movie:getposters';

    private $movieRepository;

    private $entityManager;

    /**
     * On récupère nos services via le constructeur
     * (car la commande est elle aussi un service)
     */
    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $entityManager)
    {
        $this->movieRepository = $movieRepository;
        $this->entityManager = $entityManager;

        // On  doit appeller le constructeur du parent qui contient du code si non exécuté => bug
        // à cause de l'override
        parent::__construct();
    }

    /**
     * Configuration de la commande
     */
    protected function configure()
    {
        $this
            ->setDescription('Download movie poster from OMDBAPI')
            //->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('dump', 'd', InputOption::VALUE_NONE, 'Display movie informations')
        ;
    }

    /**
     * Que fait la commande
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // 1. Aller chercher les films depuis la BDD
        $movies = $this->movieRepository->findAll();
        // 2. Parcourir chaque film
        foreach($movies as $movie) {
            // 3. Pour chaque film, lire le JSON depuis OMDBAPI avec la clé
            // 4. Lire l'attribut 'Poster'
            $url = $this->getPosterUrlFromMovie($movie);

            // 5. Télécharger l'image en local (dans le dossier public par ex.)
            if ($url !== null) {
                $filename = $this->downloadFromUrl($url, $movie->getId());
            } else {
                $filename = null;
            }

            // 6. Mettre à jour l'entité Movie avec son nom d'image
            $movie->setPoster($filename);

            // Dump
            if ($input->getOption('dump')) {
                $io->text($movie->getTitle() . ' image=' . $filename);
            }
    
        }
        // On flush tous les films
        $this->entityManager->flush();

        // echo 'Ceci est une ligne' . PHP_EOL;

        $io->title('Listing all movies');
        $io->success('Posters downloaded.');

        return 0;
    }

    /**
     * Récupère l'url du Poster
     * 
     * @param Movie $movie Le film concerné
     * 
     * @return string|null L'URL du poster du film recherché ou null
     */
    public function getPosterUrlFromMovie(Movie $movie) : ?string
    {
        // urlencode() permet d'encoder une chaine au format URL
        $titleToSearch = urlencode($movie->getTitle());
        // On crée l'URL de destination JSON à aller chercher
        $url = 'http://www.omdbapi.com/?t=' . $titleToSearch . '&apikey=85491ae8';
        // On va lire le contenu via
        // https://www.php.net/manual/fr/function.file-get-contents.php
        $responseContent = file_get_contents($url);
        // On decode ce contenu en JSON
        // JSON => PHP Object
        $json = json_decode($responseContent);
        // Si pas de réponse => null
        // OU si résultat ET poster non disponible => null
        if ($json->Response == 'False' || ($json->Response == 'True' && $json->Poster == 'N/A')) {
            return null;
        }
        // Sinon, retourne l'url du poster
        return $json->Poster;
    }

    /**
     * Télécharge l'image de puis l'URL
     * 
     * @param string $url URL de l'image
     * @param int $movieId ID du film en BDD
     * 
     * @return string Nom du fichier image téléchargé
     */
    public function downloadFromUrl(string $url, int $movieId) : string
    {
        // On recupère l'extension dynamiquement
        $extension = pathinfo($url, PATHINFO_EXTENSION);

        // On définit le nom du fichier avec son extension d'origine
        $filename = 'movie' . $movieId . '.' . $extension;

        // On sauvegarde l'image sur le serveur, si non existante
        // On pourrait avoir une option --overwrite pour gérer ça
        if (!file_exists('public/uploads/posters/' . $filename)) {
            file_put_contents('public/uploads/posters/' . $filename, file_get_contents($url));
        }
        

        return $filename;
    }
}
