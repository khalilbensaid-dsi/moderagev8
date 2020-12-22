<?php

namespace App\Form;

use App\Entity\PaiementConsulting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PaiementConsultingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nomCarte')
            ->add('numCarte')
            ->add('expiration',DateType::class,[
                'widget' => 'single_text',
                 'format' => 'yyyy-MM-dd'
            ])
            ->add('cvv'
            )
            
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PaiementConsulting::class,
        ]);
    }
}
