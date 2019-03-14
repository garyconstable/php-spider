<?php

namespace App\Repository;

use App\Entity\Pending;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pending|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pending|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pending[]    findAll()
 * @method Pending[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PendingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pending::class);
    }

    public function tableSize()
    {
        return $this->createQueryBuilder('q')
            ->select('count(q.id) as total')
            ->getQuery()
            ->getResult()
            ;
    }

    public function getBatch( $batchAmount = 10 )
    {
        return $this->createQueryBuilder('q')
            ->orderBy('q.id', 'ASC')
            ->setMaxResults( $batchAmount )
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Pending[] Returns an array of Pending objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pending
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
