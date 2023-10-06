<?php
namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

abstract class AbstractApiTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Can't use setUp() because setUpTraits() gets called earlier. We need
     * the database to have Permissions and such for setting up other test
     * traits.
     */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();
        $this->seed();
        return $uses;
    }

}
