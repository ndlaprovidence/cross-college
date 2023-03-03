<?php

namespace App\Controller;

use PhpSerial;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Ranking2Controller extends AbstractController
{
    /**
     * @Route("/ranking2", name="ranking2")
     */
    public function index(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('barcode', TextType::class, ['required' => true])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire a été envoyé, faites ce que vous voulez avec le code-barres ici
            $barcode = $form->getData()['barcode'];
            //...
        }

        // Configuration de PHP Serial
        $serial = new PhpSerial;
        $serial->deviceSet('/dev/ttyUSB0'); // Remplacez ttyUSB0 par le port série approprié pour votre douchette
        $serial->confBaudRate(9600); // Configurez la vitesse de transmission appropriée pour votre douchette
        $serial->confParity('none');
        $serial->confCharacterLength(8);
        $serial->confStopBits(1);
        $serial->confFlowControl('none');
        $serial->deviceOpen();

        // Lecture en boucle des codes-barres
        while (true) {
            $barcode = '';
            while (true) {
                $char = $serial->readPort(1);
                if ($char == "\n") { // Le code-barres se termine généralement par un caractère de retour à la ligne
                    break;
                } else {
                    $barcode .= $char;
                }
            }
            $form = $this->createFormBuilder()
                ->add('barcode', TextType::class, ['required' => true, 'data' => $barcode])
                ->add('submit', SubmitType::class, ['label' => 'Envoyer'])
                ->getForm();
            break; // Sort de la boucle de lecture après avoir lu un code-barres
        }

        $serial->deviceClose();

        return $this->render('ranking2/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
