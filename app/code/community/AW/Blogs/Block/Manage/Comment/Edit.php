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


class AW_Blogs_Block_Manage_Comment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'blogs';
        $this->_controller = 'manage_comment';

        $this->_updateButton('save', 'label', Mage::helper('blogs')->__('Save Comment'));
        $this->_updateButton('delete', 'label', Mage::helper('blogs')->__('Delete Comment'));

        $this->_addButton(
            'saveandcontinue',
            array(
                 'label'   => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                 'onclick' => 'saveAndContinueEdit()',
                 'class'   => 'save',
            ),
            -100
        );

        $this->_formScripts[]
            = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('blogs_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'blogs_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'blogs_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if (Mage::registry('blogs_data') && Mage::registry('blogs_data')->getId()) {
            return Mage::helper('blogs')->__(
                "Edit Comment By '%s'", $this->escapeHtml(Mage::registry('blogs_data')->getUser())
            );
        } else {
            return Mage::helper('blogs')->__('Add Comment');
        }
    }
}