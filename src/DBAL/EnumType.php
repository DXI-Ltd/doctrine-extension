<?php
/**
 * EnumType.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@8x8.com>
 * Created on 11 02, 2015, 10:00
 * Copyright (C) 8x8
 */

namespace Dxi\DoctrineExtension\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EnumType extends Type
{

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
        if ($platform->getName() == 'mysql') {
            $enums = $this->prepareEnums($fieldDeclaration['columnDefinition']);
            if ($enums) {
                return sprintf("ENUM('%s')", implode("', '", $enums));
            }
        }

        unset($fieldDeclaration['columnDefinition']);
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
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
        return 'enum';
    }

    private function prepareEnums($enumString)
    {
        $arEnums = explode(',', $enumString);
        $enums = array();
        foreach ($arEnums as $enum) {
            $enum = trim($enum);

            if (in_array($enum, $enums)) {
                throw new \InvalidArgumentException(sprintf('Enum "%s" is duplicated', $enum));
            }
        }

        return $enums;
    }
}
