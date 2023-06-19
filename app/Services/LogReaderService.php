<?php

declare(strict_types=1);

namespace App\Services;

use ArrayAccess;
use Countable;
use Iterator;
use RuntimeException;
use SplFileObject;

/**
 * LogReaderService
 */
class LogReaderService implements Iterator, ArrayAccess, Countable
{
    protected string $pattern = '/\[(?P<date>.*)\]\s(?P<logger>[\w-]+)\.(?P<level>\w+):\s(?P<message>[^\[\{]+)\s(?P<context>[\[\{].*[\]\}])\s(?P<extra>[\[\{].*[\]\}])/';
    protected SplFileObject $file;

    /**
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = new SplFileObject($file, 'r');
        $this->file->setFlags(
            SplFileObject::READ_AHEAD |
            SplFileObject::SKIP_EMPTY
        );
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return $this->count() < $offset;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $key = $this->file->key();
        $this->file->seek($offset);
        $log = $this->current();
        $this->file->seek($key);
        $this->file->current();

        return $log;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new RuntimeException("Log is read-only.");
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        throw new RuntimeException("Log is read-only.");
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        $this->file->rewind();
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        $this->file->next();
    }

    /**
     * {@inheritdoc}
     */
    public function current(): array
    {
        return $this->parse($this->file->current());
    }

    /**
     * {@inheritdoc}
     */
    public function key(): int
    {
        return $this->file->key();
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->file->valid();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return iterator_count($this->file);
    }

    /**
     * @param string $log
     *
     * @return array
     */
    public function parse(string $log): array
    {
        if (strlen($log) === 0) {
            return [];
        }

        preg_match($this->pattern, $log, $data);

        if (! isset($data['date'])) {
            return [];
        }

        $date = date('d.m.Y H:i:s', strtotime($data['date']));

        return [
            'date'    => $date,
            'logger'  => $data['logger'],
            'level'   => $data['level'],
            'message' => $data['message'],
            'context' => json_decode($data['context'], true),
            'extra'   => json_decode($data['extra'], true)
        ];
    }
}
