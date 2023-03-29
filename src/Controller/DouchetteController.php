<?php

namespace App\Controller;

use DateTime;
use PDOException;
use App\Entity\Run;
use App\Entity\Ranking;
use App\Entity\Student;
use App\Repository\RunRepository;
use App\Repository\RankingRepository;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DouchetteController extends AbstractController
 {
    #[ Route( '/douchette', name: 'app_douchette' ) ]

    public function createDouchetteAction( Request $request, StudentRepository $studentRepository, RunRepository $runRepository, ManagerRegistry $doctrine, RankingRepository $rankingRepository )
 {
        $identifiant = '';
        $form = $this->createFormBuilder()
        ->add( 'identifiant', TextType::class, [
            'label' => 'Code barres',
            'attr' => [
                'readonly' => false,
            ],
            'constraints' => [
                new Length( [
                    'min' => 1,
                    'max' => 4,
                    'maxMessage' => 'This value is too long. It should have 4 characters or less',
                ] ),
                new UpperCase(),
            ],
        ] )
        ->getForm();

        $form->handleRequest( $request );

        if ( $form->isSubmitted() && $form->isValid() ) {
            $data = $form->getData();
            $identifiant = $data[ 'identifiant' ];
        }
        return $this->handleBarcodeForm($identifiant, $studentRepository, $runRepository, $doctrine, $rankingRepository, $form );
    }
    
    private function handleBarcodeForm(string $identifiant, StudentRepository $studentRepository, RunRepository $runRepository, ManagerRegistry $doctrine, RankingRepository $rankingRepository, FormInterface $form )
    {
        $chronometre = '';
        $error_message = '';
        $success_message = '';
        $run_message = '';
        date_default_timezone_set( 'Europe/Paris' );
        $student = $studentRepository->find( $identifiant );

        if ( isset( $student ) ) {
            $run = $runRepository->getLast();
            // Récupération du dernier run
            $existingRanking = $rankingRepository->findOneBy( [
                'student' => $student
            ] );
            if ( $existingRanking !== null ) {
                // L'étudiant a déjà été ajouté à la table tbl_ranking pour cette course
                $error_message .= "Le coureur a déjà été ajouté.";
            } else {
                $success_message .= "Le coureur a été ajouté.";
                $end = date("Y-m-d H:i:s");
            
                $run = $runRepository->getLast();
                $startDateTime = $run->getStart();
                $start = $startDateTime->format("Y-m-d H:i:s");  
            
                $ranking = new Ranking();
                $ranking->setStudent($student);
                $ranking->setEnd(new \DateTime($end));
                $ranking->setRun($run);
                $rankingRepository->save($ranking, true);
            }

            $form = $this->createFormBuilder()
                ->add('identifiant', TextType::class, [
                    'label' => 'Code barres',
                    'attr' => [
                        'readonly' => false,
                    ],
                    'constraints' => [
                        new Length([
                            'min' => 1,
                            'max' => 4,
                            'maxMessage' => 'This value is too long. It should have 4 characters or less',
                        ]),
                        new UpperCase(),
                    ],
                ])
                ->getForm();
        } else {
            $error_message .= "Le coureur est introuvable.";
            $student = new Student();
        }

        $entityManager = $doctrine->getManager();
        $rows = $entityManager->createQuery('SELECT r FROM App\Entity\Ranking r')->getResult();

        $run = $runRepository->getLast();
        if ($run !== null) {
            $note = null;
            $startDateTime = $run->getStart();
            $start = $startDateTime->format("Y-m-d H:i:s");
            $message = "La course a démarré le " . $start . "";
        
            $run_message .= "Une course a été trouvé, vous pouvez scanné.";
        
            $chronometres = array();

            foreach ($rows as $row) {
                //Cherche à trouver le classement de l'étudiant pour la course actuelle dans la base de données
                $student = $row->getStudent();
                $existingRanking = $rankingRepository->findOneBy([
                    'student' => $student,
                    'run' => $run
                ]);

                // C'est un paramètre pour afficher que les résultats de la course actuelle
                if ($row->getRun() !== $run) {
                    continue;
                }

                $endDateTime = $row->getEnd();
                $end = $endDateTime->format("Y-m-d H:i:s");
                $endDateTime = \DateTime::createFromFormat("Y-m-d H:i:s", $end);

                $diffChronometre = $startDateTime->diff($endDateTime);

                $hoursChronometre = $diffChronometre->h;
                $minutesChronometre = $diffChronometre->i;
                $secondsChronometre = $diffChronometre->s;

                $chronometre = sprintf("1970-01-01 %02d:%02d:%02d", $hoursChronometre, $minutesChronometre, $secondsChronometre);
                //$chronometre = sprintf("1970-01-01 %02d:%02d:%02d", 0, 11, 34);
                $chronometre2 = new DateTime($chronometre);

                $student = $row->getStudent();

                $ranking = $rankingRepository->findOneBy([
                    'student' => $student
                ]); 
                
                $ranking->setChronometre(new \DateTime($chronometre));
                $rankingRepository->save($ranking, true);

                $objective = $student->getObjective();

                $secondsNote = $objective->getTimestamp() - $chronometre2->getTimestamp();
                // else if ($secondsNote < -15) {
                //     $note = null;
                // }
                
                if (abs($secondsNote) <= 15) {
                    $note = 15;
                } else if ($secondsNote < 0) {
                    switch (floor(abs($secondsNote) / 15)) {
                        case 0:
                            $note = 15;
                            break;
                        case 1:
                            $note = 14;
                            break;
                        case 2:
                            $note = 13;
                            break;
                        case 3:
                            $note = 12;
                            break;
                        case 4:
                            $note = 11;
                            break;
                        case 5:
                            $note = 10;
                            break;
                        case 6:
                            $note = 9;
                            break;
                        case 7:
                            $note = 8;
                            break;
                        case 8:
                            $note = 7;
                            break;
                        case 9:
                            $note = 6;
                            break;
                        case 10:
                            $note = 5;
                            break;
                        case 11:
                            $note = 4;
                            break;
                        case 12:
                            $note = 3;
                            break;
                        case 13:
                            $note = 2;
                            break;
                        case 14:
                            $note = 1;
                             break;
                        default:
                            $note = 0;
                            break;                           
                    }
                } else {
                    switch (round($secondsNote / 10)) {
                        case 0:
                            $note = 16;
                            break;
                        case 1:
                            $note = 17;
                            break;
                        case 2:
                            $note = 18;
                            break;
                        case 3:
                            $note = 19;
                            break;
                        default:
                            $note = 20;
                            break;
                    }
                }                
                
                $student->setNote($note);
                $studentRepository->save($student, true);
                
                $chronometres[$row->getStudent()->getId()] = new \DateTime($chronometre);  

                // C'est pour récupérer les éléments de la dernière course
                $resultRepository = $doctrine->getRepository(Ranking::class);
                $lastRun = $runRepository->getLast();
                $rows = $resultRepository->findByRun($lastRun);
            }

            return $this->render('douchette/index.html.twig', [
                'form' => $form->createView(),
                'error_message' => $error_message,
                'success_message' => $success_message,
                'run_message' => $run_message,
                'message' => $message,
                'rows' => $rows,
                'chronometres' => $chronometres,
                'note' => $note,
                'chronometre' => $chronometre,
            ]);
        } else {
            // Si le dernier run n'existe pas, affiche un message d'erreur
            $run_message = "No runs found, please press Start and you can scan.";
            $message = "The race hasn't started.";
            return $this->render('douchette/index.html.twig', [
                'form' => $form->createView(),
                'error_message' => $error_message,
                'success_message' => $success_message,
                'run_message' => $run_message,
                'message' => $message,
                'rows' => $rows,
                'chronometres' => []
            ]);
        }
    }
}

class UpperCase extends Constraint
{
    public $message = 'Please activate your shift key "SHIFT" on your keyboard.';

    public function validatedBy()
    {
        return \get_class($this) . 'Validator';
    }
}

class UpperCaseValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/[ 0-9 ]/', $value ) ) {
                $this->context->buildViolation( $constraint->message )->addViolation();
            }
        }
    }
