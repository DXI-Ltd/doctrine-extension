<?php
/**
 * DBALTypeClassGenerator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 15:32
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineEnum\DBAL;

/**
 * Class DBALTypeClassGenerator
 * @package Dxi\DoctrineEnum\DBAL
 */
class DBALTypeClassGenerator
{
    /**
     * @var string
     */
    private $typesDir;

    /**
     * @var string
     */
    private $typesNamespace;

    private static $template = <<<EOD
<?php
namespace %s;

class %s extends \Dxi\DoctrineEnum\EnumDBALType
{
    public function getName()
    {
        return '%s';
    }

    protected static function getEnumClass()
    {
        return '%s';
    }
}
EOD;

    public function __construct($typesDir, $typesNamespace = 'Dxi\DoctrineEnum\__DBALType__')
    {
        $this->typesDir = $typesDir;
        $this->typesNamespace = $typesNamespace;
    }

    /**
     * @param string $typeName
     * @param string $enumClass
     * @return string
     */
    public function generateDBALTypeClass($typeName, $enumClass)
    {
        $className = $this->getClassName($enumClass);
        $dbalTypeClass = $this->typesNamespace .'\\'.$className;

        if (class_exists($dbalTypeClass, true)) {
            return $dbalTypeClass;
        }

        $filename = $this->typesDir .'/'. $className .'.php';

        if (! is_file($filename)) {
            if (! is_dir($this->typesDir)) {
                @mkdir($this->typesDir, 755, true);
            }

            $typeCode = sprintf(self::$template, $this->typesNamespace, $className, $typeName, $enumClass);
            file_put_contents($filename, $typeCode);
        }

        require $filename;

        return $dbalTypeClass;
    }

    /**
     * @param $enumClass
     * @return string
     */
    private function getClassName($enumClass)
    {
        return preg_replace('/\\\\/', '', $enumClass);
    }
}
