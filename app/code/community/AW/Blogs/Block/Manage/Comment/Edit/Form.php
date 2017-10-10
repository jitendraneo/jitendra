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


class AW_Blogs_Block_Manage_Comment_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                 'id'     => 'edit_form',
                 'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                 'method' => 'post',
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        $fieldset = $form->addFieldset(
            'comment_form', array('legend' => Mage::helper('blogs')->__('Comment Information'))
        );

        $fieldset->addField(
            'user',
            'text',
            array(
                 'label' => Mage::helper('blogs')->__('User'),
                 'name'  => 'user',
            )
        );

        $fieldset->addField(
            'email',
            'text',
            array(
                 'label' => Mage::helper('blogs')->__('Email Address'),
                 'name'  => 'email',
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                 'label'  => Mage::helper('blogs')->__('Status'),
                 'name'   => 'status',
                 'values' => array(
                     array(
                         'value' => 1,
                         'label' => Mage::helper('blogs')->__('Unapproved'),
                     ),
                     array(
                         'value' => 2,
                         'label' => Mage::helper('blogs')->__('Approved'),
                     ),
                 ),
            )
        );

        $fieldset->addField(
            'comment',
            'editor',
            array(
                 'name'     => 'comment',
                 'label'    => Mage::helper('blogs')->__('Comment'),
                 'title'    => Mage::helper('blogs')->__('Comment'),
                 'style'    => 'width:700px; height:500px;',
                 'wysiwyg'  => false,
                 'required' => false,
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getBlogsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogsData());
            Mage::getSingleton('adminhtml/session')->setBlogsData(null);
        } elseif (Mage::registry('blogs_data')) {
            $form->setValues(Mage::registry('blogs_data')->getData());
        }
        return parent::_prepareForm();
    }
}