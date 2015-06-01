<?php
/**
 * EnumDBALType.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@dxi.eu>
 * Created on May 28, 2015, 15:02
 * Copyright (C) DXI Ltd
 */

namespace Dxi\DoctrineExtension\Enum;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Dxi\DoctrineExtension\Enum\Exception\InvalidEnumValueException;
use MabeEnum\Enum;

/**
 * Class EnumDBALType
 * @package Dxi\DoctrineExtension\Enum
 */
abstract class EnumDBALType extends Type
{
    /**
     * @return string
     */
    abstract protected function getEnumClass();

    /**
     * Gets the SQL declaration snippet for a field of this type.
     *
     * @param array $fieldDeclaration The field declaration.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $defaults = array('length' => 32, 'nullable' => false);
        $fieldDeclaration = array_replace($defaults, $fieldDeclaration);

        return Type::getType(Type::STRING)->getSQLDeclaration($fieldDeclaration, $platform);
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed                                     $value    The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
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
     * @param mixed                                     $value    The value to convert.
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value == null ? null : $this->getEnum($value);
    }

    /**
     * @param $value
     * @return Enum
     */
    private function getEnum($value)
    {
        try {
            return call_user_func(sprintf('%s::get', $this->getEnumClass()), $value);
        } catch (\Exception $e) {
            throw new InvalidEnumValueException(sprintf('Enum with value "%s" can not be found.', $value));
        }
    }
}
