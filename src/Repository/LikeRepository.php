<?php

// src/Repository/LikeRepository.php
namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function findOneByUserAndPost($userId, $postId): ?Like
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.user = :userId')
            ->andWhere('l.post = :postId')
            ->setParameter('userId', $userId)
            ->setParameter('postId', $postId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
