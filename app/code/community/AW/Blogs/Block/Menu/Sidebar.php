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


class AW_Blogs_Block_Menu_Sidebar extends AW_Blogs_Block_Abstract
{
    public function getRecent()
    {
        // widget declaration
        if ($this->getBlogsWidgetRecentCount()) {
            $size = $this->getBlogsWidgetRecentCount();
        } else {
            // standard output
            $size = self::$_helper->getRecentPage();
        }

        if ($size) {
            $collection = clone $this->_prepareCollection();
            $collection->setPageSize($size);

            foreach ($collection as $item) {
                $item->setAddress($this->getBlogsUrl($item->getIdentifier()));
            }
            return $collection;
        }
        return false;
    }

    public function getCategories()
    {
        $collection = Mage::getModel('blogs/cat')
            ->getCollection()
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->setOrder('sort_order', 'asc')
        ;
        foreach ($collection as $item) {
            $item->setAddress($this->getBlogsUrl(array(self::$_catUriParam, $item->getIdentifier())));
        }
        return $collection;
    }

    protected function _beforeToHtml()
    {
        return $this;
    }

    protected function _toHtml()
    {
        if (self::$_helper->getEnabled()) {
            $parent = $this->getParentBlock();
            if (!$parent) {
                return null;
            }

            $showLeft = Mage::getStoreConfig('blogs/menu/left');
            $showRight = Mage::getStoreConfig('blogs/menu/right');

            $isBlogsPage = Mage::app()->getRequest()->getModuleName() == AW_Blogs_Helper_Data::DEFAULT_ROOT;

            $leftAllowed = ($isBlogsPage && ($showLeft == 2)) || ($showLeft == 1);
            $rightAllowed = ($isBlogsPage && ($showRight == 2)) || ($showRight == 1);

            if (!$leftAllowed && ($parent->getNameInLayout() == 'left')) {
                return null;
            }
            if (!$rightAllowed && ($parent->getNameInLayout() == 'right')) {
                return null;
            }

            return parent::_toHtml();
        }
    }
}