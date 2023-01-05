<?php

namespace App\Repository;

use App\Entity\Ranking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ranking>
 *
 * @method Ranking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ranking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ranking[]    findAll()
 * @method Ranking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ranking::class);
    }

    public function save(Ranking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Ranking $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * @return array Returns an array of Ranking objects
    */
    public function findStudentsWithGrades(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT tbl_student.id, tbl_student.lastname, tbl_student.firstname, tbl_student.gender, tbl_grade.shortname, tbl_grade.`level`
            FROM tbl_student, tbl_grade
            WHERE tbl_student.grade_id=tbl_grade.id
            ';
        $stmt = $conn->prepare($sql);
        // $resultSet = $stmt->executeQuery(['price' => $price]);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function submit(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT tbl_student.id, tbl_student.lastname, tbl_student.firstname, tbl_student.gender, tbl_grade.shortname, tbl_grade.`level`
            FROM tbl_student, tbl_grade
            WHERE tbl_student.grade_id=tbl_grade.id
            ';
         
        $stmt = $conn->prepare($sql);
        // $resultSet = $stmt->executeQuery(['price' => $price]);
        $resultSet = $stmt->executeQuery();

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();

        while ($donnees = $response->fetch())
        {
             echo $donnees['grades'];
        }
        $reponse->closeCursor();
    }

//    /**
//     * @return Ranking[] Returns an array of Ranking objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Ranking
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
