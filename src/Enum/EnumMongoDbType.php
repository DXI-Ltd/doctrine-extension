<?php
/**
 * EnumMongoDbType.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 29, 2015, 09:28
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum;

use Doctrine\ODM\MongoDB\Types\Type;
use MabeEnum\Enum;

/**
 * Class EnumMongoDbType
 * @package Dxi\DoctrineExtension\Enum
 */
abstract class EnumMongoDbType extends Type
{
    /**
     * @return string
     */
    abstract protected function getEnumClass();

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value)
    {
        if ($value == null) {
            return null;
        }

        if (! ($value instanceof Enum)) {
            $value = $this->getEnum($value);
        }

        return $value->getValue();
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value)
    {
        return $value ? $this->getEnum($value) : null;
    }

    public function closureToMongo()
    {
        return '$return = $value instanceof MabeEnum\\Enum ? $value->getValue() : $value;';
    }

    public function closureToPHP()
    {
        $enumClass = $this->getEnumClass();
        return <<<"FUNC"
        if (\$value === null) {
            \$return = null;
        } else {
            \$callback = function (\$value) {
                foreach (\\$enumClass::getEnumerators() as \$enum) {
                    if (\$enum->getValue() == \$value) {
                        return \$enum;
                    }
                }
                throw new \Dxi\DoctrineExtension\Enum\Exception\InvalidEnumValueException(
                    sprintf('Could not find Enum of type "%s" with value "%s"', "$enumClass", \$value)
                );
            };
            \$return = call_user_func(\$callback, \$value);
        };
FUNC;
    }

    private function getEnumFnc($enumClass, $value)
    {
        $enums = call_user_func("$enumClass::getEnumerators");
        foreach ($enums as $key => $enum) {
            if ($key == $value) {
                return $enum;
            }
        }

        return null;
    }

    /**
     * @param $value
     * @return Enum
     */
    private function getEnum($value)
    {
        $enumClass = $this->getEnumClass();
        foreach ($enumClass::getEnumerators() as $enum) {
            if ($enum->getValue() == $value) {
                return $enum;
            }
        }

        throw new \Dxi\DoctrineExtension\Enum\Exception\InvalidEnumValueException(
            sprintf('Could not find Enum of type "%s" with value "%s"', "$enumClass", $value)
        );
    }
}
