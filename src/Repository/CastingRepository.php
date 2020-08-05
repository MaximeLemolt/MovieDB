<?php

namespace App\Repository;

use App\Entity\Casting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Casting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casting[]    findAll()
 * @method Casting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CastingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casting::class);
    }

    // /**
    //  * @return Casting[] Returns an array of Casting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Casting
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Affiche toutes les personnes d'un film (DQL)
     */
    public function getCastingsJoinedToPersonByMovieDql($movie)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT c, p
            FROM App\Entity\Casting c
            INNER JOIN c.person p
            WHERE c.movie = :movie
            ORDER BY c.creditOrder ASC'
        )->setParameter('movie', $movie);

        return $query->getResult();
    }

    /**
     * Affiche toutes les personnes d'un film (QueryBuilde)
     */
    public function getCastingsJoinedToPersonByMovieQb($movie)
    {
        return $this->createQueryBuilder('c')
                    ->join('c.person', 'p')
                    ->addSelect('p')
                    ->andWhere('c.movie = :movie')
                    ->setParameter('movie', $movie)
                    ->orderBy('c.creditOrder', 'ASC')
                    ->getQuery()
                    ->getResult();
    }
}
