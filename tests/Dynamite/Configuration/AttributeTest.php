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

        new Attribute(['name' => 'field','type' => 'tank']);
    }

    public function testAttributeCreationWillFailOnMissingName(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Missing "name" property in "Dynamite\Configuration\Attribute" annotation.');

        new Attribute(['type' => 'number']);
    }

    public function testAttributeGetters(): void
    {
        $attr = new Attribute(['type' => 'number', 'name' => 'userAge']);
        $this->assertEquals('userAge', $attr->getName());
        $this->assertEquals('number', $attr->getType());

    }
}