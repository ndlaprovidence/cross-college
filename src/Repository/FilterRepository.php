<?php

namespace App\Repository;

use App\Entity\Ranking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PDOException;
use Doctrine\DBAL\Statement;

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
    public function findStudentsWithGrades(string $gradeShortname = null, int $level = null, string $gender = null): array
    {
        $entityManager = $this->getEntityManager();
        $conn = $entityManager->getConnection();
        $sql = $entityManager->createQuery = "SELECT student.id, student.lastname, student.firstname, grade.shortname, grade.level, student.gender

        FROM tbl_student AS student
        JOIN tbl_grade AS grade ON grade.id = student.grade_id
        WHERE 1 = 1";
        $params = array();

        if (!empty($gradeShortname)) {
            $sql .= " AND grade.shortname = ?";
            $params[] = $gradeShortname;
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
}

