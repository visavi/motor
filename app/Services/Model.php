<?php

declare(strict_types=1);

namespace App\Services;

use ArrayIterator;
use CallbackFilterIterator;
use Closure;
use InvalidArgumentException;
use Iterator;
use LimitIterator;
use SplFileObject;
use SplTempFileObject;
use stdClass;
use UnexpectedValueException;

/**
 * File model
 *
 * @license Code and contributions have MIT License
 * @link    https://visavi.net
 * @author  Alexander Grigorev <admin@visavi.net>
 * @version 1.0
 */
abstract class Model
{
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

    public const SORT_TYPES = [
        self::SORT_ASC,
        self::SORT_DESC
    ];

    protected string $filePath;
    protected int $offset = 0;
    protected int $limit = -1;
    protected array $headers;
    protected int|string $primary;
    protected Iterator $iterator;
    protected SplFileObject $file;
    protected array $orders = [];
    protected ?stdClass $attr;

    /**
     * Begin querying the model.
     *
     * @return $this
     */
    public static function query(): self
    {
        return (new static())->open();
    }

    /**
     * Open file
     *
     * @return $this
     */
    public function open(): self
    {
        $this->file = new SplFileObject($this->filePath, 'a+');
        $this->file->setFlags(
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::READ_CSV
        );
        $this->file->rewind();

        $this->headers = $this->headers();
        $this->primary = $this->getPrimaryKey();

        $this->iterator = new LimitIterator($this->file, 1);

        /* Fix drop new line */
        $this->iterator = new CallbackFilterIterator(
            $this->iterator,
            fn ($current) => $current !== [null]
        );

        return $this;
    }

    /**
     * Reverse iterator
     *
     * @return $this
     */
    public function reverse(): self
    {
        $this->iterator = new ArrayIterator(array_reverse(iterator_to_array($this->iterator)));

        return $this;
    }

    /**
     * Get headers
     *
     * @return array
     */
    public function headers(): array
    {
        $this->file->seek(0);

        return $this->file->current();
    }

    /**
     * Get primary key
     *
     * @return int|string
     */
    public function getPrimaryKey(): int|string
    {
        return $this->headers[0];
    }

    /**
     * Where
     *
     * @param string $field
     * @param mixed $operator
     * @param mixed $value
     *
     * @return $this
     */
    public function where(string $field, mixed $operator, mixed $value = null): self
    {
        $key   = $this->getKeyByField($field);
        $value = (string) $value;

        if (func_num_args() === 2) {
            $value    = (string) $operator;
            $operator = '=';
        }

        $this->iterator = new CallbackFilterIterator(
            $this->iterator,
            fn ($current) => $this->condition($current[$key], $operator, $value)
        );

        return $this;
    }

    /**
     * Where in
     *
     * @param string $field
     * @param array  $values
     *
     * @return $this
     */
    public function whereIn(string $field, array $values): self
    {
        $key    = $this->getKeyByField($field);
        $values = array_flip($values);

        $this->iterator = new CallbackFilterIterator(
            $this->iterator,
            fn ($current) => isset($values[$current[$key]])
        );

        return $this;
    }

    /**
     * Where not in
     *
     * @param string $field
     * @param array  $values
     *
     * @return $this
     */
    public function whereNotIn(string $field, array $values): self
    {
        $key    = $this->getKeyByField($field);
        $values = array_flip($values);

        $this->iterator = new CallbackFilterIterator(
            $this->iterator,
            fn ($current) => ! isset($values[$current[$key]])
        );

        return $this;
    }

    /**
     * Sorting by asc
     *
     * @param string      $field
     * @param string|null $sort
     *
     * @return $this
     */
    public function orderBy(string $field, ?string $sort = self::SORT_ASC): self
    {
        if (! in_array($sort, self::SORT_TYPES, true)) {
            throw new InvalidArgumentException(sprintf('%s(), Argument #2 must be a valid sort flag', __METHOD__));
        }

        $this->orders[$field] = $sort;

        return $this;
    }

    /**
     * Sorting by desc
     *
     * @param string $field
     *
     * @return $this
     */
    public function orderByDesc(string $field): self
    {
        $this->orders[$field] = self::SORT_DESC;

        return $this;
    }

    /**
     * Get field by primary key
     *
     * @param int|string $id
     *
     * @return static|null
     */
    public function find(int|string $id): ?self
    {
        $find = $this->where($this->getPrimaryKey(), $id)->first();

        if (! $find) {
            return null;
        }

        return $this;
    }

    /**
     * Get first record
     *
     * @return static|null
     */
    public function first(): ?self
    {
        $this->sorting();
        $this->iterator = new LimitIterator($this->iterator, 0, 1);

        if (! $this->count()) {
            return null;
        }

        $this->attr = $this->mapper($this->iterator)[0];

        return $this;
    }

    /**
     * Get records
     *
     * @return array
     */
    public function get(): array
    {
        $this->sorting();
        $this->iterator = new LimitIterator($this->iterator, $this->offset, $this->limit);

        return $this->mapper($this->iterator);
    }

    /**
     * Get count records
     *
     * @return int
     */
    public function count(): int
    {
        return iterator_count($this->iterator);
    }

    /**
     * Set limit
     *
     * @param int $limit
     *
     * @return $this
     */
    public function limit(int $limit): self
    {
        if ($limit < -1) {
            throw new InvalidArgumentException(sprintf('%s() expects the limit to be greater or equal to -1, %s given', __METHOD__, $limit));
        }

        if ($limit === $this->limit) {
            return $this;
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * Set offset
     *
     * @param int $offset
     *
     * @return $this
     */
    public function offset(int $offset): self
    {
        if ($offset < 0) {
            throw new InvalidArgumentException(sprintf('%s() expects the offset to be a positive integer or 0, %s given', __METHOD__, $offset));
        }

        if ($this->offset === $offset) {
            return $this;
        }

        $this->offset = $offset;

        return $this;
    }

    /**
     * Insert record
     *
     * @param array $values
     *
     * @return int|string last insert id
     */
    public function insert(array $values): int|string
    {
        $fields   = array_fill_keys($this->headers, '');
        $diffKeys = array_diff_key($values, $fields);

        if ($diffKeys) {
            throw new UnexpectedValueException(sprintf('%s() called undefined column. Column "%s" does not exist', __METHOD__, key($diffKeys)));
        }

        if (! $this->file->flock(LOCK_EX)) {
            throw new UnexpectedValueException(sprintf('Unable to obtain lock on file: %s', $this->file->getFilename()));
        }

        $ids = array_column($this->mapper($this->iterator), $this->primary, $this->primary);

        if (! isset($values[$this->primary])) {
            if ($ids) {
                $maxId = max($ids);
                if (is_numeric($maxId)) {
                    ++$maxId;
                } else {
                    throw new UnexpectedValueException(sprintf('%s() no unique ID assigned. Column "%s" cannot be generated', __METHOD__, $this->primary));
                }
            } else {
                $maxId = 1;
            }

            $values[$this->primary] = $maxId;
        }

        if (isset($ids[$values[$this->primary]])) {
            throw new UnexpectedValueException(sprintf('%s() duplicate entry. Column "%s" with the value "%s" already exists', __METHOD__, $this->primary, $values[$this->primary]));
        }

        $this->file->fputcsv(array_replace($fields, $values));
        $this->file->flock(LOCK_UN);


        return $values[$this->primary];
    }

    /**
     * Update records
     *
     * @param array $values
     *
     * @return int affected rows
     */
    public function update(array $values): int
    {
        $diffKeys = array_diff_key($values, array_flip($this->headers));

        if ($diffKeys) {
            throw new UnexpectedValueException(sprintf('%s() called undefined column. Column "%s" does not exist', __METHOD__, key($diffKeys)));
        }

        $updatedRows = 0;
        $ids = array_column($this->mapper($this->iterator), $this->primary, $this->primary);

        if (! $this->file->flock(LOCK_EX)) {
            throw new UnexpectedValueException(sprintf('Unable to obtain lock on file: %s', $this->file->getFilename()));
        }

        $this->file->fseek(0);

        $temp = new SplTempFileObject(-1);
        $temp->setFlags(
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::READ_CSV
        );
        while(! $this->file->eof()) {
            $temp->fwrite($this->file->fread(1024));
        }

        $temp->rewind();
        $this->file->ftruncate(0);
        $this->file->fseek(0);

        while ($temp->valid()) {
            $current = $temp->current();

            if (isset($ids[$current[0]])) {
                $map = (array) $this->mapper($current);

                $this->file->fputcsv(array_replace($map, $values));
                $updatedRows++;
            } else {
                $this->file->fputcsv($current);
            }

            $temp->next();
        }

        $this->file->flock(LOCK_UN);

        return $updatedRows;
    }

    /**
     * Delete records
     *
     * @return int affected rows
     */
    public function delete(): int
    {
        $deletedRows = 0;
        $ids = array_column($this->mapper($this->iterator), $this->primary, $this->primary);

        if (! $this->file->flock(LOCK_EX)) {
            throw new UnexpectedValueException(sprintf('Unable to obtain lock on file: %s', $this->file->getFilename()));
        }

        $this->file->fseek(0);

        $temp = new SplTempFileObject(-1);
        $temp->setFlags(
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::READ_CSV
        );
        while(! $this->file->eof()) {
            $temp->fwrite($this->file->fread(1024));
        }

        $temp->rewind();
        $this->file->ftruncate(0);
        $this->file->fseek(0);

        while ($temp->valid()) {
            $current = $temp->current();

            if (isset($ids[$current[0]])) {
                $deletedRows++;
            } else {
                $this->file->fputcsv($current);
            }

            $temp->next();
        }

        $this->file->flock(LOCK_UN);

        return $deletedRows;
    }

    /**
     * Truncate file
     *
     * @return bool
     */
    public function truncate(): bool
    {
        if (! $this->file->flock(LOCK_EX)) {
            throw new UnexpectedValueException(sprintf('Unable to obtain lock on file: %s', $this->file->getFilename()));
        }

        $this->file->seek(0);
        $this->file->ftruncate($this->file->ftell());
        $this->file->flock(LOCK_UN);

        return true;
    }

    /**
     * @param string $field
     *
     * @return null
     */
    public function __get(string $field){
        return $this->attr->$field ?? null;
    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function __set(string $field, mixed $value): void
    {
        $this->attr->$field = $value;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function __isset(string $field)
    {
        return isset($this->attr->$field);
    }

    /**
     * Combine fields
     *
     * @return Closure
     */
    protected function combiner(): Closure
    {
        $fieldCount = count($this->headers);

        return function (array $record) use ($fieldCount): array {
            if (count($record) !== $fieldCount) {
                $record = array_slice(array_pad($record, $fieldCount, null), 0, $fieldCount);
            }

            $record = array_map(static function ($value) {
                if (is_numeric($value)) {
                    return ! str_contains($value, '.') ? (int) $value : (float) $value;
                }

                if ($value === '') {
                    return null;
                }

                return $value;
            }, $record);

            return array_combine($this->headers, $record);
        };
    }

    /**
     * Mapper fields
     *
     * @param iterable $values
     *
     * @return stdClass[]|stdClass
     */
    protected function mapper(iterable $values): array|object
    {
        $combiner = $this->combiner();

        if (is_array($values)) {
            return (object) $combiner($values);
        }

        $rows = [];
        foreach ($values as $line) {
            $rows[] = (object) $combiner($line);
        }

        return $rows;
    }

    /**
     * Sorting
     *
     * @return void
     */
    private function sorting(): void
    {
        if (! $this->orders) {
            return;
        }

        $this->iterator = new ArrayIterator(iterator_to_array($this->iterator));

        $this->iterator->uasort(
            function($a, $b) {
                $retVal = 0;
                foreach ($this->orders as $field => $sort) {
                    if ($retVal === 0) {
                        if ($sort === self::SORT_ASC) {
                            $retVal = $this->mapper($a)->$field <=> $this->mapper($b)->$field;
                        } else {
                            $retVal = $this->mapper($b)->$field <=> $this->mapper($a)->$field;
                        }
                    }
                }

                return $retVal;
            }
        );
    }

    /**
     * Operator condition
     *
     * @param string $field
     * @param string $operator
     * @param string $value
     *
     * @return bool
     */
    private function condition(string $field, string $operator, string $value): bool
    {
        return match ($operator) {
            '!=', '<>' => $field !== $value,
            '>=' => $field >= $value,
            '<=' => $field <= $value,
            '>' => $field > $value,
            '<' => $field < $value,
            default => $field === $value,
        };
    }

    /**
     * Get key by name
     *
     * @param string $field
     *
     * @return int
     */
    private function getKeyByField(string $field): int
    {
        $key = array_search($field, $this->headers, true);

        if ($key === false) {
            throw new UnexpectedValueException(sprintf('%s() called undefined column. Column "%s" does not exist', __METHOD__, $field));
        }

        return $key;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->file, $this->iterator);
    }
}
