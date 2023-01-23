<?php

namespace App\Form;

use App\Entity\ProductAttr;
use App\Entity\ProductSize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class ProductAttrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', NumberType::class, [
                'label' => 'Quantités',
                // 'mapped' => false,
            ])
            // ->add('productSize', EntityType::class, [
            //     'label' => 'Taille du produit',
            //     // 'mapped' => false,
            //     // 'required' => false,
            //     'class' => ProductAttr::class,
            //     'choice_label' => function (ProductAttr $productAttr) {
            //         return $productAttr->getProductSize()->getSize();
            //     }
            // ])
            ->add('newSize', EntityType::class, [
                'label' => 'Nouvelle taille du produit',
                'mapped' => false,
                // 'required' => false,
                'placeholder' => '--Choisir une nouvelle taille--',
                'class' => ProductSize::class,
                'choice_label' => 'size'
            ])
            // ->add('product')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductAttr::class,
            'csrf_protection' => false,
        ]);
    }
}
