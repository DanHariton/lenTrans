<?php

namespace App\Repository;

use App\Entity\RoomParticipant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
     * @param $roomId
     * @return int|mixed|string
     */
    public function findAllByRoomId($roomId)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.room = :roomId')
            ->setParameter('roomId', $roomId)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param $userId
     * @param $roomId
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findOneByUserId($userId, $roomId)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.user = :userId')
            ->andWhere('r.room = :roomId')
            ->setParameter('userId', $userId)
            ->setParameter('roomId', $roomId)
            ->getQuery()
            ->getOneOrNullResult()
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
