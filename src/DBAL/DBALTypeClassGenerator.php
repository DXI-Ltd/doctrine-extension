<?php
/**
 * DBALTypeClassGenerator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 15:32
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineEnum\DBAL;

use Dxi\DoctrineEnum\Common\AbstractTypeGenerator;

/**
 * Class DBALTypeClassGenerator
 * @package Dxi\DoctrineEnum\DBAL
 */
class DBALTypeClassGenerator extends AbstractTypeGenerator
{
    private static $template = <<<EOD
<?php
namespace %s;

class %s extends \Dxi\DoctrineEnum\EnumDBALType
{
    /**
     * @return string
     */
    public function getName()
    {
        return '%s';
    }

    /**
     * @return string
     */
    protected function getEnumClass()
    {
        return '%s';
    }
}
EOD;

    /**
     * @param string $className
     * @param string $enumClass
     * @param $typeName
     * @return string
     */
    protected function generateClassCode($className, $enumClass, $typeName)
    {
        return sprintf(self::$template, $this->typesNamespace, $className, $typeName, $enumClass);
    }
}
