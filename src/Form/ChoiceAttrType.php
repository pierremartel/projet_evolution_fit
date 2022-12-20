<?php

namespace App\Form;


use App\Entity\ProductAttr;
use App\Entity\ProductSize;
use Symfony\Component\Form\AbstractType;
use App\Repository\ProductAttrRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

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
            ->add('quantity', NumberType::class, [
                'label' => false,
                'attr' => ['class' => 'product_show_quantity'],
                'empty_data' => 1
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
