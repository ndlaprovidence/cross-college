<?php

namespace App\Form;

use DateTime;
use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname')
            ->add('firstname')
            ->add('gender')
            ->add('mas')
            ->add('objective',TimeType::class, 
                [
                    'input_format' => 'H:i:s',
                    'html5' => true,
                    'widget' => 'choice',
                    'with_minutes' => true,
                    'with_seconds' => true,
                ]
            )
            ->add('grade')
            ->add('note')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
