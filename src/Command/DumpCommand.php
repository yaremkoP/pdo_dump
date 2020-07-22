<?php

namespace App\Command;

use App\Adapter\DB\PDOInterface;
use App\Writer\Writer;
use App\Helper;
use App\Generator\SQLGenerator;
use PDOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DumpCommand extends Command
{
    const TMP_DIR = 'tmp';

    /**
     * @var string
     */
    private string $default_driver = 'mysql';

    protected function configure()
    {
        $this->setName('dump')
             ->setDescription('Create DB dump file')
             ->addArgument('username', InputArgument::REQUIRED, 'User name')
             ->addArgument('password', InputArgument::REQUIRED, 'Password')
             ->addArgument('database', InputArgument::REQUIRED, 'Database')
             ->addArgument('host', InputArgument::REQUIRED, 'Host(:port)')
             ->addArgument('file', InputArgument::OPTIONAL, 'File output', '')
             ->addOption('dbdriver', null, InputOption::VALUE_REQUIRED, 'Set db driver for PDO', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $this->extractFilePath($input->getArgument('file'));

        try {
            $db_adapter = $this->makeAdapter($input);
        } catch (PDOException $e) {
            $this->errorMessage($e->getMessage(), $output);

            return Command::FAILURE;
        }

        $writer    = new Writer($file,);
        $generator = new SQLGenerator($db_adapter);
        $writer->write($generator->dump());

        $output->writeln('<info>Dump DB start...</info>');

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
        $filepath .= ( ! $file) ? 'dump-' . time() . '.sql' : $file . '.sql';

        Helper::createTmpFolder($this::TMP_DIR);

        return $filepath;
    }

    /**
     * @param InputInterface $input
     *
     * @return PDOInterface
     */
    protected function makeAdapter(InputInterface $input)
    {
        $adapter_class = $this->handleDbDriver($input->getOption('dbdriver'));

        $host     = $input->getArgument('host');
        $database = $input->getArgument('database');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        return new $adapter_class($host, $database, $username, $password);
    }

    /**
     * @param string $option
     *
     * @return string Class name of DB adapter handler
     * @throws PDOException
     */
    protected function handleDbDriver(string $option): string
    {
        $driver = '';
        if (empty($option)) {
            $driver = $this->default_driver;
        } else {
            $driver = $option;
        }
        /** @var array $supported_driver Array of supported drivers */
        $supported_driver = Helper::getListSupportedDrivers();

        if ( ! array_key_exists($driver, $supported_driver)) {
            throw new PDOException(
                'You try to use not supported PDO driver. Please check list at Helper::getListSupportedDrivers'
            );
        }

        if ( ! class_exists($supported_driver[$driver])) {
            throw new PDOException('Can\'t find class ' . $supported_driver[$driver] . ' to initial PDO adapter');
        }

        return $supported_driver[$driver];
    }
}
