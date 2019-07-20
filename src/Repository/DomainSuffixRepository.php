<?php

namespace App\Repository;

use App\Entity\DomainSuffix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DomainSuffix|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomainSuffix|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomainSuffix[]    findAll()
 * @method DomainSuffix[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainSuffixRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DomainSuffix::class);
    }

    // /**
    //  * @return DomainSuffix[] Returns an array of DomainSuffix objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DomainSuffix
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
