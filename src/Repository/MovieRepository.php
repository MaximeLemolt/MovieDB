<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    // /**
    //  * @return Movie[] Returns an array of Movie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Affiche tous les films par ordre alphabétique (DQL)
     */
    public function getMoviesOrderedByTitleAscDql()
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT m
            FROM App\Entity\Movie m
            ORDER BY m.title ASC'
        );

        return $query->getResult();
    }

    /**
     * Affiche tous les films par ordre alphabétique (Query Builder)
     * 
     * @param string $search Mot-clé de recherche
     */
    public function getMoviesOrderedByTitleAscQb($search = null)
    {
        // Requête de base
        $qb =  $this->createQueryBuilder('m')
                    ->orderBy('m.title', 'ASC');

        // Si recherche en plus
        if ($search !== null) {
            $qb->where('m.title LIKE :search')
            ->setParameter('search', '%'.$search.'%');
        }

        return $qb->getQuery()->getResult();
    }
}
