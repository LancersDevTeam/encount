<?php
declare(strict_types=1);

namespace Encount\Error;

use Cake\Error\ErrorHandler;
use Encount\Encount;
use Throwable;

class EncountErrorHandler extends ErrorHandler
{
    /**
     * Encount error handler
     *
     * @access public
     * @author sakuragawa
     */
    public function handleError(
        int $code,
        string $description,
        ?string $file = null,
        ?int $line = null,
        ?array $context = null
    ): bool {
        $encount = new Encount();
        $encount->execute($code, $description, $file, $line, $context);

        return parent::handleError($code, $description, $file, $line, $context);
    }

    /**
     * Encount exception handler
     *
     * @access public
     * @throws \Exception
     * @author sakuragawa
     */
    public function handleException(Throwable $exception): void
    {
        $encount = new Encount();
        $encount->execute($exception);

        parent::handleException($exception);
    }

    /**
     * Encount fatal error handler
     *
     * @access public
     * @author sakuragawa
     */
    public function handleFatalError(int $code, string $description, string $file, int $line): bool
    {
        $encount = new Encount();
        $encount->execute($code, 'FatalError', $description, $file, $line);

        return parent::handleFatalError($code, $description, $file, $line);
    }
}
