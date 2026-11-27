<?php

namespace Imho\ImageUploadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Reusable form field for uploading an image.
 *
 * Extends Symfony's FileType with an Image constraint, an "accept" attribute matching the allowed types,
 * and an optional NotNull constraint. The file is not mapped to an entity property, so the
 * caller reads it from the form and stores the resulting filename itself.
 *
 * Options max_size and mime_types override the bundle's configured defaults for a single field.
 */
class ImageUploadType extends AbstractType
{
    /** @param list<string> $mimeTypes */
    public function __construct(
        private readonly string $maxSize,
        private readonly array $mimeTypes,
    ) {
    }

    /**
     * Sets the field's defaults from the bundle configuration, and builds the validation constraints
     * from the final option values, so per-field overrides take effect.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,          
            'required' => false,
            'max_size' => $this->maxSize,
            'mime_types' => $this->mimeTypes,
        ]);

        $resolver->setDefault('attr', fn (Options $o): array => ['accept' => implode(',', $o['mime_types'])]);

        $resolver->setDefault('constraints', function (Options $o): array {
            $constraints = [new Image(
                maxSize: $o['max_size'],
                mimeTypes: $o['mime_types'],
                mimeTypesMessage: 'Please upload a valid image (' . implode(', ', $o['mime_types']) . ').',
            )];
            if ($o['required']) {
                array_unshift($constraints, new NotNull(message: 'Please choose an image.'));
            }

            return $constraints;
        });
    }

    /**
     * Inherits all behaviour from FileType.
     */
    public function getParent(): string
    {
        return FileType::class;
    }
}