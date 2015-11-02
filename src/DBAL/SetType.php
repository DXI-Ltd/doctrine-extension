<?php
/**
 * SetType.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@8x8.com>
 * Created on 11 02, 2015, 10:00
 * Copyright (C) 8x8
 */

namespace Dxi\DoctrineExtension\DBAL;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class SetType extends Type
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
            $setItems = $this->prepareSetItems($fieldDeclaration['columnDefinition']);
            if ($setItems) {
                return sprintf("SET('%s')", implode("', '", $setItems));
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
        return 'set';
    }

    private function prepareSetItems($setItems)
    {
        $arItems = explode(',', $setItems);
        $items = array();
        foreach ($arItems as $item) {
            $item = trim($item);

            if (in_array($item, $items)) {
                throw new \InvalidArgumentException(sprintf('Set item "%s" is duplicated', $item));
            }
        }

        return $items;
    }
}
