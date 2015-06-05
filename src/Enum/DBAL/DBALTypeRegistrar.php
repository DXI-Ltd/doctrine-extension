<?php
/**
 * DBALTypeLoader.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 15:50
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum\DBAL;

use Doctrine\DBAL\Types\Type;
use Dxi\DoctrineExtension\Enum\Common\AbstractTypeRegistrar;

/**
 * Class DBALTypeLoader
 * @package Dxi\DoctrineExtension\Enum\DBAL
 */
class DBALTypeRegistrar extends AbstractTypeRegistrar
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
