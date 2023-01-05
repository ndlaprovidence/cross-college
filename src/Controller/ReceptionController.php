<?php

namespace App\Controller;

use PDO;
use PDOException;
use App\Entity\Run;
use App\Entity\Ranking;
use App\Entity\Student;
use App\Repository\RunRepository;
use App\Repository\RankingRepository;
use App\Repository\StudentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/reception')]
class ReceptionController extends AbstractController
{
    #[Route('/', name: 'app_reception_index')]
    public function index(RunRepository $runRepository, RankingRepository $rankingRepository, StudentRepository $studentRepository): Response
    {
        $dbserver = $this->getParameter("dbserver");
        $dbport = $this->getParameter("dbport");
        $dbname = $this->getParameter("dbname");
        $dbuser = $this->getParameter("dbuser");
        $dbpassword = $this->getParameter("dbpassword");

        date_default_timezone_set('Europe/Paris');
        $message = "La course n'a pas encore démarré";

        try {
            $connexion = new PDO("mysql:host=$dbserver;port=$dbport;dbname=$dbname", $dbuser, $dbpassword);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (isset($_GET["identifiant"])) {
                $identifiant = $_GET["identifiant"];
                $id = intval(substr($identifiant, 8));
                $end = date("Y-m-d H:i:s");
                
                // Recupérer l'heure de départ
                $run = $runRepository->getLast();    
                $startDateTime = $run->getStart();     
                $start = $startDateTime->format("Y-m-d H:i:s");
                $message = "L'élève est arrivé a " . $end . " !";
                error_log("Heure de départ = '" . $start . "'");
                error_log("L'élève avec le dossard n° " . $identifiant . " vient d'arriver à " . $end);

                // Enregistrer l'heure d'arrivée de cet élève
                $student = $studentRepository->find($id);
                $start = $runRepository->find($start);
                $ranking = new Ranking();
                $ranking->setStudent($student);
                $ranking->setEnd(new \DateTime($end));
                $ranking->setRun($start);
                $rankingRepository->save($ranking, true);             



                // $requete = "INSERT INTO `tbl_ranking`( `id`, `end`) VALUES(:identifiant, :end)";
                // $stmt = $connexion->prepare($requete);
                // $stmt->bindParam(':identifiant', $identifiant);
                // $stmt->bindParam(':end', $end);
                // error_log($requete);
                // $stmt->execute();
            } else {
                $start = date("Y-m-d H:i:s");
                $message = "La course a démarré le " . $start . " !";
                error_log("La course a démarrée à " . $start . " !");
                // Enregistrer l'heure de départ de la course
                $run = new Run();
                $run->setStart(new \DateTime($start));
                $runRepository->save($run, true);             
            }
        } catch (PDOException $e) {
            error_log('Erreur : ' . $e->getMessage());
        }

        return $this->render('reception/index.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/back', name: 'app_reception_back')]
    public function back(): Response
    {
        $dbserver = $this->getParameter("dbserver");
        $dbport = $this->getParameter("dbport");
        $dbname = $this->getParameter("dbname");
        $dbuser = $this->getParameter("dbuser");
        $dbpassword = $this->getParameter("dbpassword");

        date_default_timezone_set('Europe/Paris');
        $message = "La course n'a pas encore démarré";

        try {
            $connexion = new PDO("mysql:host=$dbserver;port=$dbport;dbname=$dbname", $dbuser, $dbpassword);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            if (isset($_GET["identifiant"])) {
                $identifiant = $_GET["identifiant"];
                $end = date("Y-m-d H:i:s");
                $requete = "SELECT start FROM tbl_run ORDER BY id DESC LIMIT 1";
                $student_id = "SELECT tbl_student.id ";
                $stm = $connexion->query($requete);
                $result = $stm->fetch();
                $start = $result[0];
                error_log("Heure de départ = '" . $start . "'");
                error_log("L'élève avec le dossard n° " . $identifiant . " vient d'arriver à " . $end);
                // Enregistrer l'heure d'arrivée de cet élève
                //$requete = "INSERT INTO eleve (identifiant) VALUES ('$identifiant');";
                $requete = "INSERT INTO `tbl_ranking`( `id`, `end`) VALUES(:identifiant, :end)";
                $stmt = $connexion->prepare($requete);
                $stmt->bindParam(':identifiant', $identifiant);
                $stmt->bindParam(':end', $end);
                error_log($requete);
                $stmt->execute();
            } else {
                $start = date("Y-m-d H:i:s");
                $message = "La course a démarré le " . $start . " !";
                //error_log("La course a démarrée à " . $start . " !");
                // Enregistrer l'heure de départ de la course
                $requete = "INSERT INTO `tbl_run`( `start`) VALUES(:start)";
                $stmt = $connexion->prepare($requete);
                $stmt->bindParam(':start', $start);
                error_log($requete);
                $stmt->execute();
            }
        } catch (PDOException $e) {
            error_log('Erreur : ' . $e->getMessage());
        }

        return $this->render('reception/index.html.twig', [
            'message' => $message,
        ]);
    }




}
