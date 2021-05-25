<?php

namespace App\Repository;

use App\Entity\RoomParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RoomParticipant|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoomParticipant|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoomParticipant[]    findAll()
 * @method RoomParticipant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomParticipant::class);
    }

    /**
     * @param $userId
     * @return int|mixed|string
     */
    public function findByUserId($userId)
    {
        return $this->createQueryBuilder('rp')
            ->andWhere('rp.user_id = :userId')
            ->setParameter('userId', $userId)
            ->leftJoin('rp.room_id', 'room.id')
            ->orderBy('rp.id', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return RoomParticipant[] Returns an array of RoomParticipant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RoomParticipant
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
