<?php

namespace App\Controller;

use App\Service\ImportStudentService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ImportStudentController extends AbstractController
{
    #[Route('/import/student', name: 'app_import_student')]
    public function index(ImportStudentService $importStudentService): Response
    {
        $io = new SymfonyStyle($input, $output);
        $this->importStudentService->importStudent($io);

        return $this->render('import_student/index.html.twig', [
            'controller_name' => 'ImportStudentController',
        ]);
    }
}
