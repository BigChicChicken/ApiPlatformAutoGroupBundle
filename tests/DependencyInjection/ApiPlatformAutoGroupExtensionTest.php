<?php

/*
 * This file is part of the ApiPlatformAutoGroupBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatformAutoGroupBundle\Tests\DependencyInjection;

use ApiPlatformAutoGroupBundle\DependencyInjection\ApiPlatformAutoGroupExtension;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Florent TEDESCO
 */
class ApiPlatformAutoGroupExtensionTest extends TestCase
{
    /**
     * @return void
     */
    public function testClassExist(): void
    {
        $this->assertTrue(class_exists(ApiPlatformAutoGroupExtension::class));
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function testLoader(): void
    {
        $container = new ContainerBuilder();
        $loader = new ApiPlatformAutoGroupExtension();
        $loader->load([], $container);

        $resources = array_map(function(FileResource $fileResource) {
            return $fileResource->getResource();
        }, $container->getResources());

        $this->assertTrue(in_array(dirname(__DIR__, 2).'/config/services.yaml', $resources));
    }
}