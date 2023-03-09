<?php

namespace App\Controller;

use PDO;
use PDOException;
use App\Repository\GradeRepository;
use App\Repository\FilterRepository;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FilterController extends AbstractController
{

    #[Route('/filter', name: 'app_filter')]
    public function index(FilterRepository $filterRepository, GradeRepository $gradeRepository, StudentRepository $studentRepository): Response
    {
        $error_message = "";
        $rows = array();

        $rows = $filterRepository->findStudentsWithGrades();
        $grades = $gradeRepository->findAll();
        $students = $studentRepository->findAll();

        return $this->render('filter/index.html.twig', [
            'rows' => $rows,
            'grades' => $grades,
            'students' => $students,
            'levels' => array(6, 5, 4, 3),
            'genders' => array('F', 'G'),
            'error_message' => $error_message,
        ]);
    }
    public function new(Request $request): Response
    {
        // creates a task object and initializes some data for this example
        $task = new Task();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($task)
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();

        // ...
    }
}
