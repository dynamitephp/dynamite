<?php
declare(strict_types=1);

namespace Dynamite\Exception;


use PHPUnit\Framework\TestCase;

class FormatExceptionTest extends TestCase
{

    public function testFilterNotFoundMessage(): void
    {
        $ex = FormatException::filterNotFound('hello');

        self::assertSame('Filter "hello" not found.', $ex->getMessage());
    }

}