<?php

namespace App\Form;

use App\Entity\Purchase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CartConfirmationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Adresse@email.com'],
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Nom de famille'],
                'required' => false,
            ])
            ->add('firstname',  TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Prénom'],
                'required' => false,
            ])
            ->add('address', TextareaType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Adresse complète pour la livraison'],
                'required' => false,
            ])
            ->add('postalCode', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Code postal'],
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Ville'],
                'required' => false,
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Téléphone (facultatif)']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Purchase::class
            // Configure your form options here
        ]);
    }
}
