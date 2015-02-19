<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProgramYearStewardType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('department', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Department"
            ])
            ->add('programYear', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:ProgramYear"
            ])
            ->add('school', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:School"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\ProgramYearSteward'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'programyearsteward';
    }
}
