<?php

declare(strict_types=1);

namespace Hellpat\Tools;

use function Psl\Vec\values;

/**
 * @template T
 */
final class KeepLatestCollection implements \Countable
{
    /**
     * @var list<T>
     */
    private array $items;

    /**
     * @param positive-int $limit
     * @param list<T> $items
     */
    private function __construct(
        private readonly int $limit,
        array $items,
    ) {
        $this->items = array_slice($items, -$this->limit);
    }

    /**
     * @param positive-int $limit
     * @return self<T>
     */
    public static function max(int $limit): self
    {
        return new self($limit, []);
    }

    /**
     * @param T $added
     * @return self<T>
     */
    public function append(mixed $added): self
    {
        return $this->appendMany([$added]);
    }

    /**
     * @param array<T> $added
     * @return self<T>
     */
    public function appendMany(array $added): self
    {
        return count($added) < $this->limit
            ? new self($this->limit, values(array_merge($this->items, $added)))
            : new self($this->limit, values($added));
    }

    #[\Override]
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return list<T>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
