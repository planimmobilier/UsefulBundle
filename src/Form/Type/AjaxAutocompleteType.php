<?php

namespace Resomedia\UsefulBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Resomedia\UsefulBundle\Form\DataTransformer\EntityToPropertyTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class AjaxAutocompleteType
 * @package Resomedia\UsefulBundle\Form\Type
 */
class AjaxAutocompleteType extends AbstractType
{
    private $parameterBag;
    private $em;

    /**
     * AjaxAutocompleteType constructor.
     * @param ParameterBagInterface $parameterBag
     * @param EntityManager $em
     */
    public function __construct(ParameterBagInterface $parameterBag, EntityManager $em)
    {
        $this->parameterBag = $parameterBag;
        $this->em = $em;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'entity_alias' => null,
            'class' => null,
            'choice_label' => null,
            'compound' => false,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'useful_ajax_autocomplete';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return TextType::class;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $entities = $this->parameterBag->get('useful.autocomplete_entities');

  
        $options['class'] = $entities[$options['entity_alias']]['class'];
        $options['choice_label'] = $entities[$options['entity_alias']]['choice_label'];


        $builder->addViewTransformer(new EntityToPropertyTransformer(
            $this->em,
            $options['class'],
            $options['choice_label']
        ), true);

        $builder->setAttribute('entity_alias', $options['entity_alias']);
        if (array_key_exists('attr', $options) && array_key_exists('class', $options['attr'])) {
            $builder->setAttribute('attr_class', $options['attr']['class']);
        } else {
            $builder->setAttribute('attr_class', '');
        }
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['entity_alias'] = $form->getConfig()->getAttribute('entity_alias');
        $view->vars['attr_class'] = $form->getConfig()->getAttribute('attr_class');
    }
}
