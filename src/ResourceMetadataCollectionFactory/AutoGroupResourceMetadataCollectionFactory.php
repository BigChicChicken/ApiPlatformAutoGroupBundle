<?php

/*
 * This file is part of the ApiPlatformAutoGroupBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatformAutoGroupBundle\ResourceMetadataCollectionFactory;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;
use ReflectionClass;

/**
 * Assigns predefined groups to all operations.
 *
 * @author Florent TEDESCO
 */
class AutoGroupResourceMetadataCollectionFactory implements ResourceMetadataCollectionFactoryInterface
{
    /**
     * @var ResourceMetadataCollectionFactoryInterface
     */
    private ResourceMetadataCollectionFactoryInterface $decorated;

    /**
     * @param ResourceMetadataCollectionFactoryInterface $decorated
     */
    public function __construct(ResourceMetadataCollectionFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $resourceClass): ResourceMetadataCollection
    {
        $resourceMetadataCollection = $this->decorated->create($resourceClass);

        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();

            if ($operations) {
                /**
                 * @var string    $operationName
                 * @var Operation $operation
                 */
                foreach ($operations as $operationName => $operation) {
                    $operations->add($operationName, $this->updateContextOnOperation($operationName, $operation, $resourceMetadata->getShortName()));
                }
            }
        }

        return $resourceMetadataCollection;
    }

    /**
     * Modifies the context to add groups.
     *
     * @param string $operationName
     * @param Operation $operation
     * @param string $shortName
     *
     * @return Operation
     */
    private function updateContextOnOperation(string $operationName, Operation $operation, string $shortName): Operation
    {
        if ($operation instanceof HttpOperation) {
            $defaultName = sprintf('_api_%s_%s%s', $operation->getUriTemplate() ?: $operation->getShortName(), strtolower($operation->getMethod() ?? HttpOperation::METHOD_GET), $operation instanceof CollectionOperationInterface ? '_collection' : '');
            if ($operationName === $defaultName) {
                $reflectionClass = new ReflectionClass($operation);
                $operationName = $reflectionClass->getShortName();
            }
        }

        $normalizationContext = $operation->getNormalizationContext() ?? [];
        $normalizationContext['groups'] = array_unique(array_merge(
            $normalizationContext['groups'] ?? [],
                $this->getDefaultGroups($shortName, true, $operationName)
        ));

        $denormalizationContext = $operation->getDenormalizationContext() ?? [];
        $denormalizationContext['groups'] = array_unique(array_merge(
            $denormalizationContext['groups'] ?? [],
                $this->getDefaultGroups($shortName, false, $operationName)
        ));

        return $operation
            ->withNormalizationContext($normalizationContext)
            ->withDenormalizationContext($denormalizationContext)
        ;
    }

    /**
     * Get predefined groups.
     *
     * Normalization groups:
     * <short_name>:read
     * <short_name>:read:<operation_name>
     *
     * Denormalization groups:
     * <short_name>:write
     * <short_name>:write:<operation_name>
     *
     * @param string $shortName
     * @param bool   $normalization
     * @param string $operationName
     *
     * @return array
     */
    protected function getDefaultGroups(string $shortName, bool $normalization, string $operationName): array
    {
        $mode = ($normalization) ? 'read' : 'write';

        return [
            "$shortName:$mode",
            "$shortName:$mode:$operationName"
        ];
    }
}