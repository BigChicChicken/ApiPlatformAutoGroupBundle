# ApiPlatformAutoGroupBundle

[![Packagist](https://img.shields.io/packagist/v/bigchicchicken/api-platform-auto-group-bundle?style=plastic.svg)](https://packagist.org/packages/bigchicchicken/api-platform-auto-group-bundle)

Bundle to define automatically all groups on all entities for [ApiPlatform](https://api-platform.com/).

## Installation:

Install ApiPlatformAutoGroupBundle library using [Composer](https://getcomposer.org/):

```bash
composer require bigchicchicken/api-platform-auto-group-bundle
```

Add/Check activation in the file `config/bundles.php`:

```php
// config/bundles.php

return [
    // ...
    ApiPlatformAutoGroupBundle\ApiPlatformAutoGroupBundle::class => ['all' => true],
];

```

## Override the default group naming strategy

By default, the following strategy is applied to all entities:

>Normalization groups:
>
>{ShortName}:read
> 
>{ShortName}:read:{OperationName}

>Denormalization groups:
> 
>{ShortName}:write
> 
>{ShortName}:write:{OperationName}

(Default operation name: `GetCollection` `Post` `Get` `Put` `Delete` `Patch`) 

But if you want to use your custom strategy, just override the service like that:

- Create a file that extend from `AutoGroupResourceMetadataCollectionFactory`.

```php
<?php

// src/ResourceMetadataCollectionFactory/CustomResourceMetadataCollectionFactory.php

namespace App\ResourceMetadataCollectionFactory;

use ApiPlatformAutoGroupBundle\ResourceMetadataCollectionFactory\AutoGroupResourceMetadataCollectionFactory;

class CustomResourceMetadataCollectionFactory extends AutoGroupResourceMetadataCollectionFactory
{
    /**
     * {@inheritDoc}
     */
    protected function getDefaultGroups(string $shortName, bool $normalization, string $operationName): array
    {
        return [
            'my_custom_strategy'
        ];
    }
}
```

- And override the service `api_platform.auto_group.resource.metadata_collection_factory` with the previous class.

```yaml
# config/services.yaml

services:
    api_platform.auto_group.resource.metadata_collection_factory:
        class: App\ResourceMetadataCollectionFactory\CustomResourceMetadataCollectionFactory
        decorates: api_platform.metadata.resource.metadata_collection_factory
        arguments:
            - '@.inner'
```

## License

This is completely free and released under the [MIT License](/LICENSE).
