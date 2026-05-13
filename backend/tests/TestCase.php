<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function asDashboardAdmin(): static
    {
        $this->withSession(['dashboard_authenticated' => true]);

        return $this;
    }
}
