<?php

namespace App\Form;

use App\Entity\Aplicacion;
use App\Entity\Juego;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JuegoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('tokenJuego')
            ->add('description')
            ->add('estado', null, [
                'label' => 'Activo',
            ])
            ->add('aplicacion', EntityType::class, [
                'class' => Aplicacion::class,
                'choice_label' => 'nombre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Juego::class,
        ]);
    }
}
