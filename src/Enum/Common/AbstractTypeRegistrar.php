<?php
/**
 * DBALTypeLoader.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 15:50
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum\Common;

use Dxi\DoctrineExtension\Enum\Exception\InvalidEnumClassException;

/**
 * Class DBALTypeLoader
 * @package Dxi\DoctrineExtension\Enum\DBAL
 */
abstract class AbstractTypeRegistrar
{
    /**
     * @var DBALTypeClassGenerator
     */
    private $generator;

    /**
     * @var array
     */
    private $classMap = array();

    /**
     * @param AbstractTypeGenerator $generator
     */
    final public function __construct(AbstractTypeGenerator $generator)
    {
        $this->generator = $generator;
        spl_autoload_register($this, false);
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

        list($typeClass, $file) = $this->generator->generateTypeClass($typeName, $enumClass);
        $this->classMap[$typeClass] = $file;

        $this->addDoctrineType($typeName, $typeClass);

    }

    /**
     * @param string $typeName
     * @param string $typeClass
     */
    abstract protected function addDoctrineType($typeName, $typeClass);

    /**
     * SplAutoLoad Handler
     * @param string $className
     */
    final public function __invoke($className)
    {
        if (isset($this->classMap[$className])) {
            require $this->classMap[$className];
        }
    }
}
