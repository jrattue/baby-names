<?php

namespace App\Repository;

use App\Entity\Year;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Year|null find($id, $lockMode = null, $lockVersion = null)
 * @method Year|null findOneBy(array $criteria, array $orderBy = null)
 * @method Year[]    findAll()
 * @method Year[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class YearRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Year::class);
    }

    /**
     * @return array<array>
     */
    public function getTop(string $gender, int $year): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT
            n.name, 
            n.gender, 
            y.rank, 
            y.count
        FROM
            year as y
        JOIN name as n on
            n.id = y.name_id 
        where
            y.`year` = :year AND
            y.`rank` > 0 AND
            n.gender = :gender
        ORDER BY y.`rank` ASC 
        LIMIT 50";

        $res = $conn->executeQuery($sql, [
            'gender'=> $gender,
            'year' => $year
        ]);
        return $res->fetchAllAssociative();

    }

    /**
     * @return array<array>
     */
    public function getTopForLetter(string $letter, int $year, string $gender): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT
            n.name, 
            n.gender, 
            y.rank, 
            y.count
        FROM
            `year` as y
        JOIN `name` as n on
            n.id = y.name_id 
        where
            y.`year` = :year AND
            y.`rank` > 0 AND
            n.gender = :gender AND 
            n.name LIKE :letter
        ORDER BY y.`rank` ASC 
        LIMIT 20";

        $letterLike = $letter."%";

        $res = $conn->executeQuery($sql, [
            'gender' => $gender,
            'year' => $year,
            'letter' => $letterLike,
        ]);

        return $res->fetchAllAssociative();
    }
    
    // /**
    //  * @return Year[] Returns an array of Year objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('y.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Year
    {
        return $this->createQueryBuilder('y')
            ->andWhere('y.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
