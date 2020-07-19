<?php

namespace App\Command;

use App\Adapter\DB\MysqlAdapter;
use App\Adapter\File\FsWriter;
use App\Helper;
use App\Reader\SQLReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class MysqlDump extends Command
{
    const TMP_DIR = 'tmp';

    protected function configure()
    {
        $this->setName('dump')
             ->setDescription('Create DB dump file')
             ->addArgument('username', InputArgument::REQUIRED, 'User name')
             ->addArgument('password', InputArgument::REQUIRED, 'Password')
             ->addArgument('database', InputArgument::REQUIRED, 'Database')
             ->addArgument('host', InputArgument::REQUIRED, 'Host(:port)')
             ->addArgument('file', InputArgument::OPTIONAL, 'File output');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // For avoiding IDE error, declare all parameters
        //extract($input->getArguments());
        $host     = $input->getArgument('host');
        $database = $input->getArgument('database');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $file     = $input->getArgument('file') ?? '';

        $file = $this->extractFilePath($file);

        $output->writeln('<info>Dump DB start...</info>');

        //todo: create DB dump
        try {
            $mysql_adapter = new MysqlAdapter($host, $database, $username, $password);
            $fs_writer     = new FsWriter($file);
            $sql_reader    = new SQLReader($mysql_adapter, $fs_writer);
            $sql_reader->dump();
        } catch (\PDOException $e) {
            $this->errorMessage($e->getMessage(), $output);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function errorMessage(string $message, OutputInterface $output)
    {
        $output->writeln("<error>{$message}</error>");
        exit(Command::FAILURE);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function extractFilePath(string $file): string
    {
        if (Helper::isIllegalPath($file)) {
            $file = Helper::trimFileName($file) . '.sql';
        }
        $filepath = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . $this::TMP_DIR . DIRECTORY_SEPARATOR;
        $filepath .= (!$file) ? 'dump-' . time() . '.sql' : $file . '.sql';

        Helper::createTmpFolder($this::TMP_DIR);

        return $filepath;
    }
}
