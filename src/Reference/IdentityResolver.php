<?php
/**
 * IdentityResolver.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@8x8.com>
 * Created on 01 25, 2016, 11:34
 * Copyright (C) 8x8
 */

namespace Dxi\DoctrineExtension\Reference;

use Doctrine\Common\Persistence\ObjectManager;
use Dxi\DoctrineExtension\Reference\Mapping\Event\ReferencesAdapter;

class IdentityResolver
{
    /**
     * @param ReferencesAdapter $adapter
     * @param ObjectManager $manager
     * @param $object
     * @param bool $single
     * @return mixed|null
     */
    public function resolveIdentity(ReferencesAdapter $adapter, ObjectManager $manager, $object, $single = true)
    {
        $id = $adapter->getIdentifier($manager, $object, $single);

        if (is_object($id)) {
            return $this->resolveIdentity($adapter, $manager, $id, $single);
        }

        return $id;
    }
}
