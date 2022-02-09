<?php

namespace App\Command;

use App\Entity\Movie;
use App\OmdbApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:movie:import',
    description: 'Add a short description for your command',
)]
class MovieImportCommand extends Command
{
    private OmdbApi $omdbApi;
    private EntityManagerInterface $manager;

    public function __construct(OmdbApi $omdbApi, EntityManagerInterface $manager)
    {
        $this->omdbApi = $omdbApi;
        $this->manager = $manager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('keyword', 'k', InputOption::VALUE_REQUIRED, 'Mot-clé de recherche de films pour l\'import en BDD.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $keyword = $input->getOption('keyword');

        if (!$keyword) {
            $keyword = $io->ask('🧐 Vous avez oublié de préciser le mot-clé, faites le maintenant !', 'Harry Potter');
        }

        $movies = $this->omdbApi->requestAllBySearch($keyword);
        #dump($movies);

        $io->title("# Films à importer en BDD ayant le mot-clé " . $keyword);

        $io->progressStart(count($movies['Search'])); // 10 étapes
        foreach ($movies['Search'] as $movieData) {
            $movieData = $this->omdbApi->requestOneById($movieData['imdbID']);

            $movie = Movie::fromApi($movieData);
            //dump($movie);
            $this->manager->persist($movie);
            $io->progressAdvance(1);        
        }
        $output->write("\r");
        $this->manager->flush();
        //$io->progressFinish();

        $io->success(sprintf('%d films viennent d\'être importés', count($movies['Search'])));


        return Command::SUCCESS;
    }
}
