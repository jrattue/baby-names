<?php

namespace App\Command;

use App\Entity\Name;
use Doctrine\DBAL\Connection;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportSingleYearCommand extends Command
{
    protected static $defaultName = 'app:import-single';

    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Imports single year')
        ;
    }

    private function strToInt(string $value): int
    {
        $value = trim($value);
        $out = str_replace(",","", $value);
        return (int)$out;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // CHANGE THESE
        $year = 2020;
        $gender = Name::GENDER_MALE;
        $filePath = __DIR__.'/../../public/data/boys-2020.csv';

        $io = new SymfonyStyle($input, $output);
        $csv = Reader::createFromPath($filePath)->setHeaderOffset(0);

        $findNameSth = $this->connection->prepare('SELECT id FROM `name` where `name` = :name');
        $nameSth = $this->connection->prepare('INSERT INTO name (`name`, `gender`) VALUES (:name, :gender)');
        $yearSth = $this->connection->prepare('INSERT INTO year (`year`, `count`, `rank`, `name_id`) VALUES (:year, :count, :rank, :name_id)');

        foreach ($csv as $record) {
            $findNameSth->bindValue(':name', $record['Name'], \PDO::PARAM_STR);
            $findNameSth->execute();
            $nameId = $findNameSth->fetchOne();
            if(!$nameId){
                $nameSth->bindValue(':name', $record['Name'], \PDO::PARAM_STR);
                $nameSth->bindValue(':gender', $gender, \PDO::PARAM_STR);
                $nameSth->execute();
                $output->write("+");

                $nameId = $this->connection->lastInsertId();
            }

            $count = $this->strToInt($record['Count']);
            $rank = (int)$record['Rank'];

            if($count == ":") $count = 0;
            if($rank == ":") $rank = 0;

            $yearSth->bindValue(':year', $year, \PDO::PARAM_INT);
            $yearSth->bindValue(':count', $count, \PDO::PARAM_INT);
            $yearSth->bindValue(':rank', $rank, \PDO::PARAM_INT);
            $yearSth->bindValue(':name_id', (int)$nameId, \PDO::PARAM_INT);
            $yearSth->execute();

            //$output->writeln("");
        }

        $io->success('Import complete');

        return Command::SUCCESS;
    }
}
