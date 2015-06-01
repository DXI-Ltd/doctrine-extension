<?php
/**
 * MongoDbTypeClassGenerator.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 29, 2015, 10:34
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum\MongoDb;

use Dxi\DoctrineExtension\Enum\Common\AbstractTypeGenerator;

/**
 * Class MongoDbTypeClassGenerator
 * @package Dxi\DoctrineExtension\Enum\MongoDb
 */
class MongoDbTypeClassGenerator extends AbstractTypeGenerator
{
    private static $template = <<<EOD
<?php
namespace %s;

class %s extends \Dxi\DoctrineExtension\Enum\EnumMongoDbType
{
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
     * @param string $typeName
     * @return string
     */
    protected function generateClassCode($className, $enumClass, $typeName)
    {
        return sprintf(self::$template, $this->typesNamespace, $className, $enumClass);
    }
}
