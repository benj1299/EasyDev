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
            ->add('projectname', TextType::class,     ['attr' => ['placeholder' => 'Projectname', 'class' => 'form-control'], 'label' => false])
            ->add('check1',      CheckboxType::class, ['required' => false, 'label' => 'Contact form'])
            ->add('check2',      CheckboxType::class, ['required' => false, 'label' => 'Newsletter'])
            ->add('check3',      CheckboxType::class, ['required' => false, 'label' => 'Pagination'])
            ->add('check4',      CheckboxType::class, ['required' => false, 'label' => "Admin Area"])
            ->add('check5',      CheckboxType::class, ['required' => false, 'label' => "Members Area"])
            ->add('check6',      CheckboxType::class, ['required' => false, 'label' => "Search Bar"])
            ->add('check7',      CheckboxType::class, ['required' => false, 'label' => 'Affichage de données de la BDD (ex: articles)'])
            ->add('check8',      CheckboxType::class, ['required' => false, 'label' => 'Post de données de la BDD (ex: articles)'])
            ->add('sql',         FileType::class,     ['required' => false, 'label' => 'Add your database', 'attr' => ['id' => 'sql', 'class' => 'files', 'data-preview-file-type' => 'text']])
            ->add('bddname',     TextType::class,     ['required' => false, 'attr' => ['placeholder' => 'Database name'], 'label' => false])
            ->add('bddid',       TextType::class,     ['required' => false, 'attr' => ['placeholder' => 'Database login'], 'label' => false])
            ->add('bddpass',     PasswordType::class, ['required' => false, 'attr' => ['placeholder' => 'Database password'], 'label' => false])
            ->add('files',       FileType::class,     ['required' => false, 'label' => 'Add your files', 'attr' => ['id' => 'files', 'class' => 'files', 'data-preview-file-type' => 'text']])
            ->add('save',        SubmitType::class,   ['label' => 'Send']);
    }

}
