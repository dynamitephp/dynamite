<?php
declare(strict_types=1);

namespace Dynamite;


use PHPUnit\Framework\TestCase;

class AccessPatternTest extends TestCase
{
    public function testNamedConstructor()
    {
        $pattern = AccessPattern::create('ACP_TEST');

        $this->assertEquals('ACP_TEST', $pattern->getName());
    }

    public function testWithIndex()
    {
        $pattern = AccessPattern::create('ACP_INDEX_TEST');
        $patternWithIndex = $pattern->withIndex('GSI1');

        $this->assertNotSame($pattern, $patternWithIndex);
        $this->assertEquals('GSI1', $patternWithIndex->getIndex());
        $this->assertEquals('ACP_INDEX_TEST', $patternWithIndex->getName());
    }

    public function testPartitionKeyFormat()
    {
        $pattern = AccessPattern::create('ACP_PK_TEST');
        $patternWithPk = $pattern->withPartitionKeyFormat('PK');

        $this->assertNotSame($pattern, $patternWithPk);
        $this->assertEquals('PK', $patternWithPk->getPartitionKeyFormat());
        $this->assertEquals('ACP_PK_TEST', $patternWithPk->getName());
    }

    public function testSortKeyFormat()
    {
        $pattern = AccessPattern::create('ACP_SK_TEST');
        $patternWithSk = $pattern->withSortKeyFormat('SK');

        $this->assertNotSame($pattern, $patternWithSk);
        $this->assertEquals('SK', $patternWithSk->getSortKeyFormat());
        $this->assertEquals('ACP_SK_TEST', $patternWithSk->getName());
    }

    public function testLimit()
    {
        $pattern = AccessPattern::create('ACP_LIMIT_TEST');
        $patternWithLimit = $pattern->withLimit(420);

        $this->assertNotSame($pattern, $patternWithLimit);
        $this->assertEquals(420, $patternWithLimit->getLimit());
        $this->assertEquals('ACP_LIMIT_TEST', $patternWithLimit->getName());
    }
}