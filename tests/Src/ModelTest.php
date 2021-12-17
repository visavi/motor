<?php

namespace Tests\Src;

use App\Models\Test;
use App\Models\Test2;
use App\Models\Test3;
use Tests\TestCase;

/**
 * @coversDefaultClass \App\Model
 */
final class ModelTest extends TestCase
{
    /**
     * Find by primary key
     * @covers ::find()
     */
    public function testFind(): void
    {
        $find = Test::query()->find(17);

        $this->assertIsObject($find);
        $this->assertEquals('17', $find->id);
    }

    /**
     * Find by name limit 1
     * @covers ::where()
     */
    public function testWhereLimit(): void
    {
        $find = Test::query()->where('name', 'Миша')->limit(1)->get();

        $this->assertIsArray($find);
        $this->assertIsObject($find[0]);
        $this->assertObjectHasAttribute('id', $find[0]);
        $this->assertEquals('Миша', $find[0]->name);
        $this->assertEquals('Заголовок10', $find[0]->title);
    }

    /**
     * Find by name and last 1
     *
     * @covers ::where()
     */
    public function testWhereLimitLast(): void
    {
        $find = Test::query()->where('name', 'Миша')->reverse()->first();

        $this->assertIsObject($find);
        $this->assertObjectHasAttribute('attr', $find);
        $this->assertEquals('Миша', $find->name);
        $this->assertEquals('Заголовок18', $find->title);
    }


    /**
     * Find by name and title
     *
     * @covers ::where()
     */
    public function testWhereWhereGet(): void
    {
        $find = Test::query()->where('name', 'Миша')->where('title', 'Заголовок10')->get();

        $this->assertIsArray($find);
        $this->assertIsObject($find[0]);
        $this->assertObjectHasAttribute('id', $find[0]);
        $this->assertEquals('Миша', $find[0]->name);
        $this->assertEquals('Заголовок10', $find[0]->title);
    }

    /**
     * Find by condition
     *
     * @covers ::where()
     */
    public function testWhere(): void
    {
        $find = Test::query()->where('time', '>=', 1231231235)->get();

        $this->assertIsArray($find);
        $this->assertIsObject($find[0]);
        $this->assertCount(3, $find);
        $this->assertObjectHasAttribute('id', $find[0]);
        $this->assertGreaterThanOrEqual('1231231235', $find[0]->time);
        $this->assertGreaterThanOrEqual('1231231235', $find[1]->time);
        $this->assertGreaterThanOrEqual('1231231235', $find[2]->time);
    }

    /**
     * Find by condition in
     *
     * @covers ::whereIn()
     */
    public function testWhereIn(): void
    {
        $find = Test::query()->whereIn('id', [1, 3, 5, 7])->get();

        $this->assertIsArray($find);
        $this->assertCount(4, $find);
        $this->assertEquals('1', $find[0]->id);
        $this->assertEquals('3', $find[1]->id);
        $this->assertEquals('5', $find[2]->id);
        $this->assertEquals('7', $find[3]->id);
    }

    /**
     * Find by condition not in
     *
     * @covers ::whereNotIn()
     */
    public function testWhereNotIn(): void
    {
        $find = Test::query()->whereNotIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10])->get();

        $this->assertIsArray($find);
        $this->assertCount(10, $find);
        $this->assertEquals('11', $find[0]->id);
        $this->assertEquals('12', $find[1]->id);
        $this->assertEquals('13', $find[2]->id);
        $this->assertEquals('14', $find[3]->id);
        $this->assertEquals('15', $find[4]->id);
        $this->assertEquals('16', $find[5]->id);
        $this->assertEquals('17', $find[6]->id);
        $this->assertEquals('18', $find[7]->id);
        $this->assertEquals('19', $find[8]->id);
        $this->assertEquals('20', $find[9]->id);
    }

    /**
     * Get count
     *
     * @covers ::count()
     */
    public function testCount(): void
    {
        $find = Test::query()->count();

        $this->assertEquals(20, $find);
    }

    /**
     * Get where count
     *
     * @covers ::count()
     */
    public function testWhereCount(): void
    {
        $find = Test::query()->where('time', '>', 1231231234)->count();

        $this->assertEquals(3, $find);
    }

    /**
     * Get lines 1 - 10
     *
     * @covers ::get()
     */
    public function testOffsetLimitGet(): void
    {
        $find = Test::query()->offset(0)->limit(10)->get();

        $this->assertCount(10, $find);
        $this->assertEquals('Заголовок1', $find[0]->title);
        $this->assertEquals('Заголовок7', $find[6]->title);
        $this->assertEquals('Заголовок10', $find[9]->title);
    }

    /**
     * Get lines reverse (last 10 lines reversed)
     *
     * @covers ::get()
     */
    public function testOffsetLimitReverseGet(): void
    {
        $find = Test::query()->reverse()->offset(0)->limit(10)->get();

        $this->assertCount(10, $find);
        $this->assertEquals('Заголовок20', $find[0]->title);
        $this->assertEquals('Заголовок14', $find[6]->title);
        $this->assertEquals('Заголовок11', $find[9]->title);
    }

    /**
     * Get lines reverse (last 10 lines reversed)
     *
     * @covers ::get()
     */
    public function testOffsetLimitReverse2Get(): void
    {
        $find = Test::query()->offset(0)->limit(10)->reverse()->get();

        $this->assertCount(10, $find);
        $this->assertEquals('Заголовок20', $find[0]->title);
        $this->assertEquals('Заголовок14', $find[6]->title);
        $this->assertEquals('Заголовок11', $find[9]->title);
    }

    /**
     * Get from condition limit and reverse
     *
     * @covers ::get()
     */
    public function testWhereLimitReverse(): void
    {
        $find = Test::query()->where('name', 'Миша')->limit(10)->reverse()->get();

        $this->assertCount(3, $find);
        $this->assertEquals('Заголовок18', $find[0]->title);
        $this->assertEquals('Заголовок11', $find[1]->title);
        $this->assertEquals('Заголовок10', $find[2]->title);
    }

    /**
     * Get headers
     *
     * @covers ::headers()
     */
    public function testHeaders(): void
    {
        $find = Test::query()->headers();

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
        $find = Test::query()->first();

        $this->assertIsObject($find);
        $this->assertObjectHasAttribute('attr', $find);
        $this->assertEquals('Петя', $find->name);
        $this->assertEquals('Заголовок1', $find->title);
    }

    /**
     * Get first 3 lines
     *
     * @covers ::first()
     */
    public function testFirst3(): void
    {
        $find = Test::query()->limit(3)->get();

        $this->assertCount(3, $find);
        $this->assertObjectHasAttribute('id', $find[0]);
        $this->assertEquals('Петя', $find[0]->name);
        $this->assertEquals('Заголовок1', $find[0]->title);
    }

    /**
     * Get last 3 lines
     *
     * @covers ::first()
     */
    public function testLast3(): void
    {
        $find = Test::query()->reverse()->limit(3)->get();

        $this->assertCount(3, $find);
        $this->assertObjectHasAttribute('id', $find[0]);
        $this->assertEquals('Петя', $find[0]->name);
        $this->assertEquals('Заголовок20', $find[0]->title);
    }

    /**
     * Find by string primary key
     * @covers ::find()
     */
    public function testFindStringKey(): void
    {
        $find = Test2::query()->find('key1');

        $this->assertIsObject($find);
        $this->assertEquals('key1', $find->key);
        $this->assertEquals('500', $find->value);
    }

    /**
     * Find by empty string primary key
     * @covers ::find()
     */
    public function testFindEmptyStringKey(): void
    {
        $find = Test2::query()->find('key3');

        $this->assertIsObject($find);
        $this->assertEquals('key3', $find->key);
        $this->assertEquals('', $find->value);
    }

    /**
     * Get all
     * @covers ::get()
     */
    public function testAllGet(): void
    {
        $find = Test2::query()->get();

        $this->assertCount(5, $find);
        $this->assertObjectHasAttribute('key', $find[0]);
        $this->assertEquals('key1', $find[0]->key);
        $this->assertEquals('500', $find[0]->value);
    }

    /**
     * Insert field
     * @covers ::insert()
     */
    public function testInsert(): void
    {
        $lastInsertId = Test3::query()->insert([
           'name' => 'name1',
           'value' => 555,
        ]);

        $find = Test3::query()->reverse()->first();

        $this->assertEquals($find->id, $lastInsertId);
        $this->assertObjectHasAttribute('attr', $find);
        $this->assertEquals('name1', $find->name);
        $this->assertEquals('555', $find->value);

        Test3::query()->truncate();
    }

    /**
     * Insert multiple fields
     * @covers ::insert()
     */
    public function testMultipleInsert(): void
    {
        foreach ($this->data() as $val) {
            Test3::query()->insert($val);
        }

        $find = Test3::query()->get();

        $this->assertCount(6, $find);
        $this->assertEquals('name3', $find[2]->name);
        $this->assertEquals('value3', $find[2]->value);

        Test3::query()->truncate();
    }

    /**
     * Update fields
     * @covers ::update()
     */
    public function testFindUpdate(): void
    {
        foreach ($this->data() as $val) {
            Test3::query()->insert($val);
        }

        $updatedLines = Test3::query()->find(1)->update(['name' => 'yyy', 'value' => 999]);

        $find = Test3::query()->find(1);

        $this->assertEquals(1, $updatedLines);
        $this->assertEquals('yyy', $find->name);
        $this->assertEquals('999', $find->value);

        Test3::query()->truncate();
    }

    /**
     * Update fields
     * @covers ::update()
     */
    public function testUpdate(): void
    {
        foreach ($this->data() as $val) {
            Test3::query()->insert($val);
        }

        Test3::query()->where('id', 3)->update(['name' => 'xxx', 'value' => 888]);

        $find = Test3::query()->find(3);

        $this->assertEquals('xxx', $find->name);
        $this->assertEquals('888', $find->value);

        Test3::query()->truncate();
    }

    /**
     * Delete fields
     * @covers ::delete()
     */
    public function testDelete(): void
    {
        foreach ($this->data() as $val) {
            Test3::query()->insert($val);
        }
        Test3::query()->where('id', 3)->delete();

        $find = Test3::query()->find(3);

        $this->assertNull($find);

        Test3::query()->truncate();
    }

    /**
     * Truncate fields
     * @covers ::truncate()
     */
    public function testTruncate(): void
    {
        foreach ($this->data() as $val) {
            Test3::query()->insert($val);
        }

        Test3::query()->truncate();

        $find = Test3::query()->get();
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
