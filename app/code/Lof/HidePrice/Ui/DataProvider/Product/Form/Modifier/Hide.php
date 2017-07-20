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
 * @package    Lof_Gallery
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\HidePrice\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Modal;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Hide extends AbstractModifier
{
    const DATA_SCOPE = '';
    const DATA_SCOPE_BANNER = 'hide';
    const GROUP_BANNER = 'hide';
    const GROUP_CONTENT = 'content';
    const SORT_ORDER = 20;
    /**
     * @var ModuleManager
     */
    protected $moduleManager;
    /**
     * @var GroupManagementInterface
     */
    protected $groupManagement;
     /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static $sortOrder = 90;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;


    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var AttributeSetRepositoryInterface
     */
    protected $attributeSetRepository;

    /**
     * @var string
     */
    protected $scopeName;

    protected $_resource;
    /**
     * @var string
     */
    protected $scopePrefix;

    /**
     * @var \Magento\Catalog\Ui\Component\Listing\Columns\Price
     */
    private $priceModifier;
    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
     /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
     /**
     * @var hidepriceCollection
     */
    protected $hidepriceCollection;
    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param BannerLinkRepositoryInterface $hideLinkRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ImageHelper $imageHelper
     * @param Status $status
     * @param AttributeSetRepositoryInterface $attributeSetRepository
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder,
        ImageHelper $imageHelper,
        Status $status,
        GroupRepositoryInterface $groupRepository,
        GroupManagementInterface $groupManagement,
        ModuleManager $moduleManager,
        AttributeSetRepositoryInterface $attributeSetRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        \Lof\HidePrice\Model\Hideprice $hidepriceCollection,
        $scopeName = 'product_form.product_form',
        $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->imageHelper = $imageHelper;
        $this->status = $status;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
        $this->request = $request;
        $this->_resource = $resource;
        $this->moduleManager = $moduleManager;
        $this->groupRepository = $groupRepository;
        $this->groupManagement = $groupManagement;
        $this->hidepriceCollection = $hidepriceCollection;

    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        
         $meta = array_replace_recursive(
            $meta,
            [
                'hideprice' => [
                    'children' => $this->getFieldsForFieldset(),
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Hide Price'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => 'data.links',
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );
        
        return $meta;
    }
    /**
     * Prepares children for the parent fieldset
     *
     * @return array
     */
    protected function getFieldsForFieldset()
    {
        $children = [];
            $children['callforprice_customergroup'] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'dataScope' => 'hideprice_id',
                            'componentType' => Form\Field::NAME,
                            'formElement' => Form\Element\Select::NAME,
                            'options' => $this->getHidePrice(),
                            'description' => __('Call For Price'),
                            'label' => 'Select Form Hide Price',
                        ],
                    ],
                ],
            ];

        return $children;
    }


    protected function getHidePrice() {
        $getHidePrice = [];
        $getHidePrice[] = [
            'label' => 'Choise form',
            'value' => '',
        ];
        foreach ($this->hidepriceCollection->getCollection() as $key => $hideprice) {
            $getHidePrice[] = [
                'label' => $hideprice->getCallforpriceText(),
                'value' => $hideprice->getHidepriceId(),
            ];
        } 
       
        return $getHidePrice;
    }

     /**
     * Retrieve allowed customer groups
     *
     * @return array
     */
    protected function getCustomerGroups()
    {
        if (!$this->moduleManager->isEnabled('Magento_Customer')) {
            return [];
        }
        $customerGroups = [
            [
                'label' => __('ALL GROUPS'),
                'value' => GroupInterface::CUST_GROUP_ALL,
            ]
        ];

        /** @var GroupInterface[] $groups */
        $groups = $this->groupRepository->getList($this->searchCriteriaBuilder->create());
        foreach ($groups->getItems() as $group) {
            $customerGroups[] = [
                'label' => $group->getCode(),
                'value' => $group->getId(),
            ];
        }

        return $customerGroups;
    }
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();
       
        if (!$productId) {
            return $data;
        }
        $connection = $this->_resource->getConnection();
        if($productId){
            $select = 'SELECT *  FROM  ' . $this->_resource->getTableName('lof_hideprice_product').'  WHERE  entity_id ='.$productId;
            $hideprice = $connection->fetchAll($select);
        }
        $dataHidePrice = array();
        foreach ($hideprice as $key => $_hideprice) {
            $dataHidePrice['hideprice_id'] = $_hideprice['hideprice_id'];
        }
        $data[$productId]['links'] =  $dataHidePrice;
        return $data;
    }

    /**
     * Get price modifier
     *
     * @return \Magento\Catalog\Ui\Component\Listing\Columns\Price
     * @deprecated
     */
    private function getPriceModifier()
    {
        if (!$this->priceModifier) {
            $this->priceModifier = ObjectManager::getInstance()->get(
                \Magento\Catalog\Ui\Component\Listing\Columns\Price::class
            );
        }
        return $this->priceModifier;
    }


    /**
     * Retrieve all data scopes
     *
     * @return array
     */
    protected function getDataScopes()
    {
        return [
            static::DATA_SCOPE_BANNER
        ];
    }

    /**
     * Prepares config for the Banner products fieldset
     *
     * @return array
     */
    protected function getBannerFieldset()
    {

        return $this->getGrid($this->scopePrefix . static::DATA_SCOPE_BANNER);
           
    }

    

    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getGrid($scope)
    {
        //$dataProvider = $scope . '_product_listing';
        $dataProvider = 'hide_product_listing';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'hide_id',
                            'title' => 'title',
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }'
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            
            /*'images' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'elementTmpl' => 'ui/dynamic-rows/cells/thumbnail',
                            'dataType' => Text::NAME,
                            'dataScope' => 'images',
                            'fit' => true,
                            'label' => __('Images'),
                            'sortOrder' => 10,
                        ],
                    ],
                ],
            ],*/
            'title' => $this->getTextColumn('title', false, __('Title'), 20),
            'position' => $this->getTextColumn('position', false, __('Position'), 60),
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ],
            
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
