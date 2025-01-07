<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\File;

class BookFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Book Title',
                'constraints' => [
                    new NotBlank(['message' => 'The title cannot be blank']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'The title cannot exceed {{ limit }} characters',
                    ]),
                ],
                'attr' => ['placeholder' => 'Enter the book title'],
            ])
            ->add('author', TextType::class, [
                'label' => 'Author',
                'constraints' => [
                    new NotBlank(['message' => 'The author name cannot be blank']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'The author name cannot exceed {{ limit }} characters',
                    ]),
                ],
                'attr' => ['placeholder' => 'Enter the author\'s name'],
            ])
            ->add('pages', IntegerType::class, [
                'label' => 'Number of Pages',
                'constraints' => [
                    new Positive(['message' => 'The number of pages must be a positive number']),
                ],
                'attr' => ['placeholder' => 'Enter the number of pages'],
            ])
            ->add('genre', TextType::class, [
                'label' => 'Genre',
                'constraints' => [
                    new NotBlank(['message' => 'The genre cannot be blank']),
                    new Length([
                        'max' => 255,
                        'maxMessage' => 'The genre cannot exceed {{ limit }} characters',
                    ]),
                ],
                'attr' => ['placeholder' => 'Enter the genre'],
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Summary',
                'constraints' => [
                    new NotBlank(['message' => 'The summary cannot be blank']),
                    new Length([
                        'max' => 2000,
                        'maxMessage' => 'The summary cannot exceed {{ limit }} characters',
                    ]),
                ],
                'attr' => ['placeholder' => 'Provide a brief summary of the book'],
            ])
            ->add('coverImage', FileType::class, [
                'label' => 'Cover Image',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                    ]),
                ],
                'attr' => ['placeholder' => 'Upload a cover image (optional)'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}



