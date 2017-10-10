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


class AW_Blogs_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_TAGCLOUD_SIZE = 'blogs/menu/tagcloud_size';
    const XML_RECENT_SIZE = 'blogs/menu/recent';

    const XML_BLOGS_PERPAGE = 'blogs/blogs/perpage';
    const XML_BLOGS_READMORE = 'blogs/blogs/readmore';
    const XML_BLOGS_PARSE_CMS = 'blogs/blogs/parse_cms';

    const XML_BLOGS_USESHORTCONTENT = 'blogs/blogs/useshortcontent';

    const XML_COMMENTS_PER_PAGE = 'blogs/comments/page_count';
}