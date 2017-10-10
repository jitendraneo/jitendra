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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
try {
    $installer->run("
        CREATE TABLE {$this->getTable('blogs/blogs')} LIKE {$this->getTable('blogs/lblogs')};
        INSERT {$this->getTable('blogs/blogs')} SELECT * FROM {$this->getTable('blogs/lblogs')};

        CREATE TABLE {$this->getTable('blogs/comment')} LIKE {$this->getTable('blogs/lcomment')};
        INSERT {$this->getTable('blogs/comment')} SELECT * FROM {$this->getTable('blogs/lcomment')};

        CREATE TABLE {$this->getTable('blogs/cat')} LIKE {$this->getTable('blogs/lcat')};
        INSERT {$this->getTable('blogs/cat')} SELECT * FROM {$this->getTable('blogs/lcat')};

        CREATE TABLE {$this->getTable('blogs/post_cat')} LIKE {$this->getTable('blogs/lpost_cat')};
        INSERT {$this->getTable('blogs/post_cat')} SELECT * FROM {$this->getTable('blogs/lpost_cat')};

        CREATE TABLE {$this->getTable('blogs/store')} LIKE {$this->getTable('blogs/lstore')};
        INSERT {$this->getTable('blogs/store')} SELECT * FROM {$this->getTable('blogs/lstore')};

        CREATE TABLE {$this->getTable('blogs/cat_store')} LIKE {$this->getTable('blogs/lcat_store')};
        INSERT {$this->getTable('blogs/cat_store')} SELECT * FROM {$this->getTable('blogs/lcat_store')};

        ALTER TABLE {$this->getTable('blogs/blogs')} ADD `tags` TEXT NOT NULL;
        ALTER TABLE {$this->getTable('blogs/blogs')} ADD `short_content` TEXT NOT NULL;
    ");
} catch (Exception $e) {
    Mage::logException($e);
}

try {
    $installer->run("
        CREATE TABLE IF NOT EXISTS {$this->getTable('blogs/tag')} (
            `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `tag` VARCHAR(255) NOT NULL ,
            `tag_count` INT(11) NOT NULL DEFAULT 0,
            `store_id` TINYINT(4) NOT NULL ,
            INDEX ( `tag`, `count`, `store_id` )
        ) ENGINE = InnoDB DEFAULT CHARSET = utf8;
    ");
} catch (Exception $e) {
    Mage::logException($e);
}
$installer->endSetup();