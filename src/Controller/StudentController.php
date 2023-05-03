<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/student')]
class StudentController extends AbstractController
{

    #[Route('/barcode', name: 'app_student_barcode', methods: ['GET'])]
    public function barcode(StudentRepository $studentRepository)
    {

        $pdf = new \TCPDF();

        // add a page
        $pdf->AddPage();

        // set style for barcode
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 8,
            'stretchtext' => 4
        );

        $students = $studentRepository->findBy([], ['grade' => 'DESC']);
        $year = date("y");

        $i = 0;
        $y = 0;
        foreach ($students as $key => $student) {

            // $id = $student->getGender() . "-" . $year . "-" . $student->getFirstname()[0] . $student->getLastname()[0] . "-" . sprintf("%04d", $student->getId());
            $id = sprintf("%04d", $student->getId());
            $name = $student->getFirstname() . "-" . $student->getLastname() . "-" .  $student->getGrade()->getShortname();

            // for ($i=0; $i<=$students; $i++)
            // {
            // $id = $students[$i]->getGender() ."-". $year ."-". $students[$i]->getFirstname()[0]. $students[$i]->getLastname()[0] ."-". "000".$students[$i]->getId();
            // }

            //$pdf->Cell(0, 0, "sexe = '" . $students[0]->getGender()."'", 0, 1);
            //$pdf->Cell(0, 0, "année = '" . $year."'", 0, 1);
            //$pdf->Cell(0, 0, "initiale prénom = '" . $students[0]->getFirstname()[0]."'", 0, 1);
            //$pdf->Cell(0, 0, "initiale nom = '" . $students[0]->getLastname()[0]."'", 0, 1);
            //$pdf->Cell(0, 0, "numéro = '" . $students[0]->getId()."'", 0, 1);
            //$pdf = "F-22-EK-0001";

            if (($i % 2) == 0) {
                $y = $pdf->GetY();
                if ($y > 240) {
                    $pdf->AddPage();
                    $y = 10;
                }
                $pdf->Cell(0, 0, $name, 0, 1);
                $pdf->write1DBarcode($id, 'C39', '', '', '', 25, 0.7, $style, 'N');
                $pdf->Ln(4);
            } else {
                $pdf->SetY($y);
                $pdf->Cell(0, 0, $name, 0, 1, 'R');
                $pdf->write1DBarcode($id, 'C39', '120', '', '', 25, 0.7, $style, 'N');
                $pdf->Ln(4);
            }
            $i++;
        }

        return $pdf->Output('code_barre.pdf', 'I');
        //return $this->renderForm('student/barcode.html.twig');
    }

    #[Route('/', name: 'app_student_index', methods: ['GET'])]
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('student/index.html.twig', [
            'students' => $studentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_student_new', methods: ['GET', 'POST'])]
    public function new(Request $request, StudentRepository $studentRepository): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $studentRepository->save($student, true);

            return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('student/new.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_student_show', methods: ['GET'])]
    public function show(Student $student, StudentRepository $studentRepository): Response
    {
        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_student_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Student $student, StudentRepository $studentRepository): Response
    {
        $form = $this->createForm(StudentType::class, $student);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $studentRepository->save($student, true);

            return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('student/edit.html.twig', [
            'student' => $student,
            'form' => $form,
        ]);
    }

    #[Route('/delete-all', name: 'app_student_delete_all', methods: ['GET', 'POST'])]
    public function deleteAll(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('confirm', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);

        $entityManager->createQueryBuilder()
            ->delete(Student::class)
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'Tous les étudiants ont été supprimés avec succès.');
    
        return $this->redirectToRoute('app_student_index');
    }

    #[Route('/{id}', name: 'app_student_delete', methods: ['POST'])]
    public function delete(Request $request, Student $student, StudentRepository $studentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $student->getId(), $request->request->get('_token'))) {
            $studentRepository->remove($student, true);
        }

        return $this->redirectToRoute('app_student_index', [], Response::HTTP_SEE_OTHER);
    }
}
