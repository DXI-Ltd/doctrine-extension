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
use Dxi\DoctrineEnum\Exception\InvalidEnumClassException;

/**
 * Class DBALTypeLoader
 * @package Dxi\DoctrineEnum\DBAL
 */
class DBALTypeLoader
{
    /**
     * @var DBALTypeClassGenerator
     */
    private $classGenerator;

    /**
     * @param DBALTypeClassGenerator $classGenerator
     */
    public function __construct(DBALTypeClassGenerator $classGenerator)
    {
        $this->classGenerator = $classGenerator;
    }

    /**
     * @param string $typeName
     * @param string $enumClass
     * @throws \Doctrine\DBAL\DBALException
     */
    public function registerType($typeName, $enumClass)
    {
        if (! class_exists($enumClass, true)) {
            throw new InvalidEnumClassException('Given enum class "%s" doesn\'t exist or can not be auto-loaded.');
        }

        $typeClass = $this->classGenerator->generateDBALTypeClass($typeName, $enumClass);

        Type::addType($typeName, $typeClass);
    }
}
