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


namespace Mirasvit\Feed\Reports;

use Mirasvit\Report\Model\AbstractReport;
use Mirasvit\Report\Model\Select\Column;

class Feed extends AbstractReport
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Feed');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'feed_overview';
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setBaseTable('mst_feed_report');

        $this->addFastFilters([
            'mst_feed_report|created_at',
            'mst_feed_report|feed_id',
        ]);

        $this->setDefaultColumns([
            'mst_feed_report|sum_clicks',
            'mst_feed_report|sum_orders',
            'mst_feed_report|sum_subtotal',
            'mst_feed_report|subtotal_per_click',
        ]);

        $this->setDefaultDimension('mst_feed_report|day');

        $this->addAvailableDimensions([
            'mst_feed_feed|name',
            'catalog_product_entity|sku',
            'mst_feed_report|day',
            'mst_feed_report|week',
            'mst_feed_report|month',
            'mst_feed_report|year',
        ]);

        $this->setGridConfig([
            'paging' => true,
        ]);

        $this->setChartConfig([
            'chartType' => 'column',
            'vAxis'     => 'mst_feed_report|sum_clicks',
        ]);
    }
}
