<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_HidePrice
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

namespace Lof\HidePrice\Controller\Widget;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\App\Area;

class Index extends \Magento\Framework\App\Action\Action
{

    const EMAIL_TEMPLATE = 'lofhideprice/emailsend/emailtemplate';
    const EMAIL_SENDER = 'lofhideprice/emailsend/emailsenderto';
    const XML_PATH_EMAIL_RECIPIENT = 'lofhideprice/emailsend/emailto';
    const REQUEST_URL = 'https://www.google.com/recaptcha/api/siteverify';
    const REQUEST_RESPONSE = 'g-recaptcha-response';

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;
    
    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @var \Lof\HidePrice\Helper\Data
     */
    protected $_helper;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    protected $image;

    protected $_productloader; 
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\HidePrice\Helper\Image  $image,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        \Lof\HidePrice\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_productloader = $_productloader;
        $this->_transportBuilder  = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_scopeConfig       = $scopeConfig;
        $this->_helper            = $helper;
        $this->_storeManager      = $storeManager;
        $this->image              = $image;
    }

    public function execute() {

        $remoteAddr = filter_input(
                INPUT_SERVER,
                'REMOTE_ADDR',
                FILTER_SANITIZE_STRING
                );
        $data = $this->getRequest()->getParams();
        $message = $this->_objectManager->create('Lof\HidePrice\Model\Message');
        $message_data = array();
        $message_data['name']         = $data['name'];
        $message_data['email']        = $data['email'];
        if(isset($data['telephone'])) {
            $message_data['phone']    = $data['telephone'];
        }
        $message_data['subject']      = $data['subject'];
        $message_data['content']      = $data['comment'];
        $message_data['entity_id']    = $data['entity_id'];
        $product = $this->_productloader->create()->load($data['entity_id']);
        $message_data['product_image']= $this->image->getImg($product,'100','', 'category_page_grid')->getUrl();
        $message_data['product_url']  = $product->getProductUrl();
        $message_data['hideprice_id'] = $data['hideprice_id'];

        $message->setData($message_data);
        $message->save();
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectUrl = $data['currUrl'];
        $secretkey = $this->_helper
                ->getConfigValue(
                        'lofhideprice/recaptcha/recaptcha_secretkey'
                        );
        $captchaErrorMsg = $this->_helper
                ->getConfigValue(
                        'lofhideprice/recaptcha/recaptcha_errormessage'
                        );
        
        if ($data['enablerecaptcha']) {
            if ($captchaErrorMsg == '') {
                $captchaErrorMsg = 'Invalid captcha. Please try again.';
            }
            $captcha = '';
            if (filter_input(INPUT_POST, 'g-recaptcha-response') !== null) {
                $captcha = filter_input(INPUT_POST, 'g-recaptcha-response');
            }

            if (!$captcha) {
                $this->messageManager->addError($captchaErrorMsg);
                return $resultRedirect->setUrl($redirectUrl);
            } else {
                $response = file_get_contents(
                        //"https://www.google.com/recaptch/api/siteverify"
                        self::REQUEST_URL
                        . "?secret=" . $secretkey
                        . "&response=" . $captcha
                        . "&remoteip=" . $remoteAddr);
                $response = json_decode($response, true);

                if ($response["success"] === false) {
                    $this->messageManager->addError($captchaErrorMsg);
                    return $resultRedirect->setUrl($redirectUrl);
                }
            }
        }

        try {

            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
           
            $error = false;

            if (!\Zend_Validate::is(trim($data['name']), 'NotEmpty')) {
                $error = true;
            }

            if (!\Zend_Validate::is(trim($data['email']), 'NotEmpty')) {
                $error = true;
            }

            if (!\Zend_Validate::is(trim($data['subject']), 'NotEmpty')) {
                $error = true;
            }

            if (!\Zend_Validate::is(trim($data['comment']), 'NotEmpty')) {
                $error = true;
            }

            if ($error) {
                throw new \Exception();
            }

            // send mail to recipients
            $this->_inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $transport = $this->_transportBuilder->setTemplateIdentifier(
                            $this->_helper->getConfig('emailsend/emailtemplate')
                    )->setTemplateOptions(
                            [
                                'area' => Area::AREA_FRONTEND,
                                'store' => $this->_storeManager
                                        ->getStore()
                                        ->getId(),
                            ]
                    )->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->_helper->getConfig('emailsend/emailsenderto'))
                    ->addTo($this->_helper->getconfig('emailsend/emailto'))
                    ->getTransport();

            $transport->sendMessage();
            $this->_inlineTranslation->resume();

            $this->messageManager->addSuccess(__('Contact Us request has been '
                    . 'received. We\'ll respond to you very soon.'));
            return $resultRedirect->setUrl($redirectUrl);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addException($e, __('Something went wrong '
                    . 'while sending the contact us request.'));
        }
        return $resultRedirect->setUrl($redirectUrl);
    }

}