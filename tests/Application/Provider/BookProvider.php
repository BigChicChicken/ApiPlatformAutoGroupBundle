<?php

/*
 * This file is part of the ApiPlatformAutoGroupBundle package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatformAutoGroupBundle\Tests\Application\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatformAutoGroupBundle\Tests\Application\Entity\Book;

/**
 * @author Florent TEDESCO
 */
final class BookProvider implements ProviderInterface
{
    /**
     * @return Book
     */
    static function getBook(): Book
    {
        $book = new Book();
        $book
            ->setAuthor('Author')
            ->setTitle('Title')
            ->setDescription('Description')
        ;

        return $book;
    }

    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|iterable|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return [self::getBook(), self::getBook()];
        }

        return self::getBook();
    }
}