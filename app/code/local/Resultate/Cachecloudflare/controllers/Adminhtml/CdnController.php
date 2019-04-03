<?php

/**
 * User: Lucas Quintela Miranda
 * Email: lucas.quintela@resutlate.com.br
 * Date: 09/05/18
 * Time: 11:42
 */
class Resultate_Cachecloudflare_Adminhtml_CdnController extends Mage_Adminhtml_Controller_Action
{
    public function clean_allAction()
    {
        try {
            $response = Mage::getModel('resultate_cachecloudflare/api')
                ->cleanAllCache(
                    $this->getRequest()->getParam('website')
                );

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($response));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__($e->getMessage()));
        }
        $this->_redirect('*/cache/index');
    }

    public function clean_urlAction()
    {
        try {
            $response = Mage::getModel('resultate_cachecloudflare/api')
                ->cleanUrlCache(
                    $this->getRequest()->getParam('url'),
                    $this->getRequest()->getParam('website')
                );

            Mage::getSingleton('adminhtml/session')->addSuccess($this->__($response));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__($e->getMessage()));
        }
        $this->_redirect('*/cache/index');
    }
}