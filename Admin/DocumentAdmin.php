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

abstract class DocumentAdmin extends Admin
{
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
    
    /**
     * Render the given offer twig template and convert it into raw html code which
     * is finally used for the pdf generation.
     * 
     * @param array $parameters Get assigned to the rendered template
     * @return string Html code of rendered template
     */
    public function renderDocument($parameters=array())
    {
        $defaults = array();
        $defaults['document'] = $this->getSubject()->getDocument();
        
        $parameters = array_merge($defaults, $parameters);

        $templateName = $this->getParam('pdf_template');
        $finalTemplate = $this->getOfferTemplate($templateName);
        $content = $this->getContainer()->get('twig')->render($finalTemplate, $parameters);
        //$this->getOffer()->getDocument()->setContent($content);
        
        return $content;
    }

    public function renderDocumentLayout($parameters=array())
    {
        $defaults = array();
        $defaults['document'] = $this->getSubject()->getDocument();
        $defaults['content'] = $this->getSubject()->getDocument()->getContent();
        $defaults['layout_template'] = 'IMOControlM3DocumentBundle::Skeletons/pdf_layout.html.twig';
        
        $parameters = array_merge($defaults, $parameters);
        
        $layoutTemplate = 'IMOControlM3CustomerBundle::Skeletons/Layouts/document_layout.html.twig';
        $layout = $this->getContainer()->get('twig')->render($layoutTemplate, $parameters);
        $this->loadDocument()->setFinalContent($layout);
        return $layout;
    }
    
    /**
     * Init and validate the given filename and path by extracting it from the given
     * customer object.
     *
     * @param OfferInterface
     *
     * @return boolean true|false if file can't create
     * @throws \InvalidArgumentException
     */
    public function writeFile(OfferInterface $offer)
    {
        if (!$offer->getCustomer() instanceof CustomerInterface) {
            throw new \InvalidArgumentException(sprintf("No valid customer object given! Expected: %s", 'IMOControl\M3\CustomerBundle\Model\Interfaces\CustomerInterface'));
        }
        
        $document = $offer->getDocument();
        if (!$document instanceof CustomerDocumentInterface) {
            throw new \InvalidArgumentException(sprintf("No valid offer object given! Expected: %s Given: %", 'IMOControl\M3\DocumentBundle\Model\Interfaces\CustomerDocumentInterface', get_class($offer->getDocument())));
        }
        
        $fs = $this->getContainer()->get('imocontrol.document.filesystem')->get('customer_folder');
        $fsAdatper = $this->getContainer()->get('imocontrol.document.filesystem')->get('customer_folder')->getAdapter();
        
        $finalName = $document->getName() . $document->getFileType();
        $filePath = sprintf("%s/%s/%s", $offer->getCustomer()->getInternalName(), $this->getOption('folder_name'), $finalName);
        if ($fsAdatper->isDirectory($offer->getCustomer()->getInternalName())) {
            $dompdf = new \DOMPDF();
            $dompdf->load_html($offer->getDocument()->getFinalContent());
            $dompdf->set_paper('A4', 'portrait');
            $dompdf->render();
            
            $dompdf->add_info('Creator', 'iMOControl V1.0');
            $dompdf->add_info('Author', 'IngH2O - Michael Ofner');
            
            if ($fs->write($filePath, $dompdf->output( array("compress" => 1)), true)) {
                $document->setPath($filePath);
                return true;
            } else {
                return false;
            }
        } else {
            throw new \RuntimeException(sprintf('The directory %s doesn\'t exists. Create it first to continue.', $offer->getCustomer()->getInternalName()));
        }
    }
}
