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
    protected $entity_class;

    /**
     * Current admin object. Get's injected by setAdminObject at rundime when the
     * admin object was instanced.
     *
     * @var IMOControl\M3\AdminBundle\Admin\CoreAdmin $admin
     */
    protected $admin;

    /**
     * Holds an stack of options values in an array.
     *
     * @var array $options
     */
    protected $options = array();

    /**
     *
     * @param	string	$entity_class			Name of the model class to init if needed.
     * @param 	string	$customer_root_path		Customer folder absolute Rootpath.
     */
    public function __construct($entity_class)
    {
        if (!class_exists($entity_class)) {
            throw new \InvalidArgumentException(sprintf("The class %s doesn't exists or is not autoloaded.", $entity_class));
        }
        $this->entity_class = $entity_class;
        $this->init();
    }

    abstract public function create($object);
    abstract public function update($object);

    public function getEntityClass()
    {
        return $this->entity_class;
    }

    public function setAdminObject($object)
    {
        if (!$object instanceof Admin) {
            throw new \InvalidArgumentException(sprintf("No valid Admin Object. Given: %s Expect instance of %s.", class_name($object), 'IMOControl\M3\AdminBundle\Admin\Admin'));
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
