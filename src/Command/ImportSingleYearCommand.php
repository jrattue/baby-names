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

        $this->connection->getConfiguration()->setSQLLogger(null);
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
        $year = 2022;
        $gender = Name::GENDER_FEMALE;
        $filePath = __DIR__.'/../../public/girls_2022.csv';
        // END CHANGE

        $io = new SymfonyStyle($input, $output);
        $csv = Reader::createFromPath($filePath)->setHeaderOffset(0);

        $findNameSql = 'SELECT id FROM `name` where `name` = :name AND gender = :gender';
        $nameSql = 'INSERT INTO name (`name`, `gender`) VALUES (:name, :gender)';
        $yearSql = 'INSERT INTO year (`year`, `count`, `rank`, `name_id`) VALUES (:year, :count, :rank, :name_id)';

        foreach ($csv as $record) {
            $nameId = $this->connection->executeQuery($findNameSql, [
                'name' => $record['Name'],
                'gender' => $gender
            ])->fetchOne();

            if(!$nameId){
                $this->connection->executeQuery($nameSql, [
                    'name' => $record['Name'],
                    'gender' => $gender
                ]);
                $nameId = $this->connection->lastInsertId();
            }

            $count = $this->strToInt($record['Count']);
            $rank = (int)$record['Rank'];

            if($count == ":") $count = 0;
            if($rank == ":") $rank = 0;

            $this->connection->executeQuery($yearSql, [
                'year' => $year, \PDO::PARAM_INT,
                'count' => $count, \PDO::PARAM_INT,
                'rank' => $rank, \PDO::PARAM_INT,
                'name_id' => (int)$nameId, \PDO::PARAM_INT,
            ]);

            $output->write(".");
        }

        $io->success('Import complete');

        return Command::SUCCESS;
    }
}
