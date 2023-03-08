<?php

namespace App\Controller;

use App\Entity\Student;
use App\Repository\RunRepository;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DouchetteController extends AbstractController
{
    #[Route('/douchette', name: 'app_douchette')]
    public function createDouchetteAction(Request $request, StudentRepository $studentRepository, RunRepository $runRepository, ManagerRegistry $doctrine)
    {
        $identifiant = "";
        $form = $this->createFormBuilder()
            ->add('identifiant', TextType::class, [
                'label' => 'Barcode',
                'attr' => [
                    'readonly' => false,
                ],
                'constraints' => [
                    new Length(['min' => 1, 'max' => 4])
                ],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $identifiant = $data['identifiant'];        
            // return $this->redirectToRoute('/Douchette');
        }

        $personne = $doctrine->getRepository(Student::class)->findOneBy(['id' => $identifiant]);

        $run = $runRepository->getLast();    
        $startDateTime = $run->getStart();     
        $start = $startDateTime->format("Y-m-d H:i:s");
        $message = "La course a démarré le " . $start . "";

        $error_message = "";

        return $this->render('douchette/index.html.twig', [
            'form' => $form->createView(),
            'identifiant' => $identifiant,
            'student' => $personne,
            'message' => $message,
            'error_message' => $error_message,
        ]);
    }
}
