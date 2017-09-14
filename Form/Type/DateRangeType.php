<?php

namespace Resomedia\UsefulBundle\Form\Type;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Resomedia\UsefulBundle\Form\DataTransformer\DateRangeToValueTransformer;
use Resomedia\UsefulBundle\Model\DateRange;

/**
 * Description of DateRangeType
 * @author shtumi
 */
class DateRangeType extends AbstractType
{
    private $date_format;
    private $default_interval;
    private $container;

    /**
     * DateRangeType constructor.
     * @param ContainerInterface $container
     * @param $parameters
     */
    public function __construct(ContainerInterface $container, $parameters)
    {
        $this->date_format      = $parameters['date_format'];
        $this->default_interval = $parameters['default_interval'];
        $this->container        = $container;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'default' => null,
            'compound' => false,
        ));
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'field';
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'useful_daterange';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!isset($options['default'])) {
            if ($options['required']){
                $dateRange = new DateRange($this->date_format);
                $dateRange->createToDate(new \DateTime, $this->default_interval);
            } else {
                $dateRange = null;
            }

        } else {
            $dateRange = $options['default'];
        }

        $options['default'] = $dateRange;

        $builder->addViewTransformer(new DateRangeToValueTransformer(
            $this->date_format
        ));

        $builder->setData($options['default']);

        // Datepicker date format
        $searches = array('d', 'm', 'y', 'Y');
        $replaces = array('dd', 'mm', 'yy', 'yyyy');

        $datepicker_format = str_replace($searches, $replaces, $this->date_format);

        $builder->setAttribute('datepicker_date_format', $datepicker_format);
    }

    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['datepicker_date_format'] = $form->getConfig()->getAttribute('datepicker_date_format');
        $view->vars['locale'] = $this->container->get('request')->getLocale();
    }
}
