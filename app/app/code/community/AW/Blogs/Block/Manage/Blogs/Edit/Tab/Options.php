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


class AW_Blogs_Block_Manage_Blogs_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('blogs_form', array('legend' => Mage::helper('blogs')->__('Meta Data')));

        $fieldset->addField(
            'meta_keywords',
            'editor',
            array(
                 'name'  => 'meta_keywords',
                 'label' => Mage::helper('blogs')->__('Keywords'),
                 'title' => Mage::helper('blogs')->__('Meta Keywords'),
                 'style' => 'width: 520px;',
            )
        );

        $fieldset->addField(
            'meta_description',
            'editor',
            array(
                 'name'  => 'meta_description',
                 'label' => Mage::helper('blogs')->__('Description'),
                 'title' => Mage::helper('blogs')->__('Meta Description'),
                 'style' => 'width: 520px;',
            )
        );

        $fieldset = $form->addFieldset(
            'blogs_options', array('legend' => Mage::helper('blogs')->__('Advanced Post Options'))
        );

        $fieldset->addField(
            'user',
            'text',
            array(
                 'label'              => Mage::helper('blogs')->__('Poster'),
                 'name'               => 'user',
                 'style'              => 'width: 520px;',
                 'after_element_html' => '<span class="hint">'
                     . Mage::helper('blogs')->__('Leave blank to use current user name')
                     . '</span>',
            )
        );

        $outputFormat = Mage::app()->getLocale()->getDateTimeFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);

        $fieldset->addField(
            'created_time',
            'date',
            array(
                 'name'   => 'created_time',
                 'label'  => $this->__('Created on'),
                 'title'  => $this->__('Created on'),
                 'image'  => $this->getSkinUrl('images/grid-cal.gif'),
                 'format' => $outputFormat,
                 'time'   => true,
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getBlogsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogsData());
            Mage::getSingleton('adminhtml/session')->setBlogsData(null);
        } elseif ($data = Mage::registry('blogs_data')) {
            $form->setValues(Mage::registry('blogs_data')->getData());
            if ($data->getData('created_time')) {
                $form->getElement('created_time')->setValue(
                    Mage::app()->getLocale()->date(
                        $data->getData('created_time'), Varien_Date::DATETIME_INTERNAL_FORMAT
                    )
                );
            }
        }

        return parent::_prepareForm();
    }
}