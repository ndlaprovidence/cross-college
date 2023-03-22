<?php

namespace App\Controller;

use PDO;
use DateTime;
use PDOException;
use App\Repository\RunRepository;
use App\Repository\GradeRepository;
use App\Repository\FilterRepository;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FilterController extends AbstractController
{
    /*
    #[Route('/ranking', name: 'app_ranking')]
    public function index(): Response
    {
    $error_message = "";
    $rows = array();
    $dbserver = $this->getParameter("dbserver");
    $dbport = $this->getParameter("dbport");
    $dbname = $this->getParameter("dbname");
    $dbuser = $this->getParameter("dbuser");
    $dbpassword = $this->getParameter("dbpassword");        
    try{
    $connexion = new PDO("mysql:host=$dbserver;port=$dbport;dbname=$dbname", $dbuser, $dbpassword);
    
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sth = $connexion->prepare("SELECT * from tbl_student");
    $sth->execute();
    //print("Récupération de toutes les lignes d'un jeu de résultats :\n");
    $rows = $sth->fetchAll();
    //dump($rows);
    }
    catch(PDOException $e){
    $error_message = $e->getMessage();
    }
    return $this->render('ranking/index.html.twig', [
    'rows' => $rows,
    'error_message' => $error_message,
    ]);
    }
    */

    #[Route('/filter', name: 'app_filter')]
    public function index(Request $request, FilterRepository $filterRepository, GradeRepository $gradeRepository, StudentRepository $studentRepository, RunRepository $runRepository): Response
    {
        $error_message = "";
        $rows = array();

        $rows = $filterRepository->findStudentsWithGrades();
        $grades = $gradeRepository->findAll();
        $students = $studentRepository->findAll();
        $levels = array(6, 5, 4, 3);
        $genders = array('F', 'G');

        if ($request->isMethod('GET')) {
            $grade = $request->query->get('grades');
            $level = $request->query->get('levels');
            $gender = $request->query->get('genders');

            $run = $runRepository->getLast();
            $startDateTime = $run->getStart();
            $start = $startDateTime->format("Y-m-d H:i:s");
            $chronometres = array();
            foreach ($rows as $row) {
                $endDateTime = $row->getEnd();
                $end = $endDateTime->format("Y-m-d H:i:s");
                $endDateTime = \DateTime::createFromFormat("Y-m-d H:i:s", $end);

                $diffChronometre = $startDateTime->diff($endDateTime);

                $hoursChronometre = $diffChronometre->h;
                $minutesChronometre = $diffChronometre->i;
                $secondsChronometre = $diffChronometre->s;

                $chronometre = sprintf("1970-01-01 %02d:%02d:%02d", $hoursChronometre, $minutesChronometre, $secondsChronometre);
            }
            // $criteria = array();

            if (!empty($grade) || !empty($level) || !empty($gender)) {
                $rows = $filterRepository->findStudentsWithGrades($grade, $level, $gender);
            } else {
                $rows = $filterRepository->findStudentsWithGrades();
            }
        }

        return $this->render('filter/index.html.twig', [
            'rows' => $rows,
            'grades' => $grades,
            'grade_checked' => $grade,
            'students' => $students,
            'levels' => array(6, 5, 4, 3),
            'level_checked' => $level,
            'genders' => array('F', 'G'),
            'gender_checked' => $gender,
            'error_message' => $error_message,
            'chronometres' => $chronometres,

        ]);
    }
}
