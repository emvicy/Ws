<?php

namespace Ws\Model;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class Log extends AbstractLogger
{
    private const OUTMAP = [
        LogLevel::EMERGENCY => STDERR,
        LogLevel::ALERT => STDERR,
        LogLevel::CRITICAL => STDERR,
        LogLevel::ERROR => STDERR,

        LogLevel::WARNING => STDOUT,
        LogLevel::NOTICE => STDOUT,
        LogLevel::INFO => STDOUT,
        LogLevel::DEBUG => STDOUT,
    ];

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed   $level
     * @param string|\Stringable $message
     * @param mixed[] $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $level = $level ?? LogLevel::ERROR;
        $output = isset(self::OUTMAP[$level]) ? self::OUTMAP[$level] : STDERR;
        fwrite($output, date('Y-m-d H:i:s') . ' [' . $level . '] ' . $message . PHP_EOL);
    }
}
