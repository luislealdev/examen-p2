<?php

namespace Tests;

use Database\Seeders\TestDataSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed basic test data needed by all tests
        $this->seed(TestDataSeeder::class);
    }
}
