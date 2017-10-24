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


class AW_Blogs_Block_Manage_Blogs_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('blogs_form', array('legend' => Mage::helper('blogs')->__('Post information')));

        $fieldset->addField(
            'title',
            'text',
            array(
                 'label'    => Mage::helper('blogs')->__('Title'),
                 'class'    => 'required-entry',
                 'required' => true,
                 'name'     => 'title',
            )
        );

        $noticeMessage = Mage::helper('blogs')->__('e.g. domain.com/blogs/<b>identifier</b>');

        $validationErrorMessage = addslashes(
            Mage::helper('blogs')->__(
                "Please use only letters (a-z or A-Z), numbers (0-9) or symbols '-' and '_' in this field"
            )
        );

        $fieldset->addField(
            'identifier',
            'text',
            array(
                 'label'              => Mage::helper('blogs')->__('Identifier'),
                 'class'              => 'required-entry aw-blogs-validate-identifier',
                 'required'           => true,
                 'name'               => 'identifier',
                 'after_element_html' => '<span class="hint">' . $noticeMessage . '</span>'
                     . "<script>
                        Validation.add(
                            'aw-blogs-validate-identifier',
                            '" . $validationErrorMessage . "',
                            function(v, elm) {
                                var regex = new RegExp(/^[a-zA-Z0-9_-]+$/);
                                return v.match(regex);
                            }
                        );
                        </script>",
            )
        );

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField(
                'store_id',
                'multiselect',
                array(
                     'name'     => 'stores[]',
                     'label'    => Mage::helper('cms')->__('Store View'),
                     'title'    => Mage::helper('cms')->__('Store View'),
                     'required' => true,
                     'values'   => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                )
            );
        }

        $categories = array();
        $collection = Mage::getModel('blogs/cat')->getCollection()->setOrder('sort_order', 'asc');
        foreach ($collection as $cat) {
            $categories[] = (array(
                'label' => (string)$cat->getTitle(),
                'value' => $cat->getCatId()
            ));
        }

        $fieldset->addField(
            'cat_id',
            'multiselect',
            array(
                 'name'     => 'cats[]',
                 'label'    => Mage::helper('blogs')->__('Category'),
                 'title'    => Mage::helper('blogs')->__('Category'),
                 'required' => true,
                 'style'    => 'height:100px',
                 'values'   => $categories,
            )
        );

        $fieldset->addField(
            'status',
            'select',
            array(
                 'label'              => Mage::helper('blogs')->__('Status'),
                 'name'               => 'status',
                 'values'             => array(
                     array(
                         'value' => 1,
                         'label' => Mage::helper('blogs')->__('Enabled'),
                     ),
                     array(
                         'value' => 2,
                         'label' => Mage::helper('blogs')->__('Disabled'),
                     ),
                     array(
                         'value' => 3,
                         'label' => Mage::helper('blogs')->__('Hidden'),
                     ),
                 ),
                 'after_element_html' => '<span class="hint">'
                     . Mage::helper('blogs')->__(
                         "Hidden pages won't be shown in blogs but still can be accessed directly"
                     )
                     . '</span>',
            )
        );

        $fieldset->addField(
            'comments',
            'select',
            array(
                 'label'              => Mage::helper('blogs')->__('Enable Comments'),
                 'name'               => 'comments',
                 'values'             => array(
                     array(
                         'value' => 0,
                         'label' => Mage::helper('blogs')->__('Enabled'),
                     ),
                     array(
                         'value' => 1,
                         'label' => Mage::helper('blogs')->__('Disabled'),
                     ),
                 ),
                 'after_element_html' => '<span class="hint">'
                     . Mage::helper('blogs')->__(
                         'Disabling will close the post to new comments'
                     )
                     . '</span>',
            )
        );

        $fieldset->addField(
            'tags',
            'text',
            array(
                 'name'               => 'tags',
                 'label'              => Mage::helper('blogs')->__('Tags'),
                 'title'              => Mage::helper('blogs')->__('tags'),
                 'style'              => 'width:700px;',
                 'after_element_html' => Mage::helper('blogs')->__('Use comma as separator'),
            )
        );

        try {
            $config = Mage::getSingleton('cms/wysiwyg_config')->getConfig();
            $config->setData(
                Mage::helper('blogs')->recursiveReplace(
                    '/blogs_admin/',
                    '/' . (string)Mage::app()->getConfig()->getNode('admin/routers/adminhtml/args/frontName') . '/',
                    $config->getData()
                )
            );
        } catch (Exception $ex) {
            $config = null;
        }

        if (Mage::getStoreConfig('blogs/blogs/useshortcontent')) {
            $fieldset->addField(
                'short_content',
                'editor',
                array(
                     'name'   => 'short_content',
                     'label'  => Mage::helper('blogs')->__('Short Content'),
                     'title'  => Mage::helper('blogs')->__('Short Content'),
                     'style'  => 'width:700px; height:100px;',
                     'config' => $config,
                )
            );
        }
        $fieldset->addField(
            'post_content',
            'editor',
            array(
                 'name'   => 'post_content',
                 'label'  => Mage::helper('blogs')->__('Content'),
                 'title'  => Mage::helper('blogs')->__('Content'),
                 'style'  => 'width:700px; height:500px;',
                 'config' => $config
            )
        );

        if (Mage::getSingleton('adminhtml/session')->getBlogsData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getBlogsData());
            Mage::getSingleton('adminhtml/session')->setBlogsData(null);
        } elseif (Mage::registry('blogs_data')) {
            Mage::registry('blogs_data')->setTags(
                Mage::helper('blogs')->convertSlashes(Mage::registry('blogs_data')->getTags())
            );
            $form->setValues(Mage::registry('blogs_data')->getData());
        }
        return parent::_prepareForm();
    }
}