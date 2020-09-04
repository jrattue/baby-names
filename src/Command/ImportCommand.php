<?php

namespace App\Command;

use App\Entity\Name;
use Doctrine\DBAL\Connection;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

    protected $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    protected function configure()
    {
        $this
            ->setDescription('Imports all data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = __DIR__.'/../../public/data/girls.csv';

        $io = new SymfonyStyle($input, $output);

        $csv = Reader::createFromPath($filePath)->setHeaderOffset(0);

        $nameSth = $this->connection->prepare('INSERT INTO name (`name`, `gender`) VALUES (:name, :gender)');

        $yearSth = $this->connection->prepare('INSERT INTO year (`year`, `count`, `rank`, `name_id`) VALUES (:year, :count, :rank, :name_id)');

        $years = range(1996, 2019);

        foreach ($csv as $record) {
            $nameSth->bindValue(':name', $record['Name'], \PDO::PARAM_STR);
            $nameSth->bindValue(':gender', Name::GENDER_FEMALE, \PDO::PARAM_STR);
            $nameSth->execute();
            $output->write("+");

            $nameId = $this->connection->lastInsertId();

            foreach ($years as $year){
                $count = $record[$year.' Count'];
                $rank = $record[$year.' Rank'];

                if($count == ":") $count = 0;
                if($rank == ":") $rank = 0;

                $yearSth->bindValue(':year', $year, \PDO::PARAM_INT);
                $yearSth->bindValue(':count', $count, \PDO::PARAM_INT);
                $yearSth->bindValue(':rank', $rank, \PDO::PARAM_INT);
                $yearSth->bindValue(':name_id', (int)$nameId, \PDO::PARAM_INT);
                $yearSth->execute();
                //$output->write(".");
            }
            $output->writeln("");
        }

        $io->success('Import complete');

        return Command::SUCCESS;
    }
}
