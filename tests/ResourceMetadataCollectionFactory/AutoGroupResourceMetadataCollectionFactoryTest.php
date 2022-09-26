<?php

/*
 * This file is part of the ApiPlatformAutoGroupBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatformAutoGroupBundle\Tests\ResourceMetadataCollectionFactory;

use ApiPlatform\Exception\ResourceClassNotFoundException;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatformAutoGroupBundle\ResourceMetadataCollectionFactory\AutoGroupResourceMetadataCollectionFactory;
use ApiPlatformAutoGroupBundle\Tests\Application\Entity\Book;
use ApiPlatformAutoGroupBundle\Tests\Application\ResourceMetadataCollectionFactory\CustomResourceMetadataCollectionFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * @author Florent TEDESCO
 */
class AutoGroupResourceMetadataCollectionFactoryTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testClassExist(): void
    {
        $this->assertTrue(class_exists(AutoGroupResourceMetadataCollectionFactory::class));
    }

    /**
     * @return void
     * @throws ResourceClassNotFoundException
     */
    public function testServiceUsable(): void
    {
        $kernel = self::createKernel();
        $kernel->boot();
        $container = $kernel->getContainer();

        // Check if the service is declared

        $this->assertTrue($container->has('api_platform.auto_group.resource.metadata_collection_factory'));

        $factory = $container->get('api_platform.auto_group.resource.metadata_collection_factory');
        $this->assertInstanceOf(AutoGroupResourceMetadataCollectionFactory::class, $factory);

        // Check if groups are implemented correctly

        $resourceMetadataCollection = $factory->create(Book::class);

        $pattern = '/^\w+:(?>read|write)(?>:\w+)?$/';

        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();

            if ($operations) {
                /** @var Operation $operation */
                foreach ($operations as $operation) {
                    $normalizationContext = $operation->getNormalizationContext();
                    if (isset($normalizationContext['groups'])) {
                        foreach ($normalizationContext['groups'] as $group) {
                            $this->assertMatchesRegularExpression($pattern, $group);
                        }
                    }

                    $denormalizationContext = $operation->getDenormalizationContext();
                    if (isset($denormalizationContext['groups'])) {
                        foreach ($denormalizationContext['groups'] as $group) {
                            $this->assertMatchesRegularExpression($pattern, $group);
                        }
                    }
                }
            }
        }

        $this->tearDown();
    }

    /**
     * @return void
     * @throws ResourceClassNotFoundException
     */
    public function testServiceOverridable(): void
    {
        $kernel = self::createKernel();
        $kernel->boot(function (ContainerBuilder $container) {
            $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__, 1).'/Application/config'));
            $loader->load('override.yaml');
        });
        $container = $kernel->getContainer();

        // Check if the service is declared

        $this->assertTrue($container->has('api_platform.auto_group.resource.metadata_collection_factory'));

        $factory = $container->get('api_platform.auto_group.resource.metadata_collection_factory');

        $this->assertInstanceOf(CustomResourceMetadataCollectionFactory::class, $factory);

        // Check if groups are implemented correctly

        $resourceMetadataCollection = $factory->create(Book::class);

        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();

            if ($operations) {
                /** @var Operation $operation */
                foreach ($operations as $operation) {
                    $normalizationContext = $operation->getNormalizationContext();
                    if (isset($normalizationContext['groups'])) {
                        foreach ($normalizationContext['groups'] as $group) {
                            $this->assertEquals(CustomResourceMetadataCollectionFactory::CUSTOM_GROUP, $group);
                        }
                    }

                    $denormalizationContext = $operation->getDenormalizationContext();
                    if (isset($denormalizationContext['groups'])) {
                        foreach ($denormalizationContext['groups'] as $group) {
                            $this->assertEquals(CustomResourceMetadataCollectionFactory::CUSTOM_GROUP, $group);
                        }
                    }
                }
            }
        }

        $this->tearDown();
    }
}