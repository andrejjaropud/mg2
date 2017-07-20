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
    /**
     * @var Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
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
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templatesFactory,
        \Magento\Email\Model\Template\Config $emailConfig,
        \Magento\Framework\Url $url,
        array $data = []
    ) {

        $this->_systemStore = $systemStore;
        $this->groupRepository = $groupRepository;
        $this->_objectConverter = $objectConverter;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_templatesFactory = $templatesFactory;
        $this->_emailConfig = $emailConfig;
        $this->_urlBuilder   = $url;
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Message Information')]);

        if ($model->getId()) {
            $fieldset->addField('form_id', 'hidden', ['name' => 'message_id']);
        }
        $fieldset->addField(
            'name',
            'note',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'text' => $model->getName()
            ]
        );
        $fieldset->addField(
            'email',
            'note',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'text' => $model->getEmail()
            ]
        );
        $fieldset->addField(
            'phone',
            'note',
            [
                'name' => 'phone',
                'label' => __('Phone'),
                'title' => __('Phone'),
                'text' => $model->getPhone()
            ]
        );
        $fieldset->addField(
            'subject',
            'note',
            [
                'name' => 'subject',
                'label' => __('Subject'),
                'title' => __('Subject'),
                'text' => $model->getSubject()
            ]
        );
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Catalog\Model\Product')->load($model->getEntityID());

        $editUrl = $this->_urlBuilder->getUrl(
                    'catalog/product/edit',
                    [
                        'id' => $model->getEntityID()
                    ]
                );

         $fieldset->addField(
            'entity_id',
            'note',
            [
                'name' => 'entity_id',
                'label' => __('Product'),
                'title' => __('Product'),
                'text' => sprintf("<a target='_blank' href='%s'>".$product->getName()."</a>", $editUrl)
            ]
        );

        $fieldset->addField(
            'content',
            'note',
            [
                'name' => 'content',
                'label' => __('Content'),
                'title' => __('Content'),
                'text' => $model->getSubject()
            ]
        );
        
        
        $fieldset->addField(
            'creation_time',
            'note',
            [
                'name' => 'creation_time',
                'label' => __('Created At'),
                'title' => __('Created At'),
                'text' => $model->getCreationTime()
            ]
        );
       
        
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
        return __('Message Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Message Information');
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
