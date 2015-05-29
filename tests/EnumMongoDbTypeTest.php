<?php
/**
 * MongoDbTypeTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 29, 2015, 15:09
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineEnum\Tests;

use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\Types\Type;
use MabeEnum\Enum;

/**
 * Class MongoDbTypeTest
 * @package Dxi\DoctrineEnum\Tests
 */
class EnumMongoDbTypeTest extends \PHPUnit_Framework_TestCase
{
    private $typeName = 'dxi.my_mongo_type_test';

    public function setUp()
    {
        try {
            Type::addType($this->typeName, 'Dxi\DoctrineEnum\Tests\DxiDoctrineEnumTestsMyMongoEnum');
        } catch (MappingException $e) {
            Type::overrideType($this->typeName, 'Dxi\DoctrineEnum\Tests\DxiDoctrineEnumTestsMyMongoEnum');
        }
    }

    /**
     * @test
     */
    public function shouldConvertScalarIntoEnumInstance()
    {
        $type = Type::getType($this->typeName);

        $value = $type->convertToPHPValue('one');
        $this->assertInstanceOf('Dxi\DoctrineEnum\Tests\MyMongoEnum', $value);
    }

    /**
     * @throws DBALException
     * @test
     */
    public function shouldConvertEnumInstanceToScalar()
    {
        /** @var MyEnum $one */
        $one = MyMongoEnum::ONE();

        $type = Type::getType($this->typeName);

        $value = $type->convertToDatabaseValue($one);
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
}

/**
 * Class MyEnum
 * @package Dxi\DoctrineEnum\Tests
 */
class MyMongoEnum extends Enum {
    const ONE = 'one';
    const TWO = 'two';
}

class DxiDoctrineEnumTestsMyMongoEnum extends \Dxi\DoctrineEnum\EnumMongoDbType
{
    public function getName()
    {
        return 'dxi.my_mongo_type_test';
    }

    protected function getEnumClass()
    {
        return 'Dxi\DoctrineEnum\Tests\MyMongoEnum';
    }
}