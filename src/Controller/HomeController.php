<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home.index', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
        // return $this->renderForm('home.html.twig', [
        //     'parcourir' => "quelque chose",
        // ]);
    }
}
