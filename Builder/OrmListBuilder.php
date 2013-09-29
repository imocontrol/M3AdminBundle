<?php
namespace IMOControl\M3\AdminBundle\Builder;

use Sonata\AdminBundle\Admin\FieldDescriptionInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\FieldDescriptionCollection;
use Sonata\AdminBundle\Builder\ListBuilderInterface;
use Sonata\AdminBundle\Guesser\TypeGuesserInterface;

use Sonata\DoctrineORMAdminBundle\Builder\ListBuilder as SonataListBuilder;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class OrmListBuilder extends SonataListBuilder
{
    /**
     * @param \Sonata\AdminBundle\Admin\FieldDescriptionInterface $fieldDescription
     *
     * @return \Sonata\AdminBundle\Admin\FieldDescriptionInterface
     */
    public function buildActionFieldDescription(FieldDescriptionInterface $fieldDescription)
    {
        if (null === $fieldDescription->getTemplate()) {
            $fieldDescription->setTemplate('IMOControlM3AdminBundle:CRUD/list:list__action.html.twig');
        }

        if (null !== $fieldDescription->getOption('actions')) {
            $actions = $fieldDescription->getOption('actions');
            foreach ($actions as $k => $action) {
                if (!isset($action['template'])) {
                    $actions[$k]['template'] = sprintf('IMOControlM3AdminBundle:CRUD/list:list__action_%s.html.twig', $k);
                }
            }

            $fieldDescription->setOption('actions', $actions);
        }

        return parent::buildActionFieldDescription($fieldDescription);
    }

}
