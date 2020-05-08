<?php

/*
 * This file is copied from Psy\ExecutionLoopClosure
 * with some modifications
 */

namespace Emargareten\FileTinker;

use Psy\ExecutionClosure as PsyExecutionClosure;
use Psy\Shell;
use Psy\Exception\BreakException;
use Psy\Exception\ErrorException;
use Psy\Exception\ThrowUpException;
use Psy\Exception\TypeErrorException;


class ExecutionClosure extends PsyExecutionClosure
{
    /**
     * @param Shell $__psysh__
     */
    public function __construct(Shell $__psysh__)
    {
        $this->setClosure($__psysh__, function () use ($__psysh__) {

            try {
                $__psysh__->getInput();

                try {

                    // Buffer stdout; we'll need it later
                    \ob_start([$__psysh__, 'writeStdout'], 1);

                    // Convert all errors to exceptions
                    \set_error_handler([$__psysh__, 'handleError']);

                    // Evaluate the current code buffer
                    $_ = eval($__psysh__->onExecute($__psysh__->flushCode() ?: PsyExecutionClosure::NOOP_INPUT));
                } catch (\Throwable $_e) {
                    // Clean up on our way out.
                    if (\ob_get_level() > 0) {
                        \ob_end_clean();
                    }

                    throw $_e;
                } finally {
                    // Won't be needing this anymore
                    \restore_error_handler();
                }

                // Flush stdout (write to shell output, plus save to magic variable)
                \ob_end_flush();

                $__psysh__->writeReturnValue($_);
            } catch (BreakException $_e) {
                $__psysh__->writeException($_e);

                return;
            } catch (ThrowUpException $_e) {
                $__psysh__->writeException($_e);

                throw $_e;
            } catch (\TypeError $_e) {
                $__psysh__->writeException(TypeErrorException::fromTypeError($_e));
            } catch (\Error $_e) {
                $__psysh__->writeException(ErrorException::fromError($_e));
            } catch (\Exception $_e) {
                $__psysh__->writeException($_e);
            }
        });
    }
}
