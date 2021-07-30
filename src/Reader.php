<?php

namespace App;

use CallbackFilterIterator;
use Closure;
use InvalidArgumentException;
use Iterator;
use LimitIterator;
use SplFileObject;
use SplTempFileObject;
use UnexpectedValueException;

/**
 * Class Reader
 */
class Reader
{
    protected int $offset = 0;
    protected int $limit = -1;
    protected array $headers;
    /** @var int|string */
    protected $primary;
    protected Iterator $iterator;
    protected SplFileObject $file;

    /**
     * Reader constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->open($filePath);
    }

    /**
     * Open file
     *
     * @param $filePath
     *
     * @return $this
     */
    public function open($filePath): self
    {
        $this->file = new SplFileObject($filePath, 'a+');
        $this->file->setFlags(
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE |
            SplFileObject::READ_AHEAD
        );
        $this->file->rewind();

        $this->headers = $this->headers();
        $this->primary = $this->getPrimaryKey();
        $this->iterator = new LimitIterator($this->file, 1);

        return $this;
    }

    /**
     * Reverse iterator
     *
     * @return $this
     */
    public function reverse(): self
    {
        $this->iterator = new ReverseArrayIterator(iterator_to_array($this->iterator));

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

        return str_getcsv($this->file->current());
    }

    /**
     * Get primary key
     *
     * @return int|string
     */
    public function getPrimaryKey()
    {
        return $this->headers[0];
    }

    /**
     * Applies condition
     *
     * @param string $field
     * @param null $operator
     * @param null $value
     *
     * @return $this
     */
    public function where(string $field, $operator = null, $value = null): self
    {
        $value = (string) $value;
        $key   = array_search($field, $this->headers, true);

        if ($key === false) {
            throw new UnexpectedValueException(sprintf('%s() called undefined column. Column "%s" does not exist', __METHOD__, $field));
        }

        if (func_num_args() === 2) {
            $value    = (string) $operator;
            $operator = '=';
        }

        $this->iterator = new CallbackFilterIterator(
            $this->iterator,
            function ($current) use ($key, $operator, $value) {
                return $this->condition(str_getcsv($current)[$key], $operator, $value);
            }
        );

        return $this;
    }

    /**
     * Applies condition
     *
     * @param string $field
     * @param array  $values
     *
     * @return $this
     */
    public function whereIn(string $field, array $values): self
    {
        $values = array_flip($values);
        $key    = array_search($field, $this->headers, true);

        if ($key === false) {
            throw new UnexpectedValueException(sprintf('%s() called undefined column. Column "%s" does not exist', __METHOD__, $field));
        }

        $this->iterator = new CallbackFilterIterator(
            $this->iterator,
            function ($current) use ($key, $values) {
                return isset($values[str_getcsv($current)[$key]]);
            }
        );

        return $this;
    }

    /**
     * Get field by primary key
     *
     * @param int|string $id
     *
     * @return array|bool
     */
    public function find($id)
    {
        $id = (string) $id;
        $this->iterator->rewind();

        while ($this->iterator->valid()) {
            if ($id === str_getcsv($this->iterator->current())[0]) {
                return $this->mapper($this->iterator->current());
            }

            $this->iterator->next();
        }

        return false;
    }

    /**
     * Get fields
     *
     * @return array
     */
    public function get(): array
    {
        return $this->mapper($this->iterator());
    }

    /**
     * Get count fields
     *
     * @return int
     */
    public function count(): int
    {
        return iterator_count($this->iterator());
    }

        /**
     * Get first fields
     *
     * @return array|bool
     */
    public function first()
    {
        $iterator = new LimitIterator($this->iterator, 0, 1);
        $lines    = $this->mapper($iterator);

        return current($lines);
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
     * Insert field
     *
     * @param array $data
     *
     * @return int|string last insert id
     */
    public function insert(array $data)
    {
        $keys = array_fill_keys($this->headers, '');
        $diffKeys = array_diff_key($data, $keys);

        if ($diffKeys) {
            throw new UnexpectedValueException(sprintf('%s() called undefined column. Column "%s" does not exist', __METHOD__, key($diffKeys)));
        }

        if (! $this->file->flock(LOCK_EX)) {
            throw new UnexpectedValueException(sprintf('Unable to obtain lock on file: %s', $this->file->getFilename()));
        }

        $ids = array_column($this->mapper($this->iterator), $this->primary, $this->primary);

        if (! isset($data[$this->primary])) {
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

            $data[$this->primary] = $maxId;
        }

        if (isset($ids[$data[$this->primary]])) {
            throw new UnexpectedValueException(sprintf('%s() duplicate entry. Column "%s" with the value "%s" already exists', __METHOD__, $this->primary, $data[$this->primary]));
        }

        $this->file->fputcsv(array_replace($keys, $data));
        $this->file->flock(LOCK_UN);


        return $data[$this->primary];
    }

    /**
     * Update fields
     *
     * @param array $data
     *
     * @return int affected lines
     */
    public function update(array $data): int
    {
        $affectedLines = 0;
        $ids = array_column($this->mapper($this->iterator), $this->primary, $this->primary);

        if (! $this->file->flock(LOCK_EX)) {
            throw new UnexpectedValueException(sprintf('Unable to obtain lock on file: %s', $this->file->getFilename()));
        }

        $this->file->fseek(0);

        $temp = new SplTempFileObject(-1);
        while(! $this->file->eof()) {
            $temp->fwrite($this->file->fread(1024));
        }

        $temp->rewind();
        $this->file->ftruncate(0);
        $this->file->fseek(0);

        while ($temp->valid()) {
            $current = $temp->current();

            if (isset($ids[str_getcsv($current)[0]])) {
                $map = $this->mapper($current);
                $newData = array_replace($map, $data);

                $this->file->fputcsv($newData);
                $affectedLines++;
            } else {
                $this->file->fwrite($current);
            }

            $temp->next();
        }

        $this->file->flock(LOCK_UN);

        return $affectedLines;
    }

    /**
     * Delete fields
     *
     * @return int affected lines
     */
    public function delete(): int
    {
        $affectedLines = 0;
        $ids = array_column($this->mapper($this->iterator), $this->primary, $this->primary);

        if (! $this->file->flock(LOCK_EX)) {
            throw new UnexpectedValueException(sprintf('Unable to obtain lock on file: %s', $this->file->getFilename()));
        }

        $this->file->fseek(0);

        $temp = new SplTempFileObject(-1);
        while(! $this->file->eof()) {
            $temp->fwrite($this->file->fread(1024));
        }

        $temp->rewind();
        $this->file->ftruncate(0);
        $this->file->fseek(0);

        while ($temp->valid()) {
            $current = $temp->current();

            if (isset($ids[str_getcsv($current)[0]])) {
                $affectedLines++;
            } else {
                $this->file->fwrite($current);
            }

            $temp->next();
        }

        $this->file->flock(LOCK_UN);

        return $affectedLines;
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

            return array_combine($this->headers, $record);
        };
    }

    /**
     * Mapper fields
     *
     * @param array|string|iterable $data
     *
     * @return array
     */
    protected function mapper($data): array
    {
        $combiner = $this->combiner();

        if (is_string($data)) {
            return $combiner(str_getcsv($data));
        }

        $rows = [];
        foreach ($data as $line) {
            $rows[] = $combiner(str_getcsv($line));
        }

        return $rows;
    }

    /**
     * Iterator
     *
     * @return LimitIterator
     */
    protected function iterator(): LimitIterator
    {
        $iterator = new LimitIterator($this->iterator, $this->offset, $this->limit);
        $this->resetProperties();

        return $iterator;
    }

    /**
     * Reset properties
     */
    protected function resetProperties(): void
    {
        $this->offset = 0;
        $this->limit = -1;
        $this->iterator = new LimitIterator($this->file, 1);
    }

    /**
     * Operator condition
     *
     * @param string $field
     * @param string $operator
     * @param $value
     *
     * @return bool
     */
    private function condition(string $field, string $operator, $value): bool
    {
        switch ($operator) {
            case '!=':
            case '<>':
                return $field !== $value;
            case '>=':
                return $field >= $value;
            case '<=':
                return $field <= $value;
            case '>':
                return $field > $value;
            case '<':
                return $field < $value;
            default:
                return $field === $value;
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->file, $this->iterator);
    }
}
