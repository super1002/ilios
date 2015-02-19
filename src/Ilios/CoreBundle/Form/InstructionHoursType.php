<?php

namespace Ilios\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InstructionHoursType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('generationTimeStamp')
            ->add('hoursAccrued')
            ->add('modified')
            ->add('modificationTimeStamp')
            ->add('user', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:User"
            ])
            ->add('session', 'single_related', [
                'required' => false,
                'entityName' => "IliosCoreBundle:Session"
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ilios\CoreBundle\Entity\InstructionHours'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'instructionhours';
    }
}
