<?php

declare(strict_types=1);

namespace CyberSpectrum\PdfLatexBundle\Exception;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Exception for failed pdflatex processes.
 */
class LatexFailedException extends RuntimeException
{
    /** The failed process. */
    private Process $process;

    /** @throws InvalidArgumentException When the passed process was successful. */
    public function __construct(Process $process)
    {
        if ($process->isSuccessful()) {
            throw new InvalidArgumentException('Expected a failed process, but the given process was successful.');
        }

        $error = sprintf(
            'The command "%s" failed.' . "\n\nExit Code: %s(%s)\n\nWorking directory: %s",
            $process->getCommandLine(),
            $process->getExitCode(),
            $process->getExitCodeText(),
            $process->getWorkingDirectory()
        );

        if (!$process->isOutputDisabled()) {
            $error .= sprintf(
                "\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s",
                $process->getOutput(),
                $process->getErrorOutput()
            );
        }

        parent::__construct($error);

        $this->process = $process;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    /** Retrieve the error output. */
    public function getLatexError(): string
    {
        $output = $this->process->getOutput();
        $errors = $this->process->getErrorOutput();

        return str_replace(
            $this->process->getWorkingDirectory(),
            '',
            $output . $errors
        );
    }
}
