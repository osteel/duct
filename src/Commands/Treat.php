<?php

namespace Osteel\Duct\Commands;

use Osteel\Duct\Services\Plumber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Treat extends Command
{
    /**
     * The name of the command (the part after "bin/duct").
     *
     * @var string
     */
    protected static $defaultName = 'treat';

    /**
     * The command description shown when running "php bin/duct list".
     *
     * @var string
     */
    protected static $defaultDescription = 'Applies a treatment to a directory';

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->addArgument('treatment', InputArgument::REQUIRED, 'The treatment');
        $this->addArgument('directory', InputArgument::REQUIRED, 'The directory');
        $this->addOption('recursive', 'r', InputOption::VALUE_NONE, 'Apply the treatment recursively');
    }

    /**
     * Execute the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $treatment = $input->getArgument('treatment');
        $directory = $input->getArgument('directory');
        $recursive = (bool) $input->getOption('recursive');

        //try {
            $plumber = new Plumber();
            $plumber->apply($treatment, $directory, $recursive);
        /*} catch (Throwable $exception) {
            return Command::FAILURE;
        }*/

        return Command::SUCCESS;
    }
}
