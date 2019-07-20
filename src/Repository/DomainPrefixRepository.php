<?php

namespace App\Repository;

use App\Entity\DomainPrefix;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DomainPrefix|null find($id, $lockMode = null, $lockVersion = null)
 * @method DomainPrefix|null findOneBy(array $criteria, array $orderBy = null)
 * @method DomainPrefix[]    findAll()
 * @method DomainPrefix[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainPrefixRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DomainPrefix::class);
    }

    // /**
    //  * @return DomainPrefix[] Returns an array of DomainPrefix objects
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
    public function findOneBySomeField($value): ?DomainPrefix
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
