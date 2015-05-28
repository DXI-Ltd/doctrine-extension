<?php
/**
 * DBALTypeLoader.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 16:26
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineEnum\Tests\DBAL;

use Doctrine\DBAL\Types\Type;
use Dxi\DoctrineEnum\DBAL\DBALTypeLoader;
use MabeEnum\Enum;

/**
 * Class DBALTypeLoader
 * @package Dxi\DoctrineEnum\Tests\DBAL
 */
class DBALTypeLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldLoadEnumType()
    {
        $typeName = 'dxi.my_enum_type';
        $enumClass = 'Dxi\DoctrineEnum\Tests\DBAL\MyNewEnum';

        $generator = $this->createGenerator();
        $generator->expects($this->once())
            ->method('generateDBALTypeClass')
            ->with($this->equalTo($typeName), $this->equalTo($enumClass))
            ->willReturn('Dxi\DoctrineEnum\Tests\DBAL\DxiDoctrineEnumTestsDBALMyEnum');

        $loader = new DBALTypeLoader($generator);
        $loader->registerType($typeName, 'Dxi\DoctrineEnum\Tests\DBAL\MyNewEnum');

        Type::getType($typeName);
    }

    /**
     * @test
     * @expectedException \Dxi\DoctrineEnum\Exception\InvalidEnumClassException
     */
    public function shouldThrowExceptionOnInvalidEnumClass()
    {
        $typeName = 'dxi.my_enum_type';
        $enumClass = 'Dxi\DoctrineEnum\Tests\DBAL\NonExistentEnum';

        $generator = $this->createGenerator();
        $generator->expects($this->never())
            ->method('generateDBALTypeClass');

        $loader = new DBALTypeLoader($generator);
        $loader->registerType($typeName, $enumClass);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Dxi\DoctrineEnum\DBAL\DBALTypeClassGenerator
     */
    private function createGenerator()
    {
        return $this->getMockBuilder('Dxi\DoctrineEnum\DBAL\DBALTypeClassGenerator')->disableOriginalConstructor()->getMock();
    }
}

class MyNewEnum extends Enum {
    const ONE = 'one';
    const TWO = 'two';
}

class DxiDoctrineEnumTestsDBALMyEnum extends \Dxi\DoctrineEnum\EnumDBALType
{
    public function getName()
    {
        return 'dxi.my_enum_type';
    }

    protected static function getEnumClass()
    {
        return 'Dxi\DoctrineEnum\Tests\DBAL\MyNewEnum';
    }
}
