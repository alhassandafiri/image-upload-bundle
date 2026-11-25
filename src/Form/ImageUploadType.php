<?php

namespace Imho\ImageUploadBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class ImageUploadType extends AbstractType
{
    /** @param list<string> $mimeTypes */
    public function __construct(
        private readonly string $maxSize,
        private readonly array $mimeTypes,
    ) {
    }

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

    public function getParent(): string
    {
        return FileType::class;
    }
}