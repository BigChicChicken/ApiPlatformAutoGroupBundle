<?php

/*
* This file is part of the ApiPlatformAutoGroupBundle package.
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

declare(strict_types=1);

namespace ApiPlatformAutoGroupBundle\Tests;

use ApiPlatformAutoGroupBundle\ApiPlatformAutoGroupBundle;
use PHPUnit\Framework\TestCase;

/**
 * @author Florent TEDESCO
 */
class ApiPlatformAutoGroupBundleTest extends TestCase
{
    /**
     * @return void
     */
    public function testClassExist(): void
    {
        $this->assertTrue(class_exists(ApiPlatformAutoGroupBundle::class));
    }

    /**
     * @return void
     */
    public function testExtensionIsLoaded(): void
    {
        $bundle = new ApiPlatformAutoGroupBundle();
        $this->assertNotNull($bundle->getContainerExtension());
    }
}