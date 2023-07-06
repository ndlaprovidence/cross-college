<?php

namespace App\Controller;

use App\Entity\Grade;
use App\Entity\Race;
use App\Entity\Ranking;
use App\Entity\Run;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

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

    #[Route('/delete-all-grade', name: 'app_grade_delete_all', methods: ['GET', 'POST'])]
    public function deleteAllGrade(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('confirm', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);

        $entityManager->createQueryBuilder()
            ->delete(Grade::class)
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'Toutes les classes ont été supprimés avec succès.');

        return $this->redirectToRoute('home.index');
    }

    #[Route('/delete-all-race', name: 'app_race_delete_all', methods: ['GET', 'POST'])]
    public function deleteAllRace(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('confirm', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);

        $entityManager->createQueryBuilder()
            ->delete(Race::class)
            ->getQuery()
            ->execute();

        $entityManager->createQueryBuilder()
            ->delete(Run::class)
            ->getQuery()
            ->execute();            

        $this->addFlash('success', 'Toutes les courses ont été supprimés avec succès.');

        return $this->redirectToRoute('home.index');
    }

    #[Route('/delete-all-ranking', name: 'app_ranking_delete_all', methods: ['GET', 'POST'])]
    public function deleteAllRanking(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('confirm', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);

        $entityManager->createQueryBuilder()
            ->delete(Ranking::class)
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'Tous les classements ont été supprimés avec succès.');

        return $this->redirectToRoute('home.index');
    }

    #[Route('/delete-all-run', name: 'app_run_delete_all', methods: ['GET', 'POST'])]
    public function deleteAllRun(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder()
            ->add('confirm', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);

        $entityManager->createQueryBuilder()
            ->delete(Run::class)
            ->getQuery()
            ->execute();

        $this->addFlash('success', 'Toutes les courses ont été supprimés avec succès.');

        return $this->redirectToRoute('home.index');
    }

    #[Route('/delete-all-kidrun', name: 'app_kidrun_delete_all', methods: ['GET', 'POST'])]
    public function deleteAllKidrun(Request $request, EntityManagerInterface $entityManager, Connection $connection): Response
    {
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');
        $schemaManager = $connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();
        foreach ($tables as $table) {
            if ($table != "tbl_user") {
                $connection->executeStatement($platform->getTruncateTableSQL($table));
            }
        }
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');

        $this->addFlash('success', 'Toutes les tables ont été supprimées avec succès.');

        return $this->redirectToRoute('home.index');
    }
}
