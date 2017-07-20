<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_HidePrice
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\HidePrice\Block\Adminhtml\Message\Edit\Tab;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;

class Email extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    protected $_wysiwygConfig;
    /**
     * [__construct description]
     * @param \Magento\Backend\Block\Template\Context                       $context               
     * @param \Magento\Framework\Registry                                   $registry              
     * @param \Magento\Framework\Data\FormFactory                           $formFactory           
     * @param GroupRepositoryInterface                                      $groupRepository       
     * @param ObjectConverter                                               $objectConverter       
     * @param SearchCriteriaBuilder                                         $searchCriteriaBuilder 
     * @param \Magento\Store\Model\System\Store                             $systemStore           
     * @param \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory      
     * @param \Magento\Email\Model\Template\Config                          $emailConfig           
     * @param array                                                         $data                  
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        GroupRepositoryInterface $groupRepository,
        ObjectConverter $objectConverter,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
        $this->groupRepository = $groupRepository;
        $this->_objectConverter = $objectConverter;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig = $emailConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofhideprice_message');

        if ($this->_isAllowedAction('Lof_HidePrice::message')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Client Information')]);
        if ($model->getId()) {
            $fieldset->addField('form_id', 'hidden', ['name' => 'message_id']);
        }
        if($model->getReply() == 'No') {
            $model->setReply('Unsent'); 
        } else {
            $model->setReply('Sent');
        }
        $fieldset->addField(
            'reply',
            'note',
            [
                'name' => 'reply',
                'label' => __('Status'),
                'title' => __('Status'),
                'text' => $model->getReply()
            ]
        );

        $fieldset->addField(
            'reply_subject',
            'text',
            [
                'name' => 'reply_subject',
                'label' => __('Subject'),
                'title' => __('Subject'),
                'disabled' => $isElementDisabled,
                'value' => $model->getReplySubject()
            ]
        );
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);
        
        $fieldset->addField(
            'reply_content',
            'editor',
            [
            'name'     => 'content',
            'label'    => __('Content'),
            'style' => 'height:20em;',

            'note' => __('{{var product_name}} name  product </br>
                {{var product_image|raw}} image  product </br>  
                {{var product_url}}  name customer </br>    
                {{var name}}  name customer </br>
                {{var email }} email customer </br>
                {{template config_path="design/email/header_template"}} header email template </br>
                {{template config_path="design/email/footer_template"}} footer email template
                '),
            'disabled' => $isElementDisabled,
            'config' => $wysiwygConfig,
            ]
        );
        $reply_subject = "Price for product  {{var product_name}}";
        $reply_content = '{template config_path="design/email/header_template"}} 
        <h3 style="color: #073763;">Dear {{var name}},</h3>
        <p><strong style="text-transform: uppercase;">Product item:</strong></p>
        <table style="border-collapse: collapse;" width="0" cellspacing="5" cellpadding="10" border="0">
            <tbody>
                <tr>
                    <td style="padding-right: 10px; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; vertical-align: top;">
                        <div class="lof-image">{{var product_image|raw}}</div>
                    </td>
                    <td style="padding-right: 10px; font-family: \'Open Sans\', \'Helvetica Neue\', Helvetica, Arial, sans-serif; vertical-align: top;" valign="top">
                    <a href="{{var product_url}}" style="color: #1979c3; text-decoration: none;">{{var product_name}}</a>
                    <hr style="border: 0; border-top: 1px solid #cccccc; margin-bottom: 20px; margin-top: 20px;">
                    '.__('Price').' <span class="price">$100</span>    
                    </td>
                </tr>        
            </tbody>
        </table>
        <p><strong style="text-transform: uppercase;">Thank you for sending us price request.Here is our price offer for you</strong></p>
        <p><strong style="text-transform: uppercase;">Please Contact Us If You Have Any Question. </strong></p>
        <p><strong style="text-transform: uppercase;">Thank You </strong></p>
        {{template config_path="design/email/footer_template"}}';
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '0' : '1');
        }
        if(empty($model->getReplyContent())) {
            $model->setData('reply_content',$reply_content);
        }
        if(empty($model->getReplySubject())) {
            $model->setData('reply_subject',$reply_subject);
        }
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

     
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Reply Email');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Reply Email');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
