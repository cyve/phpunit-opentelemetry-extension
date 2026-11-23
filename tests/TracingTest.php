<?php

namespace Cyve\OpenTelemetry\Tests;

use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

class TracingTest extends TestCase
{
    // public static function setUpBeforeClass(): void
    // {
    //     // error_log(__METHOD__);
    // }
    //
    // public static function tearDownAfterClass(): void
    // {
    //     // error_log(__METHOD__);
    // }
    //
    // public function setUp(): void
    // {
    //     // error_log(__METHOD__);
    // }
    //
    // public function tearDown(): void
    // {
    //     // error_log(__METHOD__);
    // }

    public function testTracing(): void
    {
        self::assertTrue(true);
    }

    public function testSlow(): void
    {
        usleep(1000);

        self::assertTrue(true);
    }

    public function testFailure(): void
    {
        self::assertTrue(false);
    }

    public function testSkip(): void
    {
        self::markTestSkipped();
    }

    #[TestWith([1])]
    #[TestWith([2])]
    public function testDataSet(int $data): void
    {
        self::assertTrue(true);
    }

    public function testError(): void
    {
        throw new \Error('This is an error !');
    }
}
