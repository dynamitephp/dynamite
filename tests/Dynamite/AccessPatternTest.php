<?php
declare(strict_types=1);

namespace Dynamite;


use PHPUnit\Framework\TestCase;

class AccessPatternTest extends TestCase
{
    public function testNamedConstructor(): void
    {
        $pattern = AccessPattern::create('ACP_TEST');

        self::assertEquals('ACP_TEST', $pattern->getName());
    }

    public function testWithIndex(): void
    {
        $pattern = AccessPattern::create('ACP_INDEX_TEST');
        $patternWithIndex = $pattern->withIndex('GSI1');

        self::assertNotSame($pattern, $patternWithIndex);
        self::assertEquals('GSI1', $patternWithIndex->getIndex());
        self::assertEquals('ACP_INDEX_TEST', $patternWithIndex->getName());
    }

    public function testPartitionKeyFormat(): void
    {
        $pattern = AccessPattern::create('ACP_PK_TEST');
        $patternWithPk = $pattern->withPartitionKeyFormat('PK');

        self::assertNotSame($pattern, $patternWithPk);
        self::assertEquals('PK', $patternWithPk->getPartitionKeyFormat());
        self::assertEquals('ACP_PK_TEST', $patternWithPk->getName());
    }

    public function testSortKeyFormat(): void
    {
        $pattern = AccessPattern::create('ACP_SK_TEST');
        $patternWithSk = $pattern->withSortKeyFormat('SK');

        self::assertNotSame($pattern, $patternWithSk);
        self::assertEquals('SK', $patternWithSk->getSortKeyFormat());
        self::assertEquals('ACP_SK_TEST', $patternWithSk->getName());
    }

    public function testLimit(): void
    {
        $pattern = AccessPattern::create('ACP_LIMIT_TEST');
        $patternWithLimit = $pattern->withLimit(420);

        self::assertNotSame($pattern, $patternWithLimit);
        self::assertEquals(420, $patternWithLimit->getLimit());
        self::assertEquals('ACP_LIMIT_TEST', $patternWithLimit->getName());
    }
}