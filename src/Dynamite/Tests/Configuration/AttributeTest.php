<?php
declare(strict_types=1);

namespace Dynamite\Tests\Configuration;


use Dynamite\Configuration\Attribute;
use Dynamite\Exception\ConfigurationException;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testAttributeCreationWillFailOnInvalidType()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('"tank" is not a valid value for property "type" in "Dynamite\Configuration\Attribute" annotation.');

        new Attribute(['name' => 'field','type' => 'tank']);
    }

    public function testAttributeCreationWillFailOnMissingName()
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Missing "name" property in "Dynamite\Configuration\AbstractAttribute" annotation.');

        new Attribute(['type' => 'number']);
    }

    public function testAttributeGetters()
    {
        $attr = new Attribute(['type' => 'number', 'name' => 'userAge']);
        $this->assertEquals('userAge', $attr->getName());
        $this->assertEquals('number', $attr->getType());

    }
}