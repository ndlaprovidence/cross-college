<?php

namespace App\Controller;

use PDO;
use PDOException;
use App\Repository\GradeRepository;
use App\Repository\FilterRepository;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FilterController extends AbstractController
{

    #[Route('/filter', name: 'app_filter')]
    public function index(Request $request, FilterRepository $filterRepository, GradeRepository $gradeRepository, StudentRepository $studentRepository): Response
{
    $error_message = "";
    $rows = array();

    $rows = $filterRepository->findStudentsWithGrades();
    $grades = $gradeRepository->findAll();
    $students = $studentRepository->findAll();
    $levels = array(6, 5, 4, 3);
    $genders = array('F', 'G');

    if ($request->isMethod('GET')) {
        $gradeShortname = $request->request->get('grades');
        $level = $request->request->get('levels');
        $gender = $request->request->get('genders');

        // $criteria = array();
    
        if (!empty($gradeShortname) || !empty($level) || !empty($gender)) {
            $rows = $filterRepository->findStudentsWithGrades($gradeShortname, $level, $gender);
        } else {
            $rows = $filterRepository->findStudentsWithGrades();
        }
    }

    return $this->render('filter/index.html.twig', [
        'rows' => $rows,
        'grades' => $grades,
        'students' => $students,
        'levels' => array(6, 5, 4, 3),
        'genders' => array('F', 'G'),
        'error_message' => $error_message,
    ]);
}
}
