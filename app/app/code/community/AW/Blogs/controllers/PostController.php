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


require_once 'recaptcha/recaptchalib-aw.php';

class AW_Blogs_PostController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::helper('blogs')->getEnabled()) {
            $this->_redirectUrl(Mage::helper('core/url')->getHomeUrl());
        }
    }

    protected function _validateData($data)
    {
        $errors = array();
        $helper = Mage::helper('blogs');

        if (!Zend_Validate::is($data->getUser(), 'NotEmpty')) {
            $errors[] = $helper->__('Name can\'t be empty');
        }

        if (!Zend_Validate::is($data->getComment(), 'NotEmpty')) {
            $errors[] = $helper->__('Comment can\'t be empty');
        }

        if (!Zend_Validate::is($data->getPostId(), 'NotEmpty')) {
            $errors[] = $helper->__('post_id can\'t be empty');
        }

        $validator = new Zend_Validate_EmailAddress();
        if (!$validator->isValid($data->getEmail())) {
            $errors[] = $helper->__('"%s" is not a valid email address.', $data->getEmail());
        }

        return $errors;
    }

    public function viewAction()
    {
        $identifier = $this->getRequest()->getParam('identifier', $this->getRequest()->getParam('id', false));

        $helper = Mage::helper('blogs');
        $session = Mage::getSingleton('customer/session');

        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('blogs/comment');
            $data['user'] = strip_tags($data['user']);
            $model->setData($data);

            if (!Mage::getStoreConfig('blogs/comments/enabled')) {
                $session->addError($helper->__('Comments are not enabled.'));
                if (!Mage::helper('blogs/post')->renderPage($this, $identifier)) {
                    $this->_forward('NoRoute');
                }
                return;
            }

            if (!$session->isLoggedIn() && Mage::getStoreConfig('blogs/comments/login')) {
                $session->addError($helper->__('You must be logged in to comment.'));
                if (!Mage::helper('blogs/post')->renderPage($this, $identifier)) {
                    $this->_forward('NoRoute');
                }
                return;
            } else {
                if ($session->isLoggedIn() && Mage::getStoreConfig('blogs/comments/login')) {
                    $model->setUser($helper->getUserName());
                    $model->setEmail($helper->getUserEmail());
                }
            }

            try {
                if (Mage::getStoreConfig('blogs/recaptcha/enabled') && !$session->isLoggedIn()) {
                    $privatekey = Mage::getStoreConfig('blogs/recaptcha/privatekey');

                    $resp = recaptcha_check_answer(
                        $privatekey, $data["g-recaptcha-response"], $_SERVER["REMOTE_ADDR"]
                    );

                    if (!$resp->is_valid) {
                        if ($resp->error == "incorrect-captcha-sol") {
                            $session->addError($helper->__('Your reCAPTCHA solution was incorrect, please try again'));
                        } elseif ($resp->error == "must-get-api") {
                            $session->addError($helper->__('To use reCAPTCHA you must get an API key from ' .
                                '<a href="https://www.google.com/recaptcha">https://www.google.com/recaptcha</a>'));
                        } elseif ($resp->error == "must-pass-remote-ip") {
                            $session->addError($helper->__('For security reasons, you must pass the remote ' .
                                'ip to reCAPTCHA'));
                        } elseif ($resp->error == "curl-problem") {
                            $session->addError($helper->__('An error occurred. Please check cURL library'));
                        } else {
                            $session->addError($helper->__('An error occurred. Please try again'));
                        }
                        // Redirect back with error message
                        $session->setBlogsPostModel($model);
                        $this->_redirectReferer();
                        return;
                    }
                }

                $errors = $this->_validateData($model);
                if (!empty($errors)) {
                    foreach ($errors as $error) {
                        $session->addError($error);
                    }
                    $this->_redirectReferer();
                    return;
                }

                if ($session->getData('blogs_post_model')) {
                    $session->unsetData('blogs_post_model');
                }
                $model->setCreatedTime(now());
                $model->setComment(htmlspecialchars($model->getComment(), ENT_QUOTES));
                if (Mage::getStoreConfig('blogs/comments/approval')) {
                    $model->setStatus(2);
                    $session->addSuccess($helper->__('Your comment has been submitted.'));
                } else {
                    if ($session->isLoggedIn() && Mage::getStoreConfig('blogs/comments/loginauto')) {
                        $model->setStatus(2);
                        $session->addSuccess($helper->__('Your comment has been submitted.'));
                    } else {
                        $model->setStatus(1);
                        $session->addSuccess($helper->__('Your comment has been submitted and is awaiting approval.'));
                    }
                }
                $model->save();

                $commentId = $model->getCommentId();
            } catch (Exception $e) {
                if (!Mage::helper('blogs/post')->renderPage($this, $identifier)) {
                    $this->_forward('NoRoute');
                }
            }

            if (Mage::getStoreConfig('blogs/comments/recipient_email') != null && $model->getStatus() == 1
                && isset($commentId)
            ) {
                $translate = Mage::getSingleton('core/translate');
                /* @var $translate Mage_Core_Model_Translate */
                $translate->setTranslateInline(false);
                try {
                    $data["url"] = Mage::helper('adminhtml')->getUrl('adminhtml/awblogs_manage_comment/edit/id/' . $commentId);
                    $postObject = new Varien_Object();
                    $postObject->setData($data);
                    $mailTemplate = Mage::getModel('core/email_template');
                    /* @var $mailTemplate Mage_Core_Model_Email_Template */
                    $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                        ->sendTransactional(
                            Mage::getStoreConfig('blogs/comments/email_template'),
                            Mage::getStoreConfig('blogs/comments/sender_email_identity'),
                            Mage::getStoreConfig('blogs/comments/recipient_email'), null, array('data' => $postObject)
                        );
                    $translate->setTranslateInline(true);
                } catch (Exception $e) {
                    $translate->setTranslateInline(true);
                }
            }
            $this->_redirectReferer();
            return;
            if (!Mage::helper('blogs/post')->renderPage($this, $identifier)) {
                $this->_forward('NoRoute');
            }
        } else {
            /* GET request */
            if (!Mage::helper('blogs/post')->renderPage($this, $identifier)) {
                $session->addNotice($helper->__('The requested page could not be found'));
                $this->_redirect($helper->getRoute());
                return false;
            }
        }
    }

    public function noRouteAction($coreRoute = null)
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHeader('Status', '404 File not found');

        $pageId = Mage::getStoreConfig('web/default/cms_no_route');
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('defaultNoRoute');
        }
    }
}