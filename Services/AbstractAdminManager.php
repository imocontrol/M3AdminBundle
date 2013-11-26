<?php
namespace IMOControl\M3\AdminBundle\Services;

use IMOControl\M3\AdminBundle\Admin\Admin;

abstract class AbstractAdminManager extends AbstractEntityManager
{
    /**
     * Current admin object. Get's injected by setAdminObject at rundime when the
     * admin object was instanced.
     *
     * @var IMOControl\M3\AdminBundle\Admin\CoreAdmin $admin
     */
    protected $admin;

    /**
     *
     * @param   string  $entity_class           Name of the model class to init if needed.
     */
    public function __construct($entityClass)
    {
        if (!class_exists($entityClass)) {
            throw new \InvalidArgumentException(sprintf("The class %s doesn't exists or was not autoloaded.", $entityClass));
        }
        $this->entityClass = $entityClass;
    }

    public function setAdminObject($object)
    {
        if (!$object instanceof Admin) {
            throw new \InvalidArgumentException(sprintf("No valid Admin Object. Given: %s Expect instance of %s.", get_class($object), 'IMOControl\M3\AdminBundle\Admin\Admin'));
        }
        $this->admin = $object;
    }
    
    /**
     * @return \Sonata\AdminBundle\Admin\AdminInterface
     */
    public function getAdminObject()
    {
        return $this->admin;
    }

}
