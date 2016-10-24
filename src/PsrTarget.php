<?php
declare(strict_types = 1);

namespace Vinnia\Yii2;

use Psr\Log\LogLevel;
use Psr\Log\LoggerInterface;
use yii\helpers\VarDumper;
use yii\log\Logger;
use yii\log\Target;
use Throwable;

class PsrTarget extends Target
{

    const LEVEL_MAP = [
        Logger::LEVEL_ERROR => LogLevel::ERROR,
        Logger::LEVEL_WARNING => LogLevel::WARNING,
        Logger::LEVEL_INFO => LogLevel::INFO,
        Logger::LEVEL_TRACE => LogLevel::DEBUG,
        Logger::LEVEL_PROFILE => LogLevel::DEBUG,
        Logger::LEVEL_PROFILE_BEGIN => LogLevel::DEBUG,
        Logger::LEVEL_PROFILE_END => LogLevel::DEBUG,
    ];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PsrTarget constructor.
     * @param LoggerInterface $logger
     * @param array $config
     */
    function __construct(LoggerInterface $logger, array $config = [])
    {
        parent::__construct($config);

        $this->logger = $logger;
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;

            $context = [
                'category' => $category,
                'timestamp' => $timestamp,
            ];

            if (!is_string($text)) {
                if ($text instanceof Throwable) {
                    $context['exception'] = $text;

                    // If we were to just cast the exception to a string it would include
                    // the whole stack trace. Let the logger handle the formatting.
                    $text = $this->exceptionToString($text);
                }
                else {
                    $text = VarDumper::export($text);
                }
            }

            $psrLevel = self::LEVEL_MAP[$level];
            $this->logger->log($psrLevel, $text, $context);
        }
    }

    /**
     * Borrowed from https://github.com/Seldaek/monolog/blob/master/src/Monolog/ErrorHandler.php#L153
     * @param Throwable $e
     * @return string
     */
    protected function exceptionToString(Throwable $e): string
    {
        return sprintf('Uncaught Exception %s: "%s" at %s line %s', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
    }

}
