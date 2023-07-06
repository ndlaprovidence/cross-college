<?php

namespace App\Controller;

use PDO;
use PDOException;
use App\Repository\RunRepository;
use App\Repository\RankingRepository;
use App\Repository\StudentRepository;
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
    public function index(RunRepository $runRepository, RankingRepository $rankingRepository, StudentRepository $studentRepository): Response
    {
        $error_message = "";
        $message = "";
        $start = "";
        $chronometres = array();

        $rows = $rankingRepository->findAll();
        $run = $runRepository->getLast();

        if (isset($run)) {
            try {                
                $startDateTime = $run->getStart();
                $start = $startDateTime->format("Y-m-d H:i:s");
                $startfr = $startDateTime->format("d/m/Y H:i:s");
                $message = "La course a démarré le " . $startDateTime->format("d/m/Y") . " à " . $startDateTime->format("H:i:s");
            } catch (PDOException $e) {
                error_log('Erreur : ' . $e->getMessage());
            }            
            
            foreach ($rows as $row) {
                $endDateTime = $row->getEnd();
                $end = $endDateTime->format("Y-m-d H:i:s");
                $endDateTime = \DateTime::createFromFormat("Y-m-d H:i:s", $end);

                $diff = $startDateTime->diff($endDateTime);

                $hours = $diff->h;
                $minutes = $diff->i;
                $seconds = $diff->s;

                $chronometre = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
                $chronometres[$row->getStudent()->getId()] = $chronometre;
            }

            asort($chronometres);
        }

        return $this->render('ranking/index.html.twig', [
            'rows' => $rows,
            'error_message' => $error_message,
            'message' => $message,
            'start' => $start,
            'startfr' => $startfr,            
            'chronometres' => $chronometres,
        ]);
    }
}
