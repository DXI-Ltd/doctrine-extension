<?php
/**
 * AbstractTypeGenerator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 29, 2015, 10:13
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum\Common;

/**
 * Class AbstractTypeGenerator
 * @package Dxi\DoctrineExtension\Enum\Generator
 */
abstract class AbstractTypeGenerator
{
    /**
     * @var string
     */
    private $typesDir;

    /**
     * @var string
     */
    protected $typeNamespace;

    /**
     * @param $typesDir
     * @param $typesNamespace
     */
    public function __construct($typesDir, $typesNamespace)
    {
        $this->typesDir = $typesDir;
        $this->typesNamespace = $typesNamespace;
    }

    /**
     * @param string $typeName
     * @param string $enumClass
     * @return array
     */
    public function generateTypeClass($typeName, $enumClass)
    {
        $className = $this->getClassName($enumClass);
        $typeClass = $this->typesNamespace .'\\'.$className;

        $filename = $this->typesDir .'/'. $className .'.php';

        if (! is_file($filename)) {
            if (! is_dir($this->typesDir)) {
                @mkdir($this->typesDir, 0755, true);
            }

            $typeCode = $this->generateClassCode($className, $enumClass, $typeName);
            file_put_contents($filename, $typeCode);
        }

        return array($typeClass, $filename);
    }

    /**
     * @param string $className
     * @param string $enumClass
     * @param $typeName
     * @return string
     */
    abstract protected function generateClassCode($className, $enumClass, $typeName);

    /**
     * @param $enumClass
     * @return string
     */
    private function getClassName($enumClass)
    {
        return preg_replace('/\\\\/', '', $enumClass);
    }
}
