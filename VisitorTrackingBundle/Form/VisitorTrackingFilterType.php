<?php

namespace daveudaimon\VisitorTrackingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class VisitorTrackingFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('context', 'text', array('required' => false));
        $builder->add('referer', 'text', array('required' => false));
        $builder->add('banned_ips', 'textarea', array('required' => false));
        $builder->add('period_start', 'datetime', array('required' => false, 'empty_value' => '', 'years' => range(date('Y'), date('Y')-10)));
        $builder->add('period_end', 'datetime', array('required' => false, 'empty_value' => '', 'years' => range(date('Y'), date('Y')-10)));
    }

    public function getName()
    {
        return 'visitor_tracking_filter';
    }
}
