<?php

namespace App\Repository;

use App\Entity\Queue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Queue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Queue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Queue[]    findAll()
 * @method Queue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QueueRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Queue::class);
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
    //  * @return Queue[] Returns an array of Queue objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('q.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Queue
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
