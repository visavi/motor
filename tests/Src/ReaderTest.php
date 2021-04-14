<?php

namespace Tests\Src;

use App\Reader;
use Tests\TestCase;

/**
 * @coversDefaultClass \App\Reader
 */
final class ReaderTest extends TestCase
{
    /**
     * @var Reader
     */
    private Reader $reader;

    public function setUp(): void
    {
        parent::setUp();
        $this->reader = new Reader('tests/data/test.csv');
    }

    /**
     * @covers ::__construct()
     */
    public function testReader(): void
    {
        $this->assertInstanceOf(Reader::class, $this->reader);
    }

    /**
     * Find by id
     * @covers ::find()
     */
    public function testFind(): void
    {
        $find = $this->reader->find('Vantuz');

        $this->assertIsArray($find);
        $this->assertArrayHasKey('id', $find);
        $this->assertEquals('Vantuz', $find['id']);
    }

    /**
     * Find by name limit 1
     * @covers ::where()
     */
    public function testWhereLimit(): void
    {
        $find = $this->reader->where('name', 'Миша')->limit(1)->get();

        $this->assertIsArray($find);
        $this->assertIsArray($find[0]);
        $this->assertArrayHasKey('id', $find[0]);
        $this->assertEquals('Миша', $find[0]['name']);
        $this->assertEquals('Заголовок10', $find[0]['title']);
    }

    /**
     * Find by name and last 1
     *
     * @covers ::where()
     */
    public function testWhereLimitLast(): void
    {
        $find = $this->reader->where('name', 'Миша')->reverse()->first();

        $this->assertIsArray($find);
        $this->assertArrayHasKey('id', $find);
        $this->assertEquals('Миша', $find['name']);
        $this->assertEquals('Заголовок18', $find['title']);
    }


    /**
     * Find by name and title
     *
     * @covers ::where()
     */
    public function testWhereWhereGet(): void
    {
        $find = $this->reader->where('name', 'Миша')->where('title', 'Заголовок10')->get();

        $this->assertIsArray($find);
        $this->assertIsArray($find[0]);
        $this->assertArrayHasKey('id', $find[0]);
        $this->assertEquals('Миша', $find[0]['name']);
        $this->assertEquals('Заголовок10', $find[0]['title']);
    }

    /**
     * Find by condition
     *
     * @covers ::where()
     */
    public function testWhere(): void
    {
        $find = $this->reader->where('time', '>=', 1231231235)->get();

        $this->assertIsArray($find);
        $this->assertIsArray($find[0]);
        $this->assertCount(3, $find);
        $this->assertArrayHasKey('id', $find[0]);
        $this->assertGreaterThanOrEqual('1231231235', $find[0]['time']);
        $this->assertGreaterThanOrEqual('1231231235', $find[1]['time']);
        $this->assertGreaterThanOrEqual('1231231235', $find[2]['time']);
    }

    /**
     * Get count
     *
     * @covers ::count()
     */
    public function testCount(): void
    {
        $find = $this->reader->count();

        $this->assertEquals(20, $find);
    }

    /**
     * Get where count
     *
     * @covers ::count()
     */
    public function testWhereCount(): void
    {
        $find = $this->reader->where('time', '>', 1231231234)->count();

        $this->assertEquals(3, $find);
    }

    /**
     * Get lines 1 - 10
     *
     * @covers ::get()
     */
    public function testOffsetLimitGet(): void
    {
        $find = $this->reader->offset(0)->limit(10)->get();

        $this->assertCount(10, $find);
        $this->assertEquals('Заголовок1', $find[0]['title']);
        $this->assertEquals('Заголовок7', $find[6]['title']);
        $this->assertEquals('Заголовок10', $find[9]['title']);
    }

    /**
     * Get lines reverse (last 10 lines reversed)
     *
     * @covers ::get()
     */
    public function testOffsetLimitReverseGet(): void
    {
        $find = $this->reader->reverse()->offset(0)->limit(10)->get();

        $this->assertCount(10, $find);
        $this->assertEquals('Заголовок20', $find[0]['title']);
        $this->assertEquals('Заголовок14', $find[6]['title']);
        $this->assertEquals('Заголовок11', $find[9]['title']);
    }

    /**
     * Get lines reverse (last 10 lines reversed)
     *
     * @covers ::get()
     */
    public function testOffsetLimitReverse2Get(): void
    {
        $find = $this->reader->offset(0)->limit(10)->reverse()->get();

        $this->assertCount(10, $find);
        $this->assertEquals('Заголовок20', $find[0]['title']);
        $this->assertEquals('Заголовок14', $find[6]['title']);
        $this->assertEquals('Заголовок11', $find[9]['title']);
    }

    /**
     * Get from condition limit and reverse
     *
     * @covers ::get()
     */
    public function testWhereLimitReverse(): void
    {
        $find = $this->reader->where('name', 'Миша')->limit(10)->reverse()->get();

        $this->assertCount(3, $find);
        $this->assertEquals('Заголовок18', $find[0]['title']);
        $this->assertEquals('Заголовок11', $find[1]['title']);
        $this->assertEquals('Заголовок10', $find[2]['title']);
    }

    /**
     * Get headers
     *
     * @covers ::headers()
     */
    public function testHeaders(): void
    {
        $find = $this->reader->headers();

        $this->assertCount(5, $find);
        $this->assertEquals('id', $find[0]);
        $this->assertEquals('name', $find[1]);
        $this->assertEquals('title', $find[2]);
        $this->assertEquals('text', $find[3]);
        $this->assertEquals('time', $find[4]);
    }

    /**
     * Get first line
     *
     * @covers ::first()
     */
    public function testFirst(): void
    {
        $find = $this->reader->first();

        $this->assertCount(5, $find);
        $this->assertArrayHasKey('id', $find);
        $this->assertEquals('Петя', $find['name']);
        $this->assertEquals('Заголовок1', $find['title']);
    }

    /**
     * Get first 3 lines
     *
     * @covers ::first()
     */
    public function testFirst3(): void
    {
        $find = $this->reader->first(3);

        $this->assertCount(3, $find);
        $this->assertArrayHasKey('id', $find[0]);
        $this->assertEquals('Петя', $find[0]['name']);
        $this->assertEquals('Заголовок1', $find[0]['title']);
    }

    /**
     * Get last 3 lines
     *
     * @covers ::first()
     */
    public function testLast3(): void
    {
        $find = $this->reader->reverse()->first(3);

        $this->assertCount(3, $find);
        $this->assertArrayHasKey('id', $find[0]);
        $this->assertEquals('Петя', $find[0]['name']);
        $this->assertEquals('Заголовок20', $find[0]['title']);
    }
}
