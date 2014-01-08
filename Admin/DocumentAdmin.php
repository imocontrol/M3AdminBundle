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

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Form\FormBuilder;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

use IMOControl\M3\DocumentBundle\Model\Interfaces\CustomerDocumentInterface;
use IMOControl\M3\DocumentBundle\Model\Interfaces\SupplierDocumentInterface;
use IMOControl\M3\CustomerBundle\Model\Interfaces\OfferInterface;
use IMOControl\M3\CustomerBundle\Model\Interfaces\CustomerInterface;
//use IMOControl\M3\DocumentBundle\Model\Interfaces\StaffDocumentInterface;

abstract class DocumentAdmin extends Admin
{
    public function initDocument()
    {
        if (!method_exists($this->getSubject(), 'getDocument')) {
            return;
        }
        $docClass = $this->getParam('document_class');
        if (!class_exists($docClass)) {
            throw new \RuntimeException(sprintf('Required class %s was not found. Be sure to set document_class parameter in your Admin service definition', $docClass));
        }
        $doc = $this->getSubject()->getDocument();
        if (!is_null($doc) && $doc instanceof $docClass) {
            return $this->getSubject()->getDocument();
        }
        
        // Finally init and return a new Document instance
        $doc = new $docClass();
        $doc->setFileType('.pdf');  // extension of the file
        $doc->setContentTemplate($this->getParam('render_content_template')); // Content skeleton
        $doc->setLayoutTemplate($this->getParam('render_layout_template')); // Html skeleton with core css
        $doc->setCreatedAt(new \DateTime('now'));
        $doc->setCreatedFrom($this->getCurrentUser());
        $doc->setEnabled(false);
        
        return $doc;
    }

    /*public function loadAndAssignDocument($object=null)
    {
        if (is_null($object)) {
            $object = $this->getSubject();
        }
        $doc = $this->initDocument();
        
        if ($doc instanceof CustomerDocumentInterface) {
            $doc->setCustomer($object->getCustomer());
        } elseif ($doc instanceof SupplierDocumentInterface) {
            $doc->setSupplier($object->getSupplier());
        }
        
        $fileName = Helper::generateInternalName($object->getId(), $this->getParam('folder_format'), $object, $this->getParam('folder_id_length'));
        $doc->setName($fileName);
        $doc->setContent($this->renderDocumentContent());
        $doc->setEnabled(true);
        $doc->setContentTemplate($this->getParam('render_content_template'));
        $object->setDocument($doc);
        
        return $doc;
    }
    */
    
    /**
     * Render the given offer twig template and convert it into raw html code which
     * is finally used for the pdf generation.
     * 
     * @param array $parameters Get assigned to the rendered template
     * @return string Html code of rendered template
     */
    public function renderDocumentContent($parameters=array())
    {
        $defaults = array();
        $defaults['document'] = $this->getSubject()->getDocument();
        
        $parameters = array_merge($defaults, $parameters);

        $template = $parameters['document']->getContentTemplate();
        $content = $this->getContainer()->get('twig')->render($template, $parameters);
        
        return $content;
    }

    public function renderDocumentWithLayout($parameters=array())
    {
        $defaults = array();
        $defaults['document'] = $this->getSubject()->getDocument();
        $defaults['content'] = $this->getSubject()->getDocument()->getContent();
        $defaults['layout_template'] = $this->getSubject()->getDocument()->getLayoutTemplate();
        //$defaults['layout_template'] = 'IMOControlM3DocumentBundle::Skeletons/pdf_layout.html.twig';
        
        $parameters = array_merge($defaults, $parameters);
        
        $layoutTemplate = 'IMOControlM3CustomerBundle::Skeletons/Layouts/document_layout.html.twig';
        $content = $this->getContainer()->get('twig')->render($layoutTemplate, $parameters);
        //$content = $this->getContainer()->get('twig')->render($parameters['document']->getContentTemplate(), $parameters);
        
        return $content;
    }
    
    /**
     * Init and validate the given filename and path by extracting it from the given
     * customer object.
     *
     * @param string $content    Html content to render as pdf file
     * @param string $path       Relative path in selected filesystem
     * @param string $filesystem Filesystem to load. (e.g. customer, supplier, project)
     *
     * @return boolean true|false if file can't create
     * @throws \InvalidArgumentException
     */
    public function writePdfFile($content, $path, $filesystem='customer')
    {
        $object = $this->getSubject();
        $pdf = $this->preparePdf($content);
        $fs = $this->getContainer()->get('imocontrol.document.filesystem')->get($filesystem.'_folder');
        
        if ($fs->write($path, $pdf->output( array("compress" => 1)), true)) {
            return true;
        } else {
            return false;
        }
        
        /*if (!$object->getCustomer() instanceof CustomerInterface) {
            throw new \InvalidArgumentException(sprintf("No valid customer object given! Expected: %s", 'IMOControl\M3\CustomerBundle\Model\Interfaces\CustomerInterface'));
        }
        
        $document = $object->getDocument();
        if (!$document instanceof CustomerDocumentInterface) {
            throw new \InvalidArgumentException(sprintf("No valid offer object given! Expected: %s Given: %", 'IMOControl\M3\DocumentBundle\Model\Interfaces\CustomerDocumentInterface', get_class($offer->getDocument())));
        }
        
        $fs = $this->getContainer()->get('imocontrol.document.filesystem')->get('customer_folder');
        $fsAdatper = $this->getContainer()->get('imocontrol.document.filesystem')->get('customer_folder')->getAdapter();
        
        $finalName = $document->getName() . $document->getFileType();
        $filePath = sprintf("%s/%s/%s", $object->getCustomer()->getInternalName(), $this->getParam('folder_name'), $finalName);
        if ($fsAdatper->isDirectory($object->getCustomer()->getInternalName())) {
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
        }*/
    }

    /**
     * Prepares and render a given html content and return a dompdf instace.
     * 
     * @param string Html content of the pdf file
     * @return DOMPDF object
     */
    protected function preparePdf($content) 
    {
        $dompdf = new \DOMPDF();
        $dompdf->load_html($content);
        $dompdf->set_paper('A4', 'portrait');
        $dompdf->render();
        
        foreach ($info as $key => $value) {
            $dompdf->add_info($key, $value);
        }
        return $dompdf;
    }

    protected function assignedToProject($object)
    {
        if (method_exists($object, 'getProject')) {
            $project = $object->getProject();
        }
    }
}
