<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.0.52
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Feed\Export\Step\Filtration;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Mirasvit\Feed\Export\Context;
use Mirasvit\Feed\Export\Step\AbstractStep;
use Mirasvit\Feed\Model\RuleFactory;

class Rule extends AbstractStep
{
    /**
     * {@inheritdoc}
     * @param RuleFactory              $ruleFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Context                  $context
     * @param array                    $data
     */
    public function __construct(
        RuleFactory $ruleFactory,
        ProductCollectionFactory $productCollectionFactory,
        Context $context,
        $data = []
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->context = $context;

        $this->ruleId = $data['rule_id'];

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeExecute()
    {
        parent::beforeExecute();

        $this->index = 0;
        $this->length = $this->getProductCollection()->getSize();
        $this->ruleFactory->create()->load($this->ruleId)
            ->clearProductIds();
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if ($this->isReady()) {
            $this->beforeExecute();
        }

        $rule = $this->ruleFactory->create()->load($this->ruleId);

        $validIds = [];
        while (!$this->isCompleted()) {
            $collection = $this->getProductCollection();

            $collection->getSelect()->limit(100, $this->index);

            $startIndex = $this->index;

            foreach ($collection as $product) {
                if ($rule->getConditions()->validate($product)) {
                    $validIds[] = $product->getId();
                }

                $this->index++;

                if ($this->context->isTimeout()) {
                    break 2;
                }
            }

            #sometimes collection getSize not equal real number of items
            if ($startIndex == $this->index) {
                $this->length = $this->index;
            }
        }

        $rule->saveProductIds($validIds);

        if ($this->isCompleted()) {
            $this->afterExecute();
        }
    }

    /**
     * Product collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProductCollection()
    {
        $collection = $this->productCollectionFactory->create()
            ->addStoreFilter($this->context->getFeed()->getStoreId())
            ->setStoreId($this->context->getFeed()->getStoreId());

        return $collection;
    }
}
