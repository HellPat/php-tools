<?php

declare(strict_types=1);

namespace Hellpat\Tools;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(KeepLatestCollection::class)]
final class KeepLatestCollectionTest extends TestCase
{
    /**
     * @return iterable<array{0: list<mixed>, 1: KeepLatestCollection}>
     */
    public static function expectations(): iterable
    {
        yield 'empty' => [
            [],
            KeepLatestCollection::max(3),
        ];

        yield 'space left' => [
            [1, 2],
            KeepLatestCollection::max(3)
                ->appendMany([1, 2]),
        ];

        yield 'exactly fits' => [
            [1, 2, 4],
            KeepLatestCollection::max(3)
                ->appendMany([1, 2, 4]),
        ];

        yield 'throws away' => [
            [4, 5, 6],
            KeepLatestCollection::max(3)
                ->appendMany([1, 2, 3, 4])
                ->appendMany([2, 3, 4, 5, 6]),
        ];

        yield 'throws away even if the newly pushed items fit in' => [
            [2, 5, 6],
            KeepLatestCollection::max(3)
                ->appendMany([1, 2])
                ->appendMany([5, 6]),
        ];

        yield 'strange keys' => [
            [
                'two',
                'three',
                'four',
            ],
            KeepLatestCollection::max(3)
                ->appendMany([
                    '1' => 'one',
                    '7' => 'two',
                ])
                ->appendMany([
                    '3' => 'three',
                    '4' => 'four',
                ]),
        ];
    }

    #[DataProvider('expectations')]
    /**
     * @param list<mixed> $expectation
     */
    public function testExpectations(array $expectation, KeepLatestCollection $collection): void
    {
        self::assertSame($expectation, $collection->toArray());
        self::assertSame(count($expectation), $collection->count());
        self::assertCount(count($expectation), $collection);
    }

    public function testWithObjects(): void
    {
        self::assertEquals(
            [
                KeepLatestCollection::max(2),
                KeepLatestCollection::max(3),
                KeepLatestCollection::max(4),
            ],
            KeepLatestCollection::max(3)
                ->append(KeepLatestCollection::max(1))
                ->append(KeepLatestCollection::max(2))
                ->append(KeepLatestCollection::max(3))
                ->append(KeepLatestCollection::max(4))
                ->toArray()
        );
    }
}
