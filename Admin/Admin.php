<?php
/*
 * This file is part of the iMOControl package.
 *
 * (c) Michael Ofner <michael@imocontrol.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace IMOControl\M3\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as SonataAdmin;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormBuilder;
use Sonata\AdminBundle\Form\FormMapper;

abstract class Admin extends SonataAdmin
{
    /**
     * Name of the used translation file. (Resources/translations/<name>.de.yml)
     *
     * @var string
     */
    protected $translationDomain = 'default';

    /**
     * The number of result to display in the list
     *
     * @var integer
     */
    protected $maxPerPage = 50;

    /**
     * The maximum number of page numbers to display in the list
     *
     * @var integer
     */
    protected $maxPageLinks = 50;

    /**
     * Predefined per page options
     *
     * @var array
     */
    protected $perPageOptions = array(50, 100, 200, 300, 500, 1000, 5000);

    public function __construct($code, $class, $baseControllerName)
    {
        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * Set the current injected translation domain value.
     *
     * @param string $value
     */
    public function setTranslationDomain($value)
    {
        $this->translationDomain = $value;
    }

    /**
     * @return M3UserBundle Current user if logged in
     */
    public function getCurrentUser()
    {
        return $this->getContainer()->get('security.context')->getToken()->getUser();
    }

    /**
     * Check if the current request is in edit or creation modus
     *
     * @return boolean true: Editmodus false: Creationmodus
     */
    public function isEditModus()
    {
        return ($this->getSubject()->getId() > 0);
    }

    /**
<<<<<<< HEAD
=======
     * Check if the current request is an ajax request. (e.g.: popup form or list)
     *
     * @return boolean true If ajax request
     */
    public function isAjaxModus()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }

    /**
>>>>>>> 4bb98d0aba88c9e703cbc87fd1b419b250c60600
     * Checks if the current request is a history request.
     *
     * @return boolean true|false
     */
    public function isHistoryModus()
    {
        return (false === strpos($this->getRequest()->get('_route'), 'history')) ? false : true;
    }

    public function getContainer()
    {
        return $this->getConfigurationPool()->getContainer();
    }

    public function getSecurityContext()
    {
        return $this->getContainer()->get('security.context');
    }

}
