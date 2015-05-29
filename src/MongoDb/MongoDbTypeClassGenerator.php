<?php
/**
 * MongoDbTypeClassGenerator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 29, 2015, 10:34
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineEnum\MongoDb;

use Dxi\DoctrineEnum\Common\AbstractTypeGenerator;

/**
 * Class MongoDbTypeClassGenerator
 * @package Dxi\DoctrineEnum\MongoDb
 */
class MongoDbTypeClassGenerator extends AbstractTypeGenerator
{
    private static $template = <<<EOD
<?php
namespace %s;

class %s extends \Dxi\DoctrineEnum\EnumMongoDbType
{
    /**
     * @return string
     */
    protected static function getEnumClass()
    {
        return '%s';
    }
}
EOD;

    /**
     * @param string $className
     * @param string $enumClass
     * @param string $typeName
     * @return string
     */
    protected function generateClassCode($className, $enumClass, $typeName)
    {
        return sprintf(self::$template, $this->typesNamespace, $className, $enumClass);
    }
}
