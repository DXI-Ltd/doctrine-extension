<?php
/**
 * DBALTypeClassGeneratorTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 16:12
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineEnum\Tests\DBAL;

use Doctrine\DBAL\Types\Type;
use Dxi\DoctrineEnum\DBAL\DBALTypeClassGenerator;
use MabeEnum\Enum;

/**
 * Class DBALTypeClassGeneratorTest
 * @package Dxi\DoctrineEnum\Tests\DBAL
 */
class DBALTypeClassGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldGenerateEnumTypeClass()
    {
        $type = 'dxi.my_type';
        $namespace = 'Dxi\DoctrineEnum\\__DBALType';
        $dir = sys_get_temp_dir() . '/' . md5(mt_rand(0, 100000).time());
        $generator = new DBALTypeClassGenerator($dir, $namespace);

        $className = $generator->generateDBALTypeClass($type, 'Dxi\DoctrineEnum\Tests\DBAL\MyEnum');
        $this->assertEquals('Dxi\DoctrineEnum\\__DBALType\\DxiDoctrineEnumTestsDBALMyEnum', $className);

        Type::addType($type, $className);
        $objType = Type::getType($type);

        $this->assertInstanceOf($className, $objType);
    }
}

class MyEnum extends Enum {
    const ONE = 'one';
    const TWO = 'two';
}
