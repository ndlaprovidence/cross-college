<?php

namespace App\Controller;

use PhpSerial;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Ranking2Controller extends AbstractController
{
    /**
     * @Route("/ranking2", name="ranking2")
     */
    public function createRanking2Action(Request $request)
    {
        $barcode = "";
        // Création du formulaire
        $form = $this->createFormBuilder()
            ->add('barcode', TextType::class, [
                'label' => 'Barcode',
                'attr' => [
                    'readonly' => false,
                ],
                'constraints' => [
                    new Length(['min' => 3, 'max' => 4])
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        // Traitement du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $barcode = $data['barcode'];        
            // Do something with the submitted data, e.g. save it to a database

            // Redirect to a success page
            // return $this->redirectToRoute('/ranking2');
        }

        // Affichage du formulaire
        return $this->render('ranking2/index.html.twig', [
            'form' => $form->createView(),
            'barcode' => $barcode,
        ]);
    }
}
