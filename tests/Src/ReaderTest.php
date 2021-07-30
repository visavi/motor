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
    private Reader $reader2;
    private Reader $reader3;

    public function setUp(): void
    {
        parent::setUp();
        $this->reader = new Reader('tests/data/test.csv');
        $this->reader2 = new Reader('tests/data/test2.csv');
        $this->reader3 = new Reader('tests/data/test3.csv');
    }

    /**
     * @covers ::__construct()
     */
    public function testReader(): void
    {
        $this->assertInstanceOf(Reader::class, $this->reader);
        $this->assertInstanceOf(Reader::class, $this->reader2);
        $this->assertInstanceOf(Reader::class, $this->reader3);
        $this->assertNotEquals($this->reader, $this->reader2);
    }

    /**
     * Find by primary key
     * @covers ::find()
     */
    public function testFind(): void
    {
        $find = $this->reader->find(17);

        $this->assertIsArray($find);
        $this->assertArrayHasKey('id', $find);
        $this->assertEquals('17', $find['id']);
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
     * Find by condition in
     *
     * @covers ::whereIn()
     */
    public function testWhereIn(): void
    {
        $find = $this->reader->whereIn('id', [1, 3, 5, 7])->get();

        $this->assertIsArray($find);
        $this->assertCount(4, $find);
        $this->assertEquals('1', $find[0]['id']);
        $this->assertEquals('3', $find[1]['id']);
        $this->assertEquals('5', $find[2]['id']);
        $this->assertEquals('7', $find[3]['id']);
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
        $find = $this->reader->limit(3)->get();

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
        $find = $this->reader->reverse()->limit(3)->get();

        $this->assertCount(3, $find);
        $this->assertArrayHasKey('id', $find[0]);
        $this->assertEquals('Петя', $find[0]['name']);
        $this->assertEquals('Заголовок20', $find[0]['title']);
    }

    /**
     * Find by string primary key
     * @covers ::find()
     */
    public function testFindStringKey(): void
    {
        $find = $this->reader2->find('key1');

        $this->assertIsArray($find);
        $this->assertArrayHasKey('key', $find);
        $this->assertEquals('key1', $find['key']);
        $this->assertEquals('500', $find['value']);
    }

    /**
     * Find by empty string primary key
     * @covers ::find()
     */
    public function testFindEmptyStringKey(): void
    {
        $find = $this->reader2->find('key3');

        $this->assertIsArray($find);
        $this->assertArrayHasKey('key', $find);
        $this->assertEquals('key3', $find['key']);
        $this->assertEquals('', $find['value']);
    }

    /**
     * Get all
     * @covers ::get()
     */
    public function testAllGet(): void
    {
        $find = $this->reader2->get();

        $this->assertCount(5, $find);
        $this->assertArrayHasKey('key', $find[0]);
        $this->assertEquals('key1', $find[0]['key']);
        $this->assertEquals('500', $find[0]['value']);
    }

    /**
     * Insert field
     * @covers ::insert()
     */
    public function testInsert(): void
    {
        $lastInsertId = $this->reader3->insert([
           'name' => 'name1',
           'value' => 555,
        ]);

        $find = $this->reader3->reverse()->first();

        $this->assertEquals($find['id'], $lastInsertId);
        $this->assertArrayHasKey('name', $find);
        $this->assertEquals('name1', $find['name']);
        $this->assertEquals('555', $find['value']);

        $this->reader3->truncate();
    }

    /**
     * Insert multiple fields
     * @covers ::insert()
     */
    public function testMultipleInsert(): void
    {
        foreach ($this->data() as $val) {
            $this->reader3->insert($val);
        }

        $find = $this->reader3->get();

        $this->assertCount(6, $find);
        $this->assertEquals('name3', $find[2]['name']);
        $this->assertEquals('value3', $find[2]['value']);

        $this->reader3->truncate();
    }

    /**
     * Update fields
     * @covers ::update()
     */
    public function testUpdate(): void
    {
        foreach ($this->data() as $val) {
            $this->reader3->insert($val);
        }
        $this->reader3->where('id', 3)->update(['name' => 'xxx', 'value' => 888]);

        $find = $this->reader3->find(3);

        $this->assertEquals('xxx', $find['name']);
        $this->assertEquals('888', $find['value']);

        $this->reader3->truncate();
    }

    /**
     * Delete fields
     * @covers ::delete()
     */
    public function testDelete(): void
    {
        foreach ($this->data() as $val) {
            $this->reader3->insert($val);
        }
        $this->reader3->where('id', 3)->delete();

        $find = $this->reader3->find(3);

        $this->assertEquals(false, $find);

        $this->reader3->truncate();
    }

    /**
     * Truncate fields
     * @covers ::truncate()
     */
    public function testTruncate(): void
    {
        foreach ($this->data() as $val) {
            $this->reader3->insert($val);
        }

        $this->reader3->truncate();

        $find = $this->reader3->get();
        $this->assertCount(0, $find);
    }

    /**
     * @return array
     */
    private function data(): array
    {
        return [
            [
                'name' => 'name1',
                'value' => 555,
            ],
            [
                'name' => 'name2',
                'value' => 777,
            ],
            [
                'name' => 'name3',
                'value' => 'value3',
            ],
            [
                'name' => 'name4',
                'value' => null,
            ],
            [
                'name' => 'name5',
            ],
            [
                'name' => 'name6',
                'value' => ',',
            ],
        ];
    }
}
