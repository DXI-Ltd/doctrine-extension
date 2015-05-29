<?php
/**
 * MongoDbTypeRegistrar.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 29, 2015, 14:15
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineEnum\MongoDb;

use Doctrine\ODM\MongoDB\Types\Type;
use Dxi\DoctrineEnum\Common\AbstractTypeRegistrar;

/**
 * Class MongoDbTypeRegistrar
 * @package Dxi\DoctrineEnum\MongoDb
 */
class MongoDbTypeRegistrar extends AbstractTypeRegistrar
{
    /**
     * @param string $typeName
     * @param string $typeClass
     */
    protected function addDoctrineType($typeName, $typeClass)
    {
        Type::addType($typeName, $typeClass);
    }
}
