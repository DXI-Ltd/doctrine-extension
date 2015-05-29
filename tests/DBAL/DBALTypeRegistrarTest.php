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
use Dxi\DoctrineEnum\DBAL\DBALTypeClassGenerator;
use Dxi\DoctrineEnum\DBAL\DBALTypeRegistrar;
use MabeEnum\Enum;

/**
 * Class DBALTypeLoader
 * @package Dxi\DoctrineEnum\Tests\DBAL
 */
class DBALTypeRegistrarTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldLoadEnumType()
    {
        $typeName = 'dxi.my_enum_type';

        $generator = new DBALTypeClassGenerator(sys_get_temp_dir() .'/'. md5(mt_rand().time()), 'Dxi\DoctrineEnum\Tests\DBAL');

        $loader = new DBALTypeRegistrar($generator);
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
            ->method('generateTypeClass');

        $loader = new DBALTypeRegistrar($generator);
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