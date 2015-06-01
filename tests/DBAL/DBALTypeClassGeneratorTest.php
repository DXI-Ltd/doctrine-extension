<?php
/**
 * DBALTypeClassGeneratorTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 16:12
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum\Tests\DBAL;

use Doctrine\DBAL\Types\Type;
use Dxi\DoctrineExtension\Enum\DBAL\DBALTypeClassGenerator;
use MabeEnum\Enum;

/**
 * Class DBALTypeClassGeneratorTest
 * @package Dxi\DoctrineExtension\Enum\Tests\DBAL
 */
class DBALTypeClassGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldGenerateEnumTypeClass()
    {
        $typeName = 'dxi.my_type';
        $namespace = 'Dxi\DoctrineExtension\Enum\\__DBALType';
        $dir = sys_get_temp_dir() . '/' . md5(mt_rand(0, 100000).time());
        $generator = new DBALTypeClassGenerator($dir, $namespace);

        list($className, $file) = $generator->generateTypeClass($typeName, 'Dxi\DoctrineExtension\Enum\Tests\DBAL\MyEnum');
        $this->assertFileExists($file);
        $this->assertEquals('Dxi\DoctrineExtension\Enum\\__DBALType\\DxiDoctrineExtensionEnumTestsDBALMyEnum', $className);
        require $file;

        $this->assertTrue(class_exists($className));
    }
}

class MyEnum extends Enum {
    const ONE = 'one';
    const TWO = 'two';
}
