<?php

namespace App\Repository\Admin;

use App\Entity\Admin\Rezervation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rezervation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rezervation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rezervation[]    findAll()
 * @method Rezervation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RezervationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rezervation::class);
    }

    // /**
    //  * @return Rezervation[] Returns an array of Rezervation objects
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
    public function findOneBySomeField($value): ?Rezervation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getUserrezervation($id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT r.*,h.title as hname,u.title as rname FROM rezervation r
            JOIN hotel h ON h.id = r.hotelid
            JOIN room u ON u.id = r.roomid
            WHERE r.userid = :userid
            ORDER BY r.id DESC
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['userid' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    // *** lEFT JOIN WITH SQL ******************
    public function getrezervation($id): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT r.*,h.title as hname,u.title as rname, usr.name as username FROM rezervation r
            JOIN hotel h ON h.id = r.hotelid
            JOIN room u ON u.id = r.roomid
            JOIN user usr ON usr.id = r.userid
            WHERE r.id = :id
         ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    // *** lEFT JOIN WITH SQL ******************
    public function getrezervations($status): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT r.*,h.title as hname,u.title as rname, usr.name as username FROM rezervation r
            JOIN hotel h ON h.id = r.hotelid
            JOIN room u ON u.id = r.roomid
            JOIN user usr ON usr.id = r.userid
            WHERE r.status =:status
         ';
        $stmt = $conn->prepare($sql);
        $stmt->execute(['status' => $status]);

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }
}
