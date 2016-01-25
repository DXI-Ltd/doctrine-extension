<?php
/**
 * IdentityResolverTest.php
 *
 * @author dbojdo - Daniel Bojdo <daniel.bojdo@8x8.com>
 * Created on 01 25, 2016, 11:39
 * Copyright (C) 8x8
 */

namespace Dxi\DoctrineExtension\Reference;

use Dxi\DoctrineExtension\Reference\Mapping\Event\ReferencesAdapter;
use Doctrine\Common\Persistence\ObjectManager;

class IdentityResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IdentityResolver
     */
    private $resolver;

    /**
     * @var ReferencesAdapter|\Mockery\MockInterface
     */
    private $adapter;

    /**
     * @var ObjectManager|\Mockery\MockInterface
     */
    private $manager;

    /**
     * @var \stdClass
     */
    private $object;

    protected function setUp()
    {
        $this->resolver = new IdentityResolver();
        $this->adapter = \Mockery::mock('Dxi\DoctrineExtension\Reference\Mapping\Event\ReferencesAdapter');
        $this->manager = \Mockery::mock('Doctrine\Common\Persistence\ObjectManager');
        $this->object = new \stdClass();
    }

    /**
     * @test
     * @param $resolvedId
     * @dataProvider getSingleIdentity
     */
    public function shouldResolveSimpleIdentity($resolvedId)
    {
        $this->adapter
            ->shouldReceive('getIdentifier')
            ->with($this->manager, $this->object, true)
            ->once()
            ->andReturn($resolvedId);

        $this->assertEquals(
            $resolvedId,
            $this->resolver->resolveIdentity(
                $this->adapter,
                $this->manager,
                $this->object,
                true
            )
        );
    }

    /**
     * @test
     * @param $resolvedId
     * @dataProvider getCompositeIdentity
     */
    public function shouldResolveCompositeIdentity($resolvedId)
    {
        $this->adapter
            ->shouldReceive('getIdentifier')
            ->with($this->manager, $this->object, false)
            ->once()
            ->andReturn($resolvedId);

        $this->assertEquals(
            $resolvedId,
            $this->resolver->resolveIdentity(
                $this->adapter,
                $this->manager,
                $this->object,
                false
            )
        );
    }

    /**
     * @test
     */
    public function shouldResolveRecursivelyBeEntityIdentity()
    {
        $entityId = new \stdClass();
        $resolvedId = 123;

        $this->adapter
            ->shouldReceive('getIdentifier')
            ->with($this->manager, $this->object, true)
            ->once()
            ->andReturn($entityId);

        $this->adapter
            ->shouldReceive('getIdentifier')
            ->with($this->manager, $entityId, true)
            ->once()
            ->andReturn($resolvedId);

        $this->assertEquals(
            $resolvedId,
            $this->resolver->resolveIdentity(
                $this->adapter,
                $this->manager,
                $this->object,
                true
            )
        );
    }

    public function getSingleIdentity()
    {
        return array(
            array(123),
            array('123'),
            array(null),
            array('')
        );
    }

    public function getCompositeIdentity()
    {
        return array(
            array(array('id1' => '123', 'id2' => '321')),
            array(null)
        );
    }
}
