<?php

namespace Osteel\Duct\Services;

use Symfony\Component\Console\Style\SymfonyStyle;

class Interpreter extends SymfonyStyle
{
    /**
     * {@inheritdoc}
     *
     * @param  string $message
     * @return void
     */
    public function error($message)
    {
        $this->block(sprintf(' 🚨  %s', $message), null, 'fg=white;bg=red', ' ', true);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $message
     * @return void
     */
    public function success($message)
    {
        $this->block(sprintf(' 🎉  %s', $message), null, 'fg=white;bg=green', ' ', true);
    }
}
