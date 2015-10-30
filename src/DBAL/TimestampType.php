<?php
/**
 * TimestampType.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@8x8.com>
 * Created on Oct 30, 2015, 17:412
 * Copyright (C) 8x8
 */

namespace Dxi\DoctrineExtension\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Class TimestampType
 */
class TimestampType extends Type
{
    const TIMESTAMP = 'timestamp';

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
        return $platform->getIntegerTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value) {
            return \DateTime::createFromFormat('U', $value);
        }

        return null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value ? $value->getTimestamp() : null;
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     *
     * @todo Needed?
     */
    public function getName()
    {
        return self::TIMESTAMP;
    }
}
