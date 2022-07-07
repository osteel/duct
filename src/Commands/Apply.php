<?php

namespace Osteel\Duct\Commands;

use Osteel\Duct\Services\Configurator\Exceptions\MissingConfiguration;
use Osteel\Duct\Services\Interpreter;
use Osteel\Duct\Sieves\Sieve;
use Osteel\Duct\ValueObjects\Directory;
use Osteel\Duct\ValueObjects\Treatment;
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
        $interpreter = new Interpreter($input, $output);

        try {
            $treatment = Treatment::make($input->getArgument('treatment'));
            $directory = Directory::make($input->getArgument('directory'), (bool) $input->getOption('recursive'));

            $treatment->sieves->each(fn (Sieve $sieve) => $sieve->filter($directory));
        } catch (MissingConfiguration $exception) {
            $interpreter->error('Please run "duct config"');

            return Command::FAILURE;
        } catch (Throwable $exception) {
            $interpreter->error($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
