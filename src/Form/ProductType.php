<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'required' => false
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du produit',
                'required' => false

            ])
            ->add('picture', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Le format de l\'image importer n\'est pas correct',
                    ])
                ],
            ])
            ->add('size', ChoiceType::class, [
                'label' => 'Taille du produit',
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'placeholder' => '--Choisir une taille--',
                'choices' => [
                    'Aucun' => 'Aucun',
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'required' => false
                
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie du produit',
                'placeholder' => '--Choisir une catégorie--',
                'class' => Category::class,
                'choice_label' => 'name'

            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status du produit',
                'placeholder' => '--Choisir une status--',
                'choices' => [
                    'Disponible' => 'Disponible',
                    'Epuisé' => 'Epuisé',
                    'Nouveauté' => 'Nouveauté',
                    'Promotion' => 'Promotion'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
