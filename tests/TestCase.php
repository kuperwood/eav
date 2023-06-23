<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Tests;

use Drobotik\Eav\Database\Connection;
use Drobotik\Eav\Factory\EavFactory;
use Drobotik\Eav\Trait\SingletonsTrait;
use Faker\Generator;
use Illuminate\Database\Capsule\Manager as Capsule;

class TestCase extends \PHPUnit\Framework\TestCase
{
    use SingletonsTrait;
    protected EavFactory $eavFactory;
    protected Generator $faker;
    protected function setUp() : void
    {
        parent::setUp();
        $sqlitePath = dirname(__DIR__) . '/tests/test.sqlite';
        $dbParams = [
            'driver' => 'pdo_sqlite',
            'path' => $sqlitePath
        ];
        Connection::get($dbParams);
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver'   => 'sqlite',
            'database' => $sqlitePath,
        ]);
        $this->capsule = $capsule;
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
        $migrator = new Migrator();
        $migrator->rollback();
        $migrator->migrate();
        $this->eavFactory = new EavFactory();
        $this->faker = \Faker\Factory::create();
    }

    protected function tearDown(): void
    {
        $migrator = new Migrator();
        $migrator->rollback();
        Cleaner::run();
        parent::tearDown();
    }
}