<?php

namespace Osteel\Duct\Services;

use Symfony\Component\Console\Style\SymfonyStyle;

class Interpreter extends SymfonyStyle
{
    /**
     * Ask a question and return the input value.
     */
    public function question(string $question, ?string $default = null): string
    {
        return $this->ask(sprintf(' ✍️  %s', $question), $default);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string $message
     * @return void
     */
    public function comment($message)
    {
        $this->block(sprintf(' ℹ️   %s', $message), null, 'fg=white;bg=blue', ' ', true);
    }

    public function work(string $message): void
    {
        $this->block(sprintf(' ⚙️  %s', $message), null, 'fg=white;bg=cyan', ' ', true);
    }

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
