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


class AW_Blogs_Block_Manage_Blogs_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blogs_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blogs')->__('Post Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_section',
            array(
                 'label'   => Mage::helper('blogs')->__('Post Information'),
                 'title'   => Mage::helper('blogs')->__('Post Information'),
                 'content' => $this->getLayout()->createBlock('blogs/manage_blogs_edit_tab_form')->toHtml(),
            )
        );

        $this->addTab(
            'options_section',
            array(
                 'label'   => Mage::helper('blogs')->__('Advanced Options'),
                 'title'   => Mage::helper('blogs')->__('Advanced Options'),
                 'content' => $this->getLayout()->createBlock('blogs/manage_blogs_edit_tab_options')->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}