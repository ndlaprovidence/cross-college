<?php

namespace App\Controller;

use App\Entity\Race;
use App\Form\RaceType;
use App\Repository\RaceRepository;
use App\Service\ImportStudentService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/race')]
class RaceController extends AbstractController
{
    #[Route('/', name: 'app_race_index', methods: ['GET'])]
    public function index(RaceRepository $raceRepository): Response
    {
        $message = "";

        return $this->render('race/index.html.twig', [
            'races' => $raceRepository->findAll(),
            'message' => $message,
        ]);
    }

    #[Route('/new', name: 'app_race_new', methods: ['GET', 'POST'])]
    public function new(Request $request, RaceRepository $raceRepository, SluggerInterface $slugger, ImportStudentService $importStudentService): Response
    {
        $message = "";
        $race = new Race();
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        if ($form->isSubmitted()) {

            $importFile = $form->get('importfilename')->getData();
            // dump($importFile);

            // this condition is needed because the 'import' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($importFile) {
                // dump("03");

                $originalFilename = pathinfo($importFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                // $newFilename = $safeFilename.'-'.uniqid().'.'.$importFile->guessExtension();
                $newFilename = "liste_eleves.csv";

                // Move the file to the directory where imports are stored
                try {
                    $importFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $message = "Error during CSV file copying";
                }

                // updates the 'importFilename' property to store the PDF file name
                // instead of its contents
                $race->setimportFilename($newFilename);
                $raceRepository->save($race, true);


                // dump("originalFilename = '" . $originalFilename . "'");
                // dump("newFilename = '" . $newFilename . "'");
                // dump("this->getParameter('upload_directory') = '" . $this->getParameter('upload_directory') . "'");

                // Import data from CSV file to database
                $importStudentService->importStudentFromWeb($this->getParameter('upload_directory') . "/" . $newFilename); 

            }


            return $this->render('race/index.html.twig', [
                'races' => $raceRepository->findAll(),
                'message' => $message,
            ]);
        }

        return $this->renderForm('race/new.html.twig', [
            'race' => $race,
            'form' => $form,
            'message' => $message,
        ]);
    }

    #[Route('/{id}', name: 'app_race_show', methods: ['GET'])]
    public function show(Race $race): Response
    {
        return $this->render('race/show.html.twig', [
            'race' => $race,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_race_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Race $race, RaceRepository $raceRepository): Response
    {
        $form = $this->createForm(RaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raceRepository->save($race, true);

            return $this->redirectToRoute('app_race_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('race/edit.html.twig', [
            'race' => $race,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_race_delete', methods: ['POST'])]
    public function delete(Request $request, Race $race, RaceRepository $raceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$race->getId(), $request->request->get('_token'))) {
            $raceRepository->remove($race, true);
        }

        return $this->redirectToRoute('app_race_index', [], Response::HTTP_SEE_OTHER);
    }
}
