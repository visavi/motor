<?php

namespace App;

use CallbackFilterIterator;
use Closure;
use InvalidArgumentException;
use Iterator;
use LimitIterator;
use SplFileObject;
use UnexpectedValueException;

/**
 * Class Reader
 */
class Reader
{
    protected int $offset = 0;
    protected int $limit = -1;
    protected array $headers;
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
        $this->file = new SplFileObject($filePath);
        $this->file->setFlags(
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE |
            SplFileObject::READ_AHEAD
        );
        $this->file->rewind();

        $this->headers = $this->headers();
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
        static $headers;

        if (! $headers) {
            $this->file->seek(0);
            $headers = str_getcsv($this->file->current());
        }

        return $headers;
    }

    /**
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
            $value    = $operator;
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
     * Get field by id
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

    public function count(): int
    {
        return iterator_count($this->iterator());
    }

        /**
     * Get first fields
     *
     * @param int $limit
     *
     * @return array|mixed
     */
    public function first(int $limit = 1): array
    {
        $iterator = new LimitIterator($this->iterator, 0, $limit);
        $lines    = $this->mapper($iterator);

        return $limit === 1 ? current($lines) : $lines;
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
        $mapper = $this->combiner();

        if (is_string($data)) {
            return $mapper(str_getcsv($data));
        }

        $rows = [];
        foreach ($data as $line) {
            $rows[] = $mapper(str_getcsv($line));
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
