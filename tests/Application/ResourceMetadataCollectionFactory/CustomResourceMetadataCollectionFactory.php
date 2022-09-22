<?php

/*
 * This file is part of the ApiPlatformAutoGroupBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatformAutoGroupBundle\Tests\Application\ResourceMetadataCollectionFactory;

use ApiPlatformAutoGroupBundle\ResourceMetadataCollectionFactory\AutoGroupResourceMetadataCollectionFactory;

class CustomResourceMetadataCollectionFactory extends AutoGroupResourceMetadataCollectionFactory
{
    const CUSTOM_GROUP = 'my_custom_strategy';

    /**
     * {@inheritDoc}
     */
    protected function getDefaultGroups(string $shortName, bool $normalization, string $operationName): array
    {
        return [
            self::CUSTOM_GROUP
        ];
    }
}