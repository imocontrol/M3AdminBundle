<?php
namespace IMOControl\M3\AdminBundle\Services;

use IMOControl\M3\AdminBundle\Admin\Admin;

abstract class Manager
{
    /**
     * Class name of the final application entity class.
     *
     * @var string $entity_class Name of the application entity class.
     */
    protected $entityClass;

    /**
     * Current admin object. Get's injected by setAdminObject at rundime when the
     * admin object was instanced.
     *
     * @var IMOControl\M3\AdminBundle\Admin\CoreAdmin $admin
     */
    protected $admin;

    /**
     * Holds an stack of option values in an array.
     *
     * @var array $options
     */
    protected $options = array();

    /**
     *
     * @param   string  $entity_class           Name of the model class to init if needed.
     * @param   string  $customer_root_path     Customer folder absolute Rootpath.
     */
    public function __construct($entityClass)
    {
        if (!class_exists($entityClass)) {
            throw new \InvalidArgumentException(sprintf("The class %s doesn't exists or is not autoloaded.", $entityClass));
        }
        $this->$entityClass = $entityClass;
    }

    abstract public function create($object);
    abstract public function update($object);
    abstract public function remove($object);

    public function getEntityClass()
    {
        return $this->$entityClass;
    }

    public function setAdminObject($object)
    {
        if (!$object instanceof Admin) {
            throw new \InvalidArgumentException(sprintf("No valid Admin Object. Given: %s Expect instance of %s.", get_class($object), 'IMOControl\M3\AdminBundle\Admin\Admin'));
        }
        $this->admin = $object;
    }

    public function getAdminObject()
    {
        return $this->admin;
    }

    public function addOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    public function getOption($name, $default = null)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }
        return $default;
    }

    public function removeOption($name)
    {
        if ($this->hasOption($name)) {
            unset($options[$name]);
            return true;
        }
        return false;
    }

}
