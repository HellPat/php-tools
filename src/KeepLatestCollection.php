<?php

namespace Hellpat\Tools;

final class KeepLatestCollection implements \Countable
{
    private array $items;

    /**
     * @param positive-int $limit
     * @param list<mixed> $items
     */
    private function __construct(
        private readonly int $limit,
        array $items,
    ) {
            $this->items = count($items) <= $this->limit
                ? $items
                : array_slice($items, -$this->limit);
    }

    public static function max(int $limit): self
    {
        if ($limit < 1) {
            // the check is here for performance reasons,
            // when chaining stuff the limit is validated only once,
            // and it's readonly then.
            throw new \LogicException('A limit < 1 does not make any sense');
        }

        return new self($limit, []);
    }

    public function append(mixed $added): self
    {
        return $this->appendMany([$added]);
    }

    /**
     * @param array $added
     * @return self
     */
    public function appendMany(array $added): self
    {
        return new self($this->limit, array_merge($this->items, $added));
    }

    #[\Override] public function count(): int
    {
        return count($this->items);
    }

    public function toArray(): array
    {
        return $this->items;
    }
}