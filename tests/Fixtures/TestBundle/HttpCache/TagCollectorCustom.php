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

namespace ApiPlatform\Tests\Fixtures\TestBundle\HttpCache;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Serializer\TagCollectorInterface;
use ApiPlatform\Tests\Fixtures\TestBundle\Entity\RelationEmbedder;
use Symfony\Component\PropertyInfo\Type;

/**
 * Collects cache tags during normalization.
 *
 * @author Urban Suppiger <urban@suppiger.net>
 */
class TagCollectorCustom implements TagCollectorInterface
{
    public const IRI_RELATION_DELIMITER = '#';

    public function collect(mixed $object = null, string $format = null, array $context = [], string $iri = null, mixed $data = null, string $attribute = null, ApiProperty $propertyMetadata = null, Type $type = null): void
    {
        if ($object instanceof RelationEmbedder) {
            $iri = '/RE/'.$object->id;
        }

        if ($attribute) {
            $this->addCacheTagsForRelation($context, $iri, $propertyMetadata);
        } elseif (\is_array($data)) {
            $this->addCacheTagForResource($context, $iri);
        }
    }

    private function addCacheTagForResource(array $context, ?string $iri): void
    {
        if (isset($context['resources']) && isset($iri)) {
            $context['resources'][$iri] = $iri;
        }
    }

    private function addCacheTagsForRelation(array $context, ?string $iri, ApiProperty $propertyMetadata): void
    {
        if (isset($context['resources']) && isset($iri)) {
            if (isset($propertyMetadata->getExtraProperties()['cacheDependencies'])) {
                foreach ($propertyMetadata->getExtraProperties()['cacheDependencies'] as $dependency) {
                    $cacheTag = $iri.self::IRI_RELATION_DELIMITER.$dependency;
                    $context['resources'][$cacheTag] = $cacheTag;
                }
            } else {
                $cacheTag = $iri.self::IRI_RELATION_DELIMITER.$context['api_attribute'];
                $context['resources'][$cacheTag] = $cacheTag;
            }
        }
    }
}
