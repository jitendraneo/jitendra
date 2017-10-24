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


class AW_Blogs_Model_Observer
{
    public function addBlogsSection($observer)
    {
        $sitemapObject = $observer->getSitemapObject();
        if (!($sitemapObject instanceof Mage_Sitemap_Model_Sitemap)) {
            throw new Exception(Mage::helper('blogs')->__('Error during generation sitemap'));
        }

        $storeId = $sitemapObject->getStoreId();
        $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d');
        $baseUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        /**
         * Generate blogs pages sitemap
         */
        $changefreq = (string)Mage::getStoreConfig('sitemap/blogs/changefreq');
        $priority = (string)Mage::getStoreConfig('sitemap/blogs/priority');
        $collection = Mage::getModel('blogs/blogs')->getCollection()->addStoreFilter($storeId);
        Mage::getSingleton('blogs/status')->addEnabledFilterToCollection($collection);
        $route = Mage::getStoreConfig('blogs/blogs/route',$sitemapObject->getStoreId());
        if ($route == "") {
            $route = "blogs";
        }
        foreach ($collection as $item) {
            $xml = sprintf(
                '<url><loc>%s</loc><lastmod>%s</lastmod><changefreq>%s</changefreq><priority>%.1f</priority></url>',
                htmlspecialchars($baseUrl . $route . '/' . $item->getIdentifier()) . '/', $date, $changefreq, $priority
            );

            $sitemapObject->sitemapFileAddLine($xml);
        }
        unset($collection);
    }

    public function rewritesEnable($observer)
    {
        if (Mage::helper('blogs')->getEnabled()) {
            $node = Mage::getConfig()->getNode('global/blocks/rss/rewrite');
            foreach (Mage::getConfig()->getNode('global/blocks/rss/drewrite')->children() as $dnode) {
                $node->appendChild($dnode);
            }

            $moduleName = Mage::app()->getRequest()->getModuleName();
            $controllerName = Mage::app()->getRequest()->getControllerName();
            $actionName = Mage::app()->getRequest()->getActionName();
            if ($moduleName == 'blogs' && $controllerName == 'index' && $actionName == 'index') {
                $node = Mage::getConfig()->getNode('global/blocks/page/rewrite');
                foreach (Mage::getConfig()->getNode('global/blocks/page/drewrite')->children() as $dnode) {
                    $node->appendChild($dnode);
                }
            }
        }
    }
}