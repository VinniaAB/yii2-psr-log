<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2016-10-24
 * Time: 19:35
 */

namespace Vinnia\Yii2\Tests;


use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Vinnia\Yii2\PsrTarget;
use yii\log\Logger;

class PsrTargetTest extends TestCase
{

    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @var PsrTarget
     */
    public $target;

    public function setUp()
    {
        parent::setUp();

        $this->logger = new class extends AbstractLogger {

            /**
             * @var array
             */
            public $messages = [];

            public function log($level, $message, array $context = [])
            {
                $this->messages[] = [$level, $message, $context];
            }
        };

        \Yii::$container->set(LoggerInterface::class, $this->logger);

        $this->target = new PsrTarget([
            'logger' => $this->logger,
        ]);
    }

    public function testLogsTextMessages()
    {
        $this->target->messages = [
            ['Hello World', Logger::LEVEL_INFO, 'app', time()],
        ];
        $this->target->export();

        $this->assertCount(1, $this->logger->messages);
        $this->assertEquals(LogLevel::INFO, $this->logger->messages[0][0]);
        $this->assertEquals('Hello World', $this->logger->messages[0][1]);
    }

    public function testLogsExceptionsToStrings()
    {
        $e = new \Exception('Hello World');
        $this->target->messages = [
            [$e, Logger::LEVEL_ERROR, 'app', time()],
        ];
        $this->target->export();

        $this->assertCount(1, $this->logger->messages);
        $this->assertEquals(LogLevel::ERROR, $this->logger->messages[0][0]);
        $this->assertTrue(is_string($this->logger->messages[0][1]));
        $this->assertEquals($e, $this->logger->messages[0][2]['exception']);
    }

}
