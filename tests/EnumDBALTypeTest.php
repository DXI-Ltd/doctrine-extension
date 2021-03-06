<?php
/**
 * EnumDBALTypeTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 16:45
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum\Tests;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use MabeEnum\Enum;

/**
 * Class EnumDBALTypeTest
 * @package Dxi\DoctrineExtension\Enum\Tests
 */
class EnumDBALTypeTest extends \PHPUnit_Framework_TestCase
{
    private $typeName = 'dxi.my_type_test';

    public function setUp()
    {
        try {
            Type::addType($this->typeName, 'Dxi\DoctrineExtension\Enum\Tests\DxiDoctrineEnumTestsDBALMyEnum');
        } catch (DBALException $e) {
            Type::overrideType($this->typeName, 'Dxi\DoctrineExtension\Enum\Tests\DxiDoctrineEnumTestsDBALMyEnum');
        }
    }

    /**
     * @test
     */
    public function shouldConvertScalarIntoEnumInstance()
    {
        $platform = $this->createPlatform();

        $type = Type::getType($this->typeName);

        $value = $type->convertToPHPValue('one', $platform);
        $this->assertInstanceOf('Dxi\DoctrineExtension\Enum\Tests\MyEnum', $value);
    }

    /**
     * @throws DBALException
     * @test
     */
    public function shouldConvertEnumInstanceToScalar()
    {
        /** @var MyEnum $one */
        $one = MyEnum::ONE();

        $platform = $this->createPlatform();

        $type = Type::getType($this->typeName);

        $value = $type->convertToDatabaseValue($one, $platform);
        $this->assertEquals($one->getValue(), $value);
    }

    /**
     * @throws DBALException
     * @test
     */
    public function shouldReturnValidName()
    {
        $type = Type::getType($this->typeName);
        $this->assertEquals($this->typeName, $type->getName());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Doctrine\DBAL\Platforms\AbstractPlatform
     */
    private function createPlatform()
    {
        return $this->getMockBuilder('Doctrine\DBAL\Platforms\AbstractPlatform')->disableOriginalConstructor()->getMock();
    }
}

/**
 * Class MyEnum
 * @package Dxi\DoctrineExtension\Enum\Tests
 */
class MyEnum extends Enum {
    const ONE = 'one';
    const TWO = 'two';
}

class DxiDoctrineEnumTestsDBALMyEnum extends \Dxi\DoctrineExtension\Enum\EnumDBALType
{
    public function getName()
    {
        return 'dxi.my_type_test';
    }

    protected function getEnumClass()
    {
        return 'Dxi\DoctrineExtension\Enum\Tests\MyEnum';
    }
}