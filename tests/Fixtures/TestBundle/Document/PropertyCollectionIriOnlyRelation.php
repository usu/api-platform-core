<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatform\Tests\Fixtures\TestBundle\Document;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Serializer\Annotation\Groups;

#[
    Post,
    GetCollection(uriTemplate: '/property-collection-relations'),
    GetCollection(
        uriTemplate: '/parent/{parentId}/another-collection-operations',
        uriVariables: [
            'parentId' => new Link(fromProperty: 'propertyCollectionIriOnly', fromClass: PropertyCollectionIriOnly::class),
        ]
    ),
    Get(
        uriTemplate: '/parent/{parentId}/another-collection-operations/{id}',
        uriVariables: [
            'parentId' => new Link(fromProperty: 'propertyCollectionIriOnly', fromClass: PropertyCollectionIriOnly::class),
            'id' => new Link(fromProperty: 'id', toClass: PropertyCollectionIriOnlyRelation::class),
        ]
    )
]
#[ODM\Document]
class PropertyCollectionIriOnlyRelation
{
    #[ODM\Id(strategy: 'INCREMENT', type: 'int')]
    private ?int $id = null;

    #[ODM\Field(type: 'string')]
    #[Groups('read')]
    public string $name = '';

    #[ODM\ReferenceOne(targetDocument: PropertyCollectionIriOnly::class)]
    private ?PropertyCollectionIriOnly $propertyCollectionIriOnly = null;

    public function getId(): ?int
    {
        return $this->id ?? 9999;
    }

    public function getPropertyCollectionIriOnly(): ?PropertyCollectionIriOnly
    {
        return $this->propertyCollectionIriOnly;
    }

    public function setPropertyCollectionIriOnly(?PropertyCollectionIriOnly $propertyCollectionIriOnly): void
    {
        $this->propertyCollectionIriOnly = $propertyCollectionIriOnly;
    }
}
