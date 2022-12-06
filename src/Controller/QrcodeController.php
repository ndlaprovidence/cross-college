<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QrcodeController extends AbstractController
{
    #[Route('/qrcode', name: 'app_qrcode')]
    public function index(): Response
    {
        return $this->render('qrcode/index.html.twig', [
            'controller_name' => 'QrcodeController',
        ]);
    }
}
