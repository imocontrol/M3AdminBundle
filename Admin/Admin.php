<?php

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
    protected $translationDomain = 'application';
	
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
    
    /**
	 * Set current security context
	 * 
	 * @param SecurityContextInterface $context 
	 */
	public function setSecurityContext(SecurityContextInterface $context) {
		$this->securityContext = $context;
	}
	
	/**
	 * @return M3UserBundle Current user if logged in
	 */
	public function getCurrentUser() {
		return $this->securityContext->getToken()->getUser();
	}
	
}