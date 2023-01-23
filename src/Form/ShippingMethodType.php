<?php

namespace App\Form;

use App\Entity\PurchaseShipping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ShippingMethodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', EntityType::class, [
                'class' => PurchaseShipping::class,
                'choice_label' => 'nameprice',
                'label' => false,
                'expanded' => true,
                'multiple' => false,
                'constraints' => [
                    new NotNull(),
                ],
                // 'data' => 'Standard',
                
            ])
            
         ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => PurchaseShipping::class,
        ]);
    }
}
