<?php

namespace App\Form;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class ShippingMethodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('shippingMethod', ChoiceType::class, [
                'choices' => [
                    'Standard (2-3 Jours Ouvrables) *une fois que votre commande a été expédiée (5,00€)' => 'Standard',
                    'Express (1-2 Jours Ouvrables) *une fois que votre commande a été expédiée  (10,00€)' => 'Express',
                ],
                'label' => false,
                'expanded' => true,
                'multiple' => false,
                // 'data' => 'Standard',
                
            ])
            
         ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
