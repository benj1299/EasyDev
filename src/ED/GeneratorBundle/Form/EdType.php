<?php

namespace ED\GeneratorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class EdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projectname', TextType::class,     ['attr' => ['placeholder' => 'Nom du projet', 'class' => 'form-control'], 'label' => false])
            ->add('check1',      CheckboxType::class, ['required' => false, 'label' => 'Formulaire de contact'])
            ->add('check2',      CheckboxType::class, ['required' => false, 'label' => 'Newsletter'])
            ->add('check3',      CheckboxType::class, ['required' => false, 'label' => 'Pagination'])
            ->add('check4',      CheckboxType::class, ['required' => false, 'label' => 'Affichage de données de la BDD (ex: articles)'])
            ->add('check5',      CheckboxType::class, ['required' => false, 'label' => 'Post de données de la BDD (ex: articles)'])
            ->add('check6',      CheckboxType::class, ['required' => false, 'label' => "Espace d'adminitration du site"])
            ->add('check7',      CheckboxType::class, ['required' => false, 'label' => "Espace membre"])
            ->add('check8',      CheckboxType::class, ['required' => false, 'label' => "Barre de recherche"])
            ->add('packagist',   SearchType::class,   ['required' => false, 'attr' => ['placeholder' => 'Rechercher sur packagist'], 'label' => false])
            ->add('bddname',     TextType::class,     ['required' => false, 'attr' => ['placeholder' => 'Nom de la base de données'], 'label' => false])
            ->add('bddid',       TextType::class,     ['required' => false, 'attr' => ['placeholder' => 'Identifiant de la base de données'], 'label' => false])
            ->add('bddpass',     PasswordType::class, ['required' => false, 'attr' => ['placeholder' => 'Mot de passe de la base de données'], 'label' => false])
            ->add('htmlfile',    FileType::class,     ['label' => 'Fichiers HTML'])
            ->add('cssfile',     FileType::class,     ['required' => false, 'label' => 'Fichiers CSS'])
            ->add('jsfile',      FileType::class,     ['required' => false, 'label' => 'Fichiers JS'])
            ->add('save',        SubmitType::class,   ['label' => 'Envoyer']);
    }

}
