<?php

namespace Osteel\Duct\Commands;

use Osteel\Duct\Services\Assistant\Assistant;
use Osteel\Duct\Services\Configurator\Configurator;
use Osteel\Duct\Services\Configurator\Exceptions\MissingConfiguration;
use Osteel\Duct\Services\Operator\Operator;
use Osteel\Duct\Services\Reporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class Apply extends Command
{
    /**
     * The name of the command (the part after "bin/duct").
     *
     * @var string
     */
    protected static $defaultName = 'apply';

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
        $reporter = new Reporter($input, $output);

        try {
            $configurator = new Configurator();
            $assistant    = new Assistant($configurator->load('TREATMENTS_LOCATION'));
            $operator     = new Operator($reporter);

            $treatment = $assistant->prepare($input->getArgument('treatment'));
            $directory = $assistant->open($input->getArgument('directory'), (bool) $input->getOption('recursive'));

            $operator->apply($treatment, $directory);
        } catch (MissingConfiguration $exception) {
            $reporter->error('Please run "duct config"');

            return Command::FAILURE;
        } catch (Throwable $exception) {
            $reporter->error($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
