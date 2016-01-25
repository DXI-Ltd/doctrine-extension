<?php

namespace Dxi\DoctrineExtension\Reference;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\EventArgs;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Gedmo\Exception\RuntimeException;
use Gedmo\Mapping\Event\AdapterInterface;
use Gedmo\Mapping\MappedEventSubscriber;

/**
 * Listener for loading and persisting cross database references.
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @author Bulat Shakirzyanov <mallluhuct@gmail.com>
 * @author Jonathan H. Wage <jonwage@gmail.com>
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class ReferencesListener extends MappedEventSubscriber
{
    /**
     * @var ManagerRegistry[]
     */
    private $registries;

    /**
     * @var
     */
    private $idResolver;

    public function __construct(array $registries = array())
    {
        $this->registries = $registries;
        $this->idResolver = new IdentityResolver();
    }

    public function loadClassMetadata(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $this->loadMetadataForObjectClass(
            $ea->getObjectManager(), $eventArgs->getClassMetadata()
        );
    }

    public function postLoad(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $om = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta = $om->getClassMetadata(get_class($object));
        $config = $this->getConfiguration($om, $meta->name);
        foreach ($config['referenceOne'] as $mapping) {
            $property = $meta->reflClass->getProperty($mapping['field']);
            $property->setAccessible(true);
            if (isset($mapping['identifier'])) {
                $manager = $this->getManager($mapping['type'], $mapping['class']);

                $referencedObjectId = $this->getReferencedObjectId($config, $mapping, $object, $meta);
                if ($referencedObjectId) {
                    $property->setValue(
                        $object,
                        $ea->getSingleReference(
                            $manager,
                            $mapping['class'],
                            $referencedObjectId
                        )
                    );
                }
            }
        }

        foreach ($config['referenceMany'] as $mapping) {
            $property = $meta->reflClass->getProperty($mapping['field']);
            $property->setAccessible(true);
            if (isset($mapping['mappedBy'])) {
                $id = $ea->extractIdentifier($om, $object);
                $manager = $this->getManager($mapping['type'], $mapping['class']);
                $class = $mapping['class'];
                $refMeta = $manager->getClassMetadata($class);
                $refConfig = $this->getConfiguration($manager, $refMeta->name);
                if (isset($refConfig['referenceOne'][$mapping['mappedBy']])) {
                    $refMapping = $refConfig['referenceOne'][$mapping['mappedBy']];
                    $identifier = $refMapping['identifier'];
                    $property->setValue(
                        $object,
                        new LazyCollection(
                            function () use ($id, &$manager, $class, $identifier) {
                                $results = $manager
                                    ->getRepository($class)
                                    ->findBy(array(
                                        $identifier => $id,
                                    ));

                                return new ArrayCollection((is_array($results) ? $results : $results->toArray()));
                            }
                        )
                    );
                }
            }
        }

        $this->updateManyEmbedReferences($eventArgs);
    }

    public function prePersist(EventArgs $eventArgs)
    {
        $this->updateReferences($eventArgs);
    }

    public function preUpdate(EventArgs $eventArgs)
    {
        $this->updateReferences($eventArgs);
    }

    public function getSubscribedEvents()
    {
        return array(
            'postLoad',
            'loadClassMetadata',
            'prePersist',
            'preUpdate',
        );
    }

    /**
     * @param string $type
     * @param ManagerRegistry $registry
     */
    public function setRegistry($type, ManagerRegistry $registry)
    {
        $this->registries[$type] = $registry;
    }

    /**
     * @param string $type
     * @param string $class
     * @return ObjectManager
     */
    public function getManager($type, $class)
    {
        if (!isset($this->registries[$type])) {
            throw new RuntimeException(
                sprintf('Could not find Registry with required type "%s".', $type)
            );
        }

        $registry = $this->registries[$type];
        foreach ($registry->getManagers() as $manager) {
            try {
                $manager->getClassMetadata($class);

                return $manager;
            } catch (\Exception $e) {
            }
        }

        throw new RuntimeException(
            sprintf('Could not find Manager type "%s" for class "%s".', $type, $class)
        );
    }

    protected function getNamespace()
    {
        return __NAMESPACE__;
    }

    private function updateReferences(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $om = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta = $om->getClassMetadata(get_class($object));
        $config = $this->getConfiguration($om, $meta->name);
        foreach ($config['referenceOne'] as $mapping) {
            if (isset($mapping['identifier'])) {
                $property = $meta->reflClass->getProperty($mapping['field']);
                $property->setAccessible(true);
                $referencedObject = $property->getValue($object);
                if (! is_object($referencedObject)) {
                    continue;
                }

                $identifierFields = explode(',', $mapping['identifier']);
                if (count($identifierFields) == 1) {
                    $meta->setFieldValue(
                        $object,
                        $mapping['identifier'],
                        $this->getIdentifier($ea, $mapping, $referencedObject)
                    );
                    continue;
                }

                // composite key reference
                $id = $this->getIdentifier($ea, $mapping, $referencedObject, false);

                foreach($identifierFields as $idField) {
                    if ($meta->hasField($idField)) {
                        $meta->setFieldValue(
                            $object,
                            $idField,
                            $id[$idField]
                        );
                    } else {
                        $referenceDefinition = $this->getReferenceDefinition($config, $idField);
                        $referenceIdField = $referenceDefinition['identifier'];

                        $referencedObjectProperty = $meta->getReflectionClass()->getProperty($referenceDefinition['field']);
                        $referencedObjectProperty->setAccessible(true);

                        $referencedObject = $referencedObjectProperty->getValue($object);
                        $referencedObjectId = $referencedObject ? $this->getIdentifier($ea, $referenceDefinition, $referencedObject, true) : null;

                        $meta->setFieldValue($object, $referenceIdField, $referencedObjectId);
                    }
                }
            }
        }
        $this->updateManyEmbedReferences($eventArgs);
    }

    public function updateManyEmbedReferences(EventArgs $eventArgs)
    {
        $ea = $this->getEventAdapter($eventArgs);
        $om = $ea->getObjectManager();
        $object = $ea->getObject();
        $meta = $om->getClassMetadata(get_class($object));
        $config = $this->getConfiguration($om, $meta->name);

        foreach ($config['referenceManyEmbed'] as $mapping) {
            $property = $meta->reflClass->getProperty($mapping['field']);
            $property->setAccessible(true);

            $id = $ea->extractIdentifier($om, $object);
            $manager = $this->getManager(RegistryTypes::MONGODB_ODM, $mapping['class']);

            $class = $mapping['class'];
            $refMeta = $manager->getClassMetadata($class);
            // Trigger the loading of the configuration to validate the mapping
            $this->getConfiguration($manager, $refMeta->name);

            $identifier = $mapping['identifier'];
            $property->setValue(
                $object,
                new LazyCollection(
                    function () use ($id, &$manager, $class, $identifier) {
                        $results = $manager
                            ->getRepository($class)
                            ->findBy(array(
                                $identifier => $id,
                            ));

                        return new ArrayCollection((is_array($results) ? $results : $results->toArray()));
                    }
                )
            );
        }
    }

    /**
     * @param array $config
     * @param array $mapping
     * @param $object
     * @param ClassMetadata $meta
     * @return array|mixed|null
     * @throws \Exception
     */
    private function getReferencedObjectId(array $config, array $mapping, $object, ClassMetadata $meta)
    {
        $identifierFields = explode(',', $mapping['identifier']);
        if (count($identifierFields) > 1) {
            $referencedObjectId = array();
            foreach ($identifierFields as $i => $identifierField) {
                if (! $meta->hasField($identifierField)) {
                    $referenceDef = $this->getReferenceDefinition($config, $identifierField);
                    $id = $meta->getFieldValue($object, $referenceDef['identifier']);
                    $referencedObjectId[$identifierField] = $id;
                } else {
                    $id = $meta->getFieldValue($object, $identifierField);
                    $referencedObjectId[$identifierField] = $id;
                }

                if ($id === null) {
                    return null;
                }
            }

            return $referencedObjectId;
        }

        return $meta->getFieldValue($object, $mapping['identifier']);
    }

    private function getReferenceDefinition(array $config, $idField)
    {
        foreach ($config['referenceOne'] as $def) {
            if ($def['field'] == $idField) {
                return $def;
            }
        }

        throw new \Exception(sprintf('Can not find a reference definition for ID field "%s"', $idField));
    }

    /**
     * @param AdapterInterface $ea
     * @param array $mapping
     * @param $referencedObject
     * @param bool $single
     * @return mixed
     */
    private function getIdentifier(AdapterInterface $ea, array $mapping, $referencedObject, $single = true)
    {
        return $this->idResolver->resolveIdentity(
            $ea,
            $this->getManager($mapping['type'], $mapping['class']),
            $referencedObject,
            $single
        );
    }
}
