<?php

namespace App\Repository;

use App\Entity\JobListing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobListing>
 *
 * @method JobListing|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobListing|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobListing[]    findAll()
 * @method JobListing[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobListingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobListing::class);
    }

//    /**
//     * @return job_listing[] Returns an array of job_listing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('j.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?job_listing
//    {
//        return $this->createQueryBuilder('j')
//            ->andWhere('j.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
