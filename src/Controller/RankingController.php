<?php

namespace App\Controller;

use App\Repository\RankingRepository;
use PDO;
use PDOException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RankingController extends AbstractController
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

    #[Route('/ranking', name: 'app_ranking')]
    public function index(RankingRepository $rankingRepository): Response
    {
        $error_message = "";
        $rows = array();

        $rows = $rankingRepository->findAll();

        return $this->render('ranking/index.html.twig', [
            'rows' => $rows,
            'error_message' => $error_message,
        ]);
    }

}
