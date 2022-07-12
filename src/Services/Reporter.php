<?php

namespace Osteel\Duct\Services;

use Symfony\Component\Console\Style\SymfonyStyle;

class Reporter extends SymfonyStyle
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

    /**
     * Formats a command action.
     */
    public function action(string $message): void
    {
        $this->block(sprintf(' ⚙️   %s', $message), null, 'fg=white;bg=cyan', ' ', true);
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

    /**
     * {@inheritdoc}
     *
     * @param  string $message
     * @return void
     */
    public function warning($message)
    {
        $this->block(sprintf(' 😲  %s', $message), null, 'fg=white;bg=yellow', ' ', true);
    }
}
