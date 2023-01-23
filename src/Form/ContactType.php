<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom*',
                'attr' => [
                    'placeholder' => 'Entrer votre nom',
                    'class' => 'input__design'
                ],
                'required' => false
            ])
            ->add('email',  EmailType::class, [
                'label' => 'Email*',
                'attr' => [
                    'placeholder' => 'Entrer votre adresse email',
                    'class' => 'input__design'
                ],
                'required' => false
            ])
            ->add('subject',   ChoiceType::class, [
                'label' => 'Sujet*',
                'placeholder' => 'Selectionner un sujet',
                'choices' => [
                    'Ma commande' => 'Ma commande',
                    'Livraison' => 'Livraison',
                    'Retour' => 'Retour',
                    'Paiement' => 'Paiement',
                    'Problèmes sur le site' => 'Problèmes sur le site',
                    'Recrutement' => 'Recrutement',
                    'Autre' => 'Autre'
                ],
                'required' => false,
                'attr' => ['class' => 'input__design']
            ])
            ->add('purchase', TextType::class, [
                'label' => 'Numéro de commande',
                'attr' => [
                    'placeholder' => 'Entrer votre numéro de commande ( ex : #125 )',
                    'class' => 'input__design'
                ],
                'required' => false
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message*',
                'attr' => [
                    'placeholder' => 'Entrer le détail de votre demande',
                    'class' => 'input__design'
                ],
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
