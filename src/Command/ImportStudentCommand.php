<?php

namespace App\Command;

use App\Command\ImportStudentCommand;
use App\Service\ImportStudentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-students')]
class ImportStudentCommand extends Command
{
    private ImportStudentService $importStudentService;
    public function __construct(ImportStudentService $importStudentService)
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->importStudentService->importStudentFromCli($io);

        return Command::SUCCESS;
    }
}
