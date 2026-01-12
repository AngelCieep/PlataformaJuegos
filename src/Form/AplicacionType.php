<?php

namespace App\Form;

use App\Entity\Aplicacion;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AplicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('apiKey')
            ->add('estado', null, [
                'label' => 'Activo',
            ])
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => function(User $user) {
                    return $user->getNombre() . ' (' . $user->getEmail() . ')';
                },
                'label' => 'Propietario',
                'placeholder' => 'Seleccionar propietario',
                'required' => false,
                'query_builder' => function ($repository) {
                    return $repository->createQueryBuilder('u')
                        ->where('u.roles LIKE :role_owner OR u.roles LIKE :role_admin')
                        ->setParameter('role_owner', '%ROLE_OWNER%')
                        ->setParameter('role_admin', '%ROLE_ADMIN%')
                        ->orderBy('u.nombre', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Aplicacion::class,
        ]);
    }
}
