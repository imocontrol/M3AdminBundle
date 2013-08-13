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
	
	/**
	 * Security Context
	 * 
	 * @var SecurityContextInterface
	 */
    protected $securityContext;
    
    public function __construct($code, $class, $baseControllerName)
    {
       parent::__construct($code, $class, $baseControllerName);
    }
    
    /**
	 * Set current security context
	 * 
	 * @param SecurityContextInterface $context 
	 */
	public function setSecurityContext(SecurityContextInterface $context) {
		$this->securityContext = $context;
	}
	
	public function setTranslationDomain($value)
	{
		$this->translationDomain = $value;
	}
	
	/**
	 * @return M3UserBundle Current user if logged in
	 */
	public function getCurrentUser() {
		return $this->securityContext->getToken()->getUser();
	}
	
}