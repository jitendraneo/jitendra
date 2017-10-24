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


class AW_Blogs_Helper_Comment extends Mage_Core_Helper_Abstract
{
    public function renderPage(Mage_Core_Controller_Front_Action $action, $identifier = null, $data = array())
    {
        $page = Mage::getSingleton('blogs/blogs');
        if (!is_null($identifier) && $identifier !== $page->getId()) {
            $page->setStoreId(Mage::app()->getStore()->getId());
            if (!$page->load($identifier)) {
                return false;
            }
        }

        if (!$page->getId()) {
            return false;
        }
        if ($page->getStatus() == 2) {
            return false;
        }
        $pageTitle = Mage::getSingleton('blogs/blogs')->load($identifier)->getTitle();
        $blogsTitle = Mage::getStoreConfig('blogs/blogs/title') . " - ";

        $action->loadLayout();
        if ($storage = Mage::getSingleton('customer/session')) {
            $action->getLayout()->getMessagesBlock()->addMessages($storage->getMessages(true));
        }
        $action->getLayout()->getBlock('head')->setTitle($blogsTitle . $pageTitle);
        $action->getLayout()->getBlock('root')->setTemplate(Mage::getStoreConfig('blogs/blogs/layout'));
        $action->getLayout()->getBlock('post')->setCommentDetails($data['user'], $data['email'], $data['comment']);
        $action->renderLayout();
        return true;
    }
}