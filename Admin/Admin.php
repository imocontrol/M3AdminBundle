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
    protected $maxPerPage = 0;

    /**
     * The maximum number of page numbers to display in the list
     *
     * @var integer
     */
    protected $maxPageLinks = 0;
    
    /**
     * Holds a stack of imoc specific parameters for this currently used admin instance.
     *
     * @var array
     */
    protected $parameters = array();

    /**
     * Predefined per page options
     *
     * @var array
     */
    protected $perPageOptions = array(50, 100, 200, 300, 500, 1000);

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
        if (is_object($this->getSubject())) {
            return ($this->getSubject()->getId() > 0);
        }
        return true;
    }

    /**
     * Check if the current request is an ajax request. (e.g.: popup form or list)
     *
     * @return boolean true If ajax request
     */
    public function isAjaxModus()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }

    /**
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
    
    /**
     * Check if a parameter exists
     * 
     * @param string $name Name of parameter to check
     * @return boolean
     */
    public function hasParam($name)
    {
        return array_key_exists($name, $this->parameters);
    }
    
    /**
     * Add key value pair to parameter array
     * 
     * @param string $name
     * @param mixed $value 
     */
    public function addParam($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Set a array to params. Notice existing values get's overridden...
     * 
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->parameters = $params;
    }
    
    /**
     * Get full parameter array
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->parameters;
    }
    
    /**
     * Get a single parameter or return by default null if param not exists.
     * 
     * @param string $name
     * @param mixed $default Return value if parameter doesn't exists. Default null
     */
    public function getParam($name, $default=null)
    {
        if ($this->hasParam($name)) {
            return $this->options[$name];
        }
        return $default;
    }
    
    /**
     * Remove a parameter if exists
     * 
     * @param string $name
     * @return boolean
     */
    public function removeParam($name)
    {
        if ($this->hasParam($name)) {
            unset($this->parameters[$name]);
            return true;
        }
        return false;
    }
    
    public function getSecurityContext()
    {
        return $this->getContainer()->get('security.context');
    }

    public function getDefaultEditorOptions() {
        return array('theme' => 'modern', 'cleanup' => false, 'relative_urls' => false, 'verify_html' => false,
                                                                            'convert_urls' => false, 
                                                                            'keep_styles' => true,
                                                                            'pagebreak_separator' => "<span style='page-break-after: always;'><!-- User pagebreak --></span>",
                                                                            'plugins' => 'preview, fullscreen, pagebreak, table, responsivefilemanager', 'object_resizing' => true, 
                                                                            'external_filemanager_path' => '/filemanager/',
                                                                            'filemanager_title:"Responsive Filemanager"',
                                                                            'external_plugins' => array('filemanager' => "/bundles/imocontrolm3document/js/tinymce/plugins/responsivefilemanager/plugin.min.js"),
                                                                            'toolbar2' => 'filemanager',
                                                                            'width' => '820px', 'height' => '900px',
                                                                            
                                                                            'theme_advanced_buttons3' => "fullscreen, preview,zoom,separator,fontsizeselect,forecolor,backcolor,pagebreak,tablecontrols,|,sub, sup",
                                                                            );
    }
    
    /**
     * Set the offer manager service.
     * Should always be injected by the service container.
     *
     * @param \IMOControl\M3\CustomerBundle\Services\OfferManager
     * @deprecated
     */
    public function setManager(\IMOControl\M3\AdminBundle\Services\AbstractAdminManager $instance)
    {
        $this->manager = $instance;
        $this->manager->setAdminObject($this);
    }
    
    /**
     * Set the offer manager service.
     * Should always be injected by the service container.
     *
     * @param \IMOControl\M3\AdminBundle\Services\AbstractAdminManager
     * @deprecated
     */
    public function getManager()
    {
        return $this->manager;
    }
    
    public function initDocument()
    {
        if (!method_exists($this->getSubject(), 'getDocument')) {
            return;
        }
        $docClass = $this->getParam('document_class');
        if (!class_exists($docClass)) {
            throw new \RuntimeException(sprintf('Required class %s was not found. Be sure to set document_class parameter in your Admin', $docClass));
        }
        $doc = $this->getSubject()->getDocument();
        if (!is_null($doc) && $doc instanceof $docClass) {
            return $this->getSubject()->getDocument();
        }
        
        // Finally init and return a new Document instance
        $doc = new $docClass();
        $doc->setFileType('.pdf');  // extension of the file
        $doc->setContentTemplate($this->getParam('pdf_template')); // Content skeleton
        $doc->setLayoutTemplate('Default'); // Html skeleton with core css
        $doc->setCreatedAt(new \DateTime());
        $doc->setCreatedFrom($this->getCurrentUser());
        $doc->setEnabled(false);
        
        return $doc;
    }
    
    public function loadDocument($object)
    {
        $doc = $this->initDocument();
        $doc->setCustomer($object->getCustomer());
        
        $doc->setCreatedAt(new \DateTime());
        $doc->setCreatedFrom($this->getAdminObject()->getCurrentUser());
        $doc->setEnabled(true);
        //$doc->setLocked(true);
        $fileName = Helper::generateInternalName($offer->getId(), $this->getOption('folder_format'), $offer, $this->getOption('folder_id_length'));
        $doc->setName($fileName);
        $offer->setDocument($doc);
        $doc->setContent($this->renderDocument());
        return $doc;
    }
    
}
