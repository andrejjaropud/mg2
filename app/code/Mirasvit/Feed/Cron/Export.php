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


namespace Mirasvit\Feed\Cron;

use Magento\Framework\App\State as AppState;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Model\Feed\DelivererFactory;
use Mirasvit\Feed\Model\Feed\ExporterFactory;
use Mirasvit\Feed\Model\Feed\History;
use Mirasvit\Feed\Model\ResourceModel\Feed\Collection as FeedCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Export
{
    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var FeedCollection
     */
    protected $feedCollection;

    /**
     * @var ExporterFactory
     */
    protected $exporterFactory;

    /**
     * @var DelivererFactory
     */
    protected $delivererFactory;

    /**
     * @var History
     */
    protected $history;

    /**
     * Constructor
     *
     * @param AppState         $appState
     * @param DateTime         $dateTime
     * @param FeedCollection   $feedCollection
     * @param ExporterFactory  $exporterFactory
     * @param DelivererFactory $delivererFactory
     * @param History          $history
     */
    public function __construct(
        AppState $appState,
        DateTime $dateTime,
        FeedCollection $feedCollection,
        ExporterFactory $exporterFactory,
        DelivererFactory $delivererFactory,
        History $history
    ) {
        $this->appState = $appState;
        $this->dateTime = $dateTime;
        $this->feedCollection = $feedCollection;
        $this->exporterFactory = $exporterFactory;
        $this->delivererFactory = $delivererFactory;
        $this->history = $history;
    }

    /**
     * Export and delivery feeds
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $collection = $this->feedCollection
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('cron', 1);

        /** @var Feed $feed */
        foreach ($collection as $feed) {
            if ($this->canExport($feed) >= 0) {
                $feed = $feed->load($feed->getId());
                $exporter = $this->exporterFactory->create();
                $deliverer = $this->delivererFactory->create();

                try {
                    foreach ($exporter->exportCli($feed) as $status => $message) {

                    }

                    if ($feed->getFtp()) {
                        $deliverer->delivery($feed);
                    }
                } catch (\Exception $e) {
                    $this->history->add($feed, 'Exception', $e->getMessage());
                    echo $e;
                }
            } else {
                $this->history->add($feed, 'Cron', 'Skip cron job.');
            }
        }
    }

    /**
     * Check conditions for ability to run feed export by cron
     *
     * @param Feed $feed
     * @param int  $timestamp
     * @return int
     */
    public function canExport(Feed $feed, $timestamp = null)
    {
        $result = -1;

        $currentDay = (int)$this->dateTime->date('w', $timestamp);
        $currentDayOfYear = (int)$this->dateTime->date('z', $timestamp);
        $currentTime = (int)$this->dateTime->date('G', $timestamp) * 60 + (int)$this->dateTime->date('i', $timestamp);

        $lastRun = strtotime($feed->getGeneratedAt());
        $lastDayOfYear = $this->dateTime->date('z', $lastRun);
        $lastTime = (int)$this->dateTime->date('G', $lastRun) * 60 + (int)$this->dateTime->date('i', $lastRun);
        if (!$feed->getGeneratedAt()) {
            $lastTime = $currentTime - 25;
        }

        // we run generation minimum day ago. Need run generation
        if ($currentDayOfYear > $lastDayOfYear) {
            $lastTime = 0;
        }

        if (in_array($currentDay, $feed->getCronDay())) {
            foreach ($feed->getCronTime() as $cronTime) {
                if ($currentTime >= $cronTime
                    && $cronTime >= $lastTime
                    && $currentTime - $lastTime > 10
                ) {
                    $result = $cronTime;
                    break;
                }
            }
        }

        return $result;
    }
}
