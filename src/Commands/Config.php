<?php

namespace Osteel\Duct\Commands;

use Exception;
use Osteel\Duct\Services\Configurator\Configurator;
use Osteel\Duct\Services\Reporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Config extends Command
{
    /**
     * The name of the command (the part after "bin/duct").
     *
     * @var string
     */
    protected static $defaultName = 'config';

    /**
     * The command description shown when running "php bin/duct list".
     *
     * @var string
     */
    protected static $defaultDescription = 'Configure duct';

    /**
     * Execute the command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int 0 if everything went fine, or an exit code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reporter     = new Reporter($input, $output);
        $configurator = new Configurator();

        $treatmentsLocation = $this->getTreatmentsLocation($configurator, $reporter);
        $editor             = $this->getEditor($configurator, $reporter);

        $process = new Process([$editor, $treatmentsLocation]);
        $process->setTty(true);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $reporter->success('Configuration successfully updated');

        return Command::SUCCESS;
    }

    private function getTreatmentsLocation(Configurator $configurator, Reporter $reporter): string
    {
        try {
            $treatmentsLocation = $configurator->load('TREATMENTS_LOCATION');
        } catch (Exception) {
            $treatmentsLocation = $reporter->question(
                'Where to save treatments?',
                sprintf('%s/config', dirname(__DIR__, 2))
            );

            if (! is_dir($treatmentsLocation)) {
                $reporter->error('Invalid directory');
                return $this->getTreatmentsLocation($configurator, $reporter);
            }

            $treatmentsLocation .= '/treatments.yml';

            $configurator->save('TREATMENTS_LOCATION', $treatmentsLocation);
        }

        return $treatmentsLocation;
    }

    private function getEditor(Configurator $configurator, Reporter $reporter): string
    {
        try {
            $editor = $configurator->load('EDITOR');
        } catch (Exception) {
            $editor = $reporter->question('What editor to use?', 'vi');

            $configurator->save('EDITOR', $editor);
        }

        return $editor;
    }
}
