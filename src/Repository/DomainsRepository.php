<?php

namespace App\Repository;

use App\Entity\Domains;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Domains|null find($id, $lockMode = null, $lockVersion = null)
 * @method Domains|null findOneBy(array $criteria, array $orderBy = null)
 * @method Domains[]    findAll()
 * @method Domains[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DomainsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Domains::class);
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
    //  * @return Domains[] Returns an array of Domains objects
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
    public function findOneBySomeField($value): ?Domains
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
