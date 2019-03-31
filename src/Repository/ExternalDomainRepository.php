<?php

namespace App\Repository;

use App\Entity\ExternalDomain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExternalDomain|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExternalDomain|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExternalDomain[]    findAll()
 * @method ExternalDomain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExternalDomainRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExternalDomain::class);
    }

    public function tableSize()
    {
        return $this->createQueryBuilder('q')
            ->select('count(q.id) as total')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return ExternalDomain[] Returns an array of ExternalDomain objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExternalDomain
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
