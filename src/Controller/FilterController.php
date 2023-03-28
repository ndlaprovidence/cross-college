<?php

namespace App\Controller;

use PDO;
use DateTime;
use PDOException;
use App\Repository\RunRepository;
use App\Repository\GradeRepository;
use App\Repository\FilterRepository;
use App\Repository\RankingRepository;
use App\Repository\StudentRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
    public function index(Request $request, FilterRepository $filterRepository, GradeRepository $gradeRepository, StudentRepository $studentRepository, RunRepository $runRepository, RankingRepository $rankingRepository): Response
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
            'levels' => $levels,
            'level_checked' => $level,
            'genders' => $genders,
            'gender_checked' => $gender,
            'error_message' => $error_message,
        ]);
    }

    #[Route('/export', name: 'app_export')]
    public function export(Request $request, FilterRepository $filterRepository)
    {
        // TODO: exporter le tableau filtré au format Excel
        $grade = $request->query->get('grades');
        $level = $request->query->get('levels');
        $gender = $request->query->get('genders');

        $rows = $filterRepository->findStudentsWithGrades($grade, $level, $gender);

        // créer un nouveau classeur
        $spreadsheet = new Spreadsheet();

        // sélectionner la feuille active
        $sheet = $spreadsheet->getActiveSheet();

        // ajouter les en-têtes de colonne
        $sheet->setCellValue('A1', 'Classement');
        $sheet->setCellValue('B1', 'Elève');
        $sheet->setCellValue('C1', 'Classe');
        $sheet->setCellValue('D1', 'Catégorie');
        $sheet->setCellValue('E1', 'Genre');
        $sheet->setCellValue('F1', 'Chronomètre');

        // ajouter les données
        $i = 2;
    foreach ($rows as $row) {
        $sheet->setCellValue('A' . $i, $row['classement']);
        $sheet->setCellValue('B' . $i, $row['firstname'] . ' ' . $row['lastname']);
        $sheet->setCellValue('C' . $i, $row['shortname']);
        $sheet->setCellValue('D' . $i, $row['level']);
        $sheet->setCellValue('E' . $i, $row['gender']);
        $sheet->setCellValue('F' . $i, $row['chronometre']->format('H:i:s'));
        $i++;
    }

    // générer le fichier Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('export.xlsx');

    // envoyer le fichier Excel en réponse à la requête
    $response = new BinaryFileResponse('export.xlsx');
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export.xlsx');
    return $response;
    }
}
