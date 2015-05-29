<?php
/**
 * DBALTypeLoader.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 15:50
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineEnum\DBAL;

use Doctrine\DBAL\Types\Type;
use Dxi\DoctrineEnum\Common\AbstractTypeRegistrar;

/**
 * Class DBALTypeLoader
 * @package Dxi\DoctrineEnum\DBAL
 */
class DBALTypeRegistrar extends AbstractTypeRegistrar
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
