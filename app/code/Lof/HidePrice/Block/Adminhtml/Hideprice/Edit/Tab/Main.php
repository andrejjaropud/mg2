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
namespace Lof\HidePrice\Block\Adminhtml\Hideprice\Edit\Tab;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as ObjectConverter;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Framework\Convert\DataObject $objectConverter, 
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
        $model = $this->_coreRegistry->registry('lofhideprice_hideprice');
        if ($this->_isAllowedAction('Lof_HidePrice::hideprice')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }
        
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Hideprice Information')]);
       
        if ($model->getId()) {
            $fieldset->addField('hideprice_id', 'hidden', ['name' => 'hideprice_id']);
        }
       
        $fieldset->addField(
            'callforprice_text',
            'text', 
            [
            'name' => 'callforprice_text',
            'label' => __('Call/Hide Price Text'),
            'title' => __('Call/Hide Price Text'),
            'disabled' => $isElementDisabled
            ]
        );  
        $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId().time()]);
       
        $fieldset->addField(
            'content',
            'editor',
            [
            'name'     => 'content',
            'label'    => __('Content'),
            'style' => 'height:20em;',
            'disabled' => $isElementDisabled,
            'config' => $wysiwygConfig
            ]
        );  
        $groupsOptions = $this->_objectConverter->toOptionArray(
            $this->groupRepository->getList($this->_searchCriteriaBuilder->create())->getItems(),
            'id', 'code'
            ); 
        $fieldset->addField(
        'callforprice_customergroup',
        'multiselect',
        [
        'name' => 'callforprice_customergroup[]',
        'label' => __('Customer Groups'),
        'title' => __('Customer Groups'),
        'required' => true,
        'values' =>  $groupsOptions, 
        'disabled' => $isElementDisabled,
        ]
        );
        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'store_id[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled,
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
        $fieldset->addField(
            'is_active',
            'select',
            [
            'label' => __('Status'),
            'title' => __('Status'),
            'name' => 'is_active',
            'options' => $model->getAvailableStatuses(),
            'disabled' => $isElementDisabled
            ]
        ); 

        //$fieldset = $form->addFieldset('inquiry_forms', ['legend' => __('Inquiry Form')]);
        // $fieldset->addField(
        //     'inquiry_form',
        //     'text',
        //     ['name' => 'inquiry_form', 'class' => 'requried-entry', 'value' => $model->getData('inquiry_form')]
        // );
        
        // $form->getElement(
        //     'inquiry_form'
        // )->setRenderer(
        //     $this->getLayout()->createBlock('Lof\HidePrice\Block\Adminhtml\Hideprice\Renderer\InquiryForm')
        // );
        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
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
        return __('Hideprice Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Hideprice Information');
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
