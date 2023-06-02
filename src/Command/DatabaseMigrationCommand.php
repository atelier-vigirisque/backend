<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DatabaseMigrationCommand extends Command
{
    public function __construct(private Connection $conn)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:migrations:execute');
        $this->addOption(
            'skip-integrity-check',
            null,
            InputOption::VALUE_NONE,
            'Dont check if already executed files have changed'
        );
        $this->addOption(
            'drop-database',
            null,
            InputOption::VALUE_NONE,
            'Drop the database before execute all migrations'
        );
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dont execute migrations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->conn->beginTransaction();

        if ($input->getOption('drop-database') && !$input->getOption('dry-run')) {
            $output->writeln("drop database...");
            $this->resetDatabase();
        }

        $this->createMigrationTableIfNotExists();
        ;

        $files = $this->getFilesToProcess($input->getOption('skip-integrity-check'));

        if (!count($files)) {
            $output->writeln("Already up to date.");
            return 0;
        }

        if ($input->getOption('dry-run')) {
            $output->writeln("Files to process :");
            $output->writeln($files);
            $output->writeln("remove --dry-run to execute");
            return 0;
        }

        foreach ($files as $file) {
            $output->writeln("execute $file...");
            $sql = file_get_contents($file);
            $this->conn->executeStatement($sql);
            $this->conn->executeQuery("insert into _migrations (file, checksum) values (?, ?)", [
                basename($file),
                md5_file($file)
            ]);
        }

        $this->conn->commit();

        return 0;
    }

    private function getFilesToProcess($skipIntegrityCheck = false)
    {
        $files_to_execute = [];

        $files_available = glob(__DIR__ . '/../../migrations/*.sql');

        usort($files_available, function ($a, $b) {
            $order_a = (int)explode('_', basename($a))[0];
            $order_b = (int)explode('_', basename($b))[0];

            return $order_a <=> $order_b;
        });

        foreach ($files_available as $file) {
            if (!preg_match('/\d+_\w+\.sql/', basename($file))) {
                throw new \Exception(
                    "The file " .
                    basename($file) .
                    "  is incorrectly named, it must start with a number followed by an underscore."
                );
            }

            $processed = $this->conn
                ->executeQuery("select checksum from _migrations where file=?", [basename($file)])
                ->fetchOne();
            if ($processed) {
                if (!$skipIntegrityCheck && $processed != md5_file($file)) {
                    throw new \Exception("The file $file have changed before last execution. 
                    Check the file then retry. Add --skip-integrity-check to force execution.");
                }
            } else {
                $files_to_execute[] = $file;
            }
        }

        return $files_to_execute;
    }

    private function createMigrationTableIfNotExists()
    {
        $this->conn->executeQuery("
            create table if not exists _migrations (
                file varchar not null primary key,
                executed_at timestamptz not null default NOW(),
                checksum varchar not null
            )
        ");
    }

    private function resetDatabase()
    {
        $this->conn->executeQuery("DROP SCHEMA public CASCADE;");
        $this->conn->executeQuery("CREATE SCHEMA public;");
    }
}
