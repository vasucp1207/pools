<?php

namespace Utopia\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Utopia\Pools\Connection;
use Utopia\Pools\Pool;

class ConnectionTest extends TestCase
{
    protected Connection $object;

    public function setUp(): void
    {
        $this->object = new Connection('x');
    }

    public function testGetID(): void
    {
        $this->assertEquals(null, $this->object->getID());

        $this->object->setID('test');

        $this->assertEquals('test', $this->object->getID());
    }

    public function testSetID(): void
    {
        $this->assertEquals(null, $this->object->getID());

        $this->assertInstanceOf(Connection::class, $this->object->setID('test'));

        $this->assertEquals('test', $this->object->getID());
    }

    public function testGetResource(): void
    {
        $this->assertEquals('x', $this->object->getResource());
    }

    public function testSetResource(): void
    {
        $this->assertEquals('x', $this->object->getResource());

        $this->assertInstanceOf(Connection::class, $this->object->setResource('y'));

        $this->assertEquals('y', $this->object->getResource());
    }

    public function testSetPool(): void
    {
        $pool = new Pool('test', 1, function () {
            return 'x';
        });

        $this->assertNull($this->object->getPool());
        $this->assertInstanceOf(Connection::class, $this->object->setPool($pool));
    }

    public function testGetPool(): void
    {
        $pool = new Pool('test', 1, function () {
            return 'x';
        });

        $this->assertNull($this->object->getPool());
        $this->assertInstanceOf(Connection::class, $this->object->setPool($pool));

        $pool = $this->object->getPool();

        if($pool === null) {
            throw new Exception("Pool should never be null here.");
        }

        $this->assertInstanceOf(Pool::class, $pool);
        $this->assertEquals('test', $pool->getName());
    }

    public function testReclaim(): void
    {
        $pool = new Pool('test', 2, function () {
            return 'x';
        });

        $this->assertEquals(2, $pool->count());

        $connection1 = $pool->pop();

        $this->assertEquals(1, $pool->count());

        $connection2 = $pool->pop();

        $this->assertEquals(0, $pool->count());

        $this->assertInstanceOf(Pool::class, $connection1->reclaim());

        $this->assertEquals(1, $pool->count());

        $this->assertInstanceOf(Pool::class, $connection2->reclaim());

        $this->assertEquals(2, $pool->count());
    }

    public function testReclaimException(): void {
        $this->expectException(Exception::class);
        $this->object->reclaim();
    }
}
