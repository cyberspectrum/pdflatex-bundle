<?php

/**
 * This file is part of cyberspectrum/pdflatex-bundle.
 *
 * (c) CyberSpectrum <http://www.cyberspectrum.de/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    cyberspectrum/pdflatex-bundle
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @copyright  2017 CyberSpectrum <http://www.cyberspectrum.de/>
 * @license    LGPL https://github.com/cyberspectrum/pdflatex-bundle/blob/master/LICENSE
 * @filesource
 */
declare(strict_types = 1);

namespace CyberSpectrum\PdfLatexBundle\Exception;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Exception for failed pdflatex processes.
 */
class LatexFailedException extends RuntimeException
{
    /**
     * The failed process.
     *
     * @var Process
     */
    private $process;

    /**
     * Create a new instance.
     *
     * @param Process $process The failed process.
     *
     * @throws InvalidArgumentException When the passed process was successful.
     */
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

    /**
     * Retrieve the process.
     *
     * @return Process
     */
    public function getProcess()
    {
        return $this->process;
    }

    /**
     * Retrieve the error output.
     *
     * @return mixed
     */
    public function getLatexError()
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
