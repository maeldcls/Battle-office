<?php

namespace App\Form;

use App\Entity\Country;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
        // ->add('country', EntityType::class, [
        //             'class' => Country::class,
        //             'required' => true,
        //             'label' => 'Pays',
                   
        // ])

        
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
