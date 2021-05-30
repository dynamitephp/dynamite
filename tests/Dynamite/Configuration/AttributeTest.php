<?php
declare(strict_types=1);

namespace Dynamite\Configuration;


use Dynamite\Configuration\Attribute;
use Dynamite\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testAttributeCreationWillFailOnInvalidType(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('"tank" is not a valid value for property "type" in "Dynamite\Configuration\Attribute" annotation.');

        new Attribute('field', 'tank');
    }

    public function testAttributeCreationWillFailOnMissingName(): void
    {
        self::markTestSkipped('To be dropped');
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Missing "name" property in "Dynamite\Configuration\Attribute" annotation.');

        new Attribute(['type' => 'number']);
    }

    public function testAttributeGetters(): void
    {
        $attr = new Attribute('userAge', 'number');
        self::assertEquals('userAge', $attr->getName());
        self::assertEquals('number', $attr->getType());

    }
}