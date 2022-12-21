<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ChoiceAttrType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('size', ChoiceType::class, [
                'choices' => [
                    'S' => 'S',
                    'M' => 'M',
                    'L' => 'L',
                    'XL' => 'XL',
                ],
                'label' => false,
                'expanded' => true,
                'attr' => ['class' => 'radio']
            
                
                
            ])
            // ->add('size', EntityType::class, [
            //     'class' => ProductAttr::class,
            //     'query_builder' => function(ProductAttrRepository $pr) use ($product_id, $product_size_id){
            //         return $pr->findSizeByProduct($product_id, $product_size_id);
            //     }
            // ])
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
