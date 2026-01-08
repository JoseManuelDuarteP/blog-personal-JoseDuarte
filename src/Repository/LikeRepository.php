<?php

namespace App\Repository;

use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Like>
 */
class LikeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Like::class);
    }

    public function hasUserLikedImage($userId, $imageId): bool
    {
        $query = $this->createQueryBuilder('l')
            ->andWhere('l.user = :user')
            ->andWhere('l.image = :image')
            ->setParameter('user', $userId)
            ->setParameter('image', $imageId)
            ->getQuery();

        return $query->getOneOrNullResult() !== null;
    }
}
