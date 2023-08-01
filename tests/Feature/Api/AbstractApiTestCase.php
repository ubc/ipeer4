<?php
namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class AbstractApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
}
