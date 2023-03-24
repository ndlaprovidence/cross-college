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

        $qb = $entityManager->createQueryBuilder();

        $qb->select('s.id', 's.lastname', 's.firstname', 'g.shortname', 'g.level', 's.gender', 'r.chronometre')
            ->from('App\Entity\Ranking', 'r')
            ->join('r.student', 's')
            ->join('s.grade', 'g');

        if (!empty($grade)) {
            $qb->andWhere('g.shortname = :grade')
                ->setParameter('grade', $grade);
        }

        if (!empty($level)) {
            $qb->andWhere('g.level = :level')
                ->setParameter('level', $level);
        }

        if (!empty($gender)) {
            $qb->andWhere('s.gender = :gender')
                ->setParameter('gender', $gender);
        }

        $qb->orderBy('r.chronometre', 'ASC');

        $query = $qb->getQuery();

        return $query->getResult();
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
