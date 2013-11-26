<?php
namespace IMOControl\M3\AdminBundle\Services;

abstract class AbstractEntityManager
{
    /**
     * Class name of the final application entity class.
     *
     * @var string $entityClass Name of the application entity class.
     */
    protected $entityClass;

    /**
     * Holds a stack of option values in an array.
     *
     * @var array $options
     */
    protected $options = array();

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

    abstract public function create();
    abstract public function update();
    abstract public function remove();

    public function getEntityClass()
    {
        return $this->entityClass;
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
