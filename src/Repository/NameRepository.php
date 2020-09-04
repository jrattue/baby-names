<?php

namespace App\Repository;

use App\Entity\Name;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\FetchMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Name|null find($id, $lockMode = null, $lockVersion = null)
 * @method Name|null findOneBy(array $criteria, array $orderBy = null)
 * @method Name[]    findAll()
 * @method Name[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Name::class);
    }

    public function findForSearch($term)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT `id`, `name`, IF(`gender` = "M", "Male", "Female") as gender from name n where n.name LIKE :term LIMIT 15';

        $term = $term."%";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam('term', $term);
        $stmt->execute();
        return $stmt->fetchAll(FetchMode::ASSOCIATIVE);

    }

    // /**
    //  * @return Name[] Returns an array of Name objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Name
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}