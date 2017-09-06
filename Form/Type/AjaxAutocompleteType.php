<?php

namespace Shtumi\UsefulBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Shtumi\UsefulBundle\Form\DataTransformer\EntityToPropertyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AjaxAutocompleteType extends AbstractType
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'entity_alias' => null,
            'class' => null,
            'choice_label' => null,
            'compound' => false,
        ));
    }

    public function getBlockPrefix()
    {
        return 'shtumi_ajax_autocomplete';
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entities = $this->container->getParameter('shtumi.autocomplete_entities');

  
        $options['class'] = $entities[$options['entity_alias']]['class'];
        $options['choice_label'] = $entities[$options['entity_alias']]['choice_label'];


        $builder->addViewTransformer(new EntityToPropertyTransformer(
            $this->container->get('doctrine')->getManager(),
            $options['class'],
            $options['choice_label']
        ), true);

        $builder->setAttribute('entity_alias', $options['entity_alias']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['entity_alias'] = $form->getConfig()->getAttribute('entity_alias');
    }

}
