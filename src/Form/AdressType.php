<?php

namespace App\Form;

use App\Entity\Adress;
use App\Entity\Country;
use App\Form\CountryType as FormCountryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adress')
            ->add('complementaryAdress')
            ->add('city')
            ->add('zipCode')
            ->add('firstName')
            ->add('lastName')
            ->add('phone', TelType::class)
            ->add('country', EntityType::class, [
                'class' => Country::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Adress::class,
        ]);
    }
}
