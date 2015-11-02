<?php
/**
 * TypeRegistrar.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@8x8.com>
 * Created on 11 02, 2015, 09:23
 * Copyright (C) 8x8
 */

namespace Dxi\DoctrineExtension\DBAL;

use Doctrine\DBAL\Types\Type;

class TypesRegistrar
{
    /**
     * @param array $typesMap
     */
    public function __construct(array $typesMap)
    {
        $this->typesMap = $typesMap;
    }

    public function register()
    {
        foreach ($this->typesMap as $className => $typeName) {
            if (Type::hasType($typeName)) {
                Type::overrideType($typeName, $className);

                continue;
            }

            Type::addType($typeName, $className);
        }
    }
}
