<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Blogs
 * @version    tip
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Blogs_Adminhtml_Awblogs_Manage_CatController extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/blogs/cat');
    }

    protected function _initAction()
    {
        $this
            ->loadLayout()
            ->_setActiveMenu('blogs/cat')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager')
            );
        $this->displayTitle('Categories');

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('blogs/cat');

                $model
                    ->setId($this->getRequest()->getParam('id'))
                    ->delete()
                ;

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Category was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $blogsIds = $this->getRequest()->getParam('blogs');
        if (!is_array($blogsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('adminhtml')->__('Please select categories')
            );
        } else {
            try {
                foreach ($blogsIds as $blogsId) {
                    $blogs = Mage::getModel('blogs/cat')->load($blogsId);
                    $blogs->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d category(s) were successfully deleted', count($blogsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blogs/cat')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('blogs_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('blogs/cat');
            $this->displayTitle('Edit category');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Blogs Manager'), Mage::helper('adminhtml')->__('BlogsManager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('blogs/manage_cat_edit'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blogs')->__('Category does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('blogs/cat')->load($id);

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('blogs_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('blogs/cat');
        $this->displayTitle('Add new category');

        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Blogs Manager'), Mage::helper('adminhtml')->__('BlogsManager')
        );
        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Category Manager'), Mage::helper('adminhtml')->__('Category Manager')
        );

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('blogs/manage_cat_edit'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if (isset($data['stores'])) {
                if ($data['stores'][0] == 0) {
                    unset($data['stores']);
                    $data['stores'] = array();
                    $stores = Mage::getSingleton('adminhtml/system_store')->getStoreCollection();
                    foreach ($stores as $store) {
                        $data['stores'][] = $store->getId();
                    }
                }
            }

            $model = Mage::getModel('blogs/cat');
            $model
                ->setData($data)
                ->setId($this->getRequest()->getParam('id'))
            ;

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blogs')->__('Category was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('blogs')->__('Unable to find category to save'));
        $this->_redirect('*/*/');
    }

    protected function displayTitle($data = null, $root = 'Blogs')
    {
        if (!Mage::helper('blogs')->magentoLess14()) {
            if ($data) {
                if (!is_array($data)) {
                    $data = array($data);
                }
                foreach ($data as $title) {
                    $this->_title($this->__($title));
                }
                $this->_title($this->__($root));
            } else {
                $this->_title($this->__('Blogs'))->_title($root);
            }
        }
        return $this;
    }
}