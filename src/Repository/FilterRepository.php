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
class FilterRepository extends ServiceEntityRepository
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
    public function findStudentsWithGrades(string $grade = null, string $level = null, string $gender = null): array
    {
        $entityManager = $this->getEntityManager();
        $conn = $entityManager->getConnection();
        $sql = $entityManager->createQuery = "SELECT student.id, student.lastname, student.firstname, grade.shortname, grade.level, student.gender, ranking.end
    
        FROM tbl_student AS student
        JOIN tbl_grade AS grade ON grade.id = student.grade_id
        JOIN tbl_ranking AS ranking ON ranking.id = ranking.id
        WHERE 1 = 1";
        $params = array();

        if (!empty($grade)) {
            $sql .= " AND grade.shortname = ?";
            $params[] = $grade;
        }

        if (!empty($level)) {
            $sql .= " AND grade.level = ?";
            $params[] = $level;
        }

        if (!empty($gender)) {
            $sql .= " AND student.gender = ?";
            $params[] = $gender;
        }

        $sql .= " ORDER BY grade.shortname ASC, grade.level DESC, student.lastname ASC";
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery($params);

        return $resultSet->fetchAllAssociative();
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
