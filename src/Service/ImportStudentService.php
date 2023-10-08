<?php

namespace App\Service;
use DateTime;
use App\Entity\Grade;
use League\Csv\Reader;
use App\Entity\Student;
use App\Repository\GradeRepository;
use App\Repository\StudentRepository;
use App\Service\ImportStudentService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Constraint\IsEmpty;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportStudentService
{
    private StudentRepository $studentRepository;
    private GradeRepository $gradeRepository;
    private EntityManagerInterface $em;

    public function __construct( StudentRepository $studentRepository,  GradeRepository $gradeRepository, EntityManagerInterface $em)
    {
        $this->studentRepository = $studentRepository;
        $this->gradeRepository = $gradeRepository;
        $this->em = $em;
    }

    public function importStudentFromCli(SymfonyStyle $io): void
    {
        $io->title('Importation des élèves');

        $arrayStudents = $this->readCsvFile();

        $io->progressStart(count($arrayStudents));

        foreach ($arrayStudents as $arrayStudent) {
            $io->progressAdvance();
            $student = $this->createOrUpdateStudent($arrayStudent);
            $this->em->persist($student);
        }
        $this->em->flush();

        $io->progressFinish();

        $io->success('Importation terminée');
    }

    public function importStudentFromWeb(String $csv): void
    {
        $arrayStudents = $this->readCsvFile($csv);

        foreach ($arrayStudents as $arrayStudent) {
            $student = $this->createOrUpdateStudent($arrayStudent);
            $this->em->persist($student);
        }
        $this->em->flush();

    }

    private function readCsvFile($csvFileName = ""): Reader
    {
        if (empty($csvFileName)) {
            $csv  = Reader::createFromPath('%kernel.root.dir%/../public/upload/liste_eleves.csv', 'r');
        }
        else {
            $csv  = Reader::createFromPath($csvFileName, 'r');
        }
        $csv->setHeaderOffset(0);
        //$csv->setDelimiter(';');
        return $csv;
    }

    private function createOrUpdateStudent(array $arrayStudent): Student
    {
        $student = $this->studentRepository->findOneBy(['lastname' => $arrayStudent['Nom']]);
        if (!$student) {
            $student = new Student();
        }
        $student->setLastname($arrayStudent['Nom'])
            ->setFirstname($arrayStudent['Prénom'])
            ->setGender($arrayStudent['SEXE'])
            ->setMas(floatval($arrayStudent['VMA']));
        
        $objective = DateTime::createFromFormat('H:i:s', $arrayStudent['OBJECTIVE']);
        if ($objective !== false) {
            $student->setObjective($objective);
        }

        $grade = $this->gradeRepository->findOneBy(['shortname' => $arrayStudent['CLASSE']]);
        if (!$grade) {
            $grade = new Grade();
            $grade->setShortname($arrayStudent['CLASSE']);
            $grade->setlevel(intval($arrayStudent['CLASSE'][0]));
            $this->em->persist($grade);        
            $this->em->flush();
        }
        $student->setGrade($grade);
        return $student;
    }
}