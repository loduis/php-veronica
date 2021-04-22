<?php

namespace Veronica\Tests;

use PHPUnit\Framework\TestCase as FrameworkTestCase;
use Spatie\Snapshots\MatchesSnapshots;

abstract class TestCase extends FrameworkTestCase
{
    use MatchesSnapshots;

    protected function setUp(): void
    {
    }
}

