<?php
/**
 * MongoDbTypeRegistrar.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 29, 2015, 14:15
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum\MongoDb;

use Doctrine\ODM\MongoDB\Types\Type;
use Dxi\DoctrineExtension\Enum\Common\AbstractTypeRegistrar;

/**
 * Class MongoDbTypeRegistrar
 * @package Dxi\DoctrineExtension\Enum\MongoDb
 */
class MongoDbTypeRegistrar extends AbstractTypeRegistrar
{
    /**
     * @param string $typeName
     * @param string $typeClass
     */
    protected function addDoctrineType($typeName, $typeClass)
    {
        if (Type::hasType($typeName)) {
            Type::overrideType($typeName, $typeClass);
            return;
        }

        Type::addType($typeName, $typeClass);
    }
}
