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


namespace Mirasvit\Feed\Model\Feed;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\App\Emulation;
use Mirasvit\Feed\Export\Handler;
use Mirasvit\Feed\Export\HandlerFactory;
use Mirasvit\Feed\Model\Config;
use Mirasvit\Feed\Model\Feed;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Exporter
{
    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * @var HandlerFactory
     */
    protected $handlerFactory;

    /**
     * @var History
     */
    protected $history;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var array
     */
    protected $handlers = [];

    /**
     * Constructor
     *
     * @param Emulation      $appEmulation
     * @param EventManager   $eventManager
     * @param HandlerFactory $handlerFactory
     * @param History        $history
     * @param Config         $config
     */
    public function __construct(
        Emulation $appEmulation,
        EventManager $eventManager,
        HandlerFactory $handlerFactory,
        History $history,
        Config $config
    ) {
        $this->appEmulation = $appEmulation;
        $this->eventManager = $eventManager;
        $this->handlerFactory = $handlerFactory;
        $this->history = $history;
        $this->config = $config;
    }

    /**
     * Export Handler
     *
     * @param Feed $feed
     * @return Handler
     */
    public function getHandler(Feed $feed)
    {
        if (!isset($this->handlers[$feed->getId()])) {
            $this->handlers[$feed->getId()] = $this->handlerFactory->create()
                ->setFeed($feed);
        }

        return $this->handlers[$feed->getId()];
    }

    /**
     * Export feed via browser (few iterations)
     *
     * @param Feed $feed
     * @return string
     */
    public function export(Feed $feed)
    {
        register_shutdown_function([$this, 'onShutdown'], $feed);

        $this->appEmulation->startEnvironmentEmulation($feed->getStore()->getId());

        $handler = $this->getHandler($feed);
        $handler->setFilename($feed->getFilename());

        $handler->execute();

        $this->updateFeed($feed, $handler);

        $this->appEmulation->stopEnvironmentEmulation();

        return $handler->getStatus();
    }

    /**
     * Export feed in shell
     *
     * @param Feed $feed
     * @return void
     * @throws \Exception
     */
    public function exportCli(Feed $feed)
    {
        $lockFile = $this->config->getTmpPath().'/'.$feed->getId().'.cli.lock';
        $lockPointer = fopen($lockFile, "w");

        if (flock($lockPointer, LOCK_EX | LOCK_NB)) {
            register_shutdown_function([$this, 'onShutdown'], $feed);

            $this->history->add($feed, __('Export'), __('Start export process'));

            $this->appEmulation->startEnvironmentEmulation($feed->getStore()->getId());

            $handler = $this->getHandler($feed);

            $handler->reset()
                ->setFilename($feed->getFilename());

            do {
                $handler->execute();

                $this->updateFeed($feed, $handler);

                yield $handler->getStatus() => $handler->toString();
            } while (!in_array($handler->getStatus(), [
                Config::STATUS_COMPLETED,
                Config::STATUS_ERROR,
            ]));

            $this->appEmulation->stopEnvironmentEmulation();

            flock($lockPointer, LOCK_UN);
        } else {
            throw new \Exception("File $lockFile already locked by another process");
        }

        fclose($lockPointer);
    }

    /**
     * Export feed preview (first 10 products)
     *
     * @param Feed  $feed
     * @return void
     */
    public function exportPreview(Feed $feed)
    {
        $appEmulation = $this->appEmulation;
        $appEmulation->startEnvironmentEmulation($feed->getStore()->getId());

        $handler = $this->getHandler($feed);

        $handler->reset();

        $handler->setFilename($feed->getPreviewFilename())
            ->enableTestMode();

        do {
            $handler->execute();
        } while (!in_array($handler->getStatus(), [
            Config::STATUS_COMPLETED,
            Config::STATUS_ERROR,
        ]));

        $appEmulation->stopEnvironmentEmulation();
    }

    /**
     * Update export information for feed
     * Dispatch event for emails
     *
     * @param Feed    $feed
     * @param Handler $handler
     * @return $this
     */
    protected function updateFeed(Feed $feed, Handler $handler)
    {
        $this->history->add($feed, __('Export'), $handler->toString());

        if ($handler->getStatus() == Config::STATUS_COMPLETED) {
            $feed->setGeneratedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
                ->setGeneratedTime($handler->getTimeSinceStart())
                ->save();

            $this->history->add($feed, __('Export'), __('Feed was successfully exported'));

            $this->eventManager->dispatch('feed_export_success', ['feed' => $feed]);
        }

        return $this;
    }

    /**
     * Save fatal errors to feed history
     *
     * @param Feed $feed
     * @return void
     */
    public function onShutdown($feed)
    {
        if (error_get_last()) {
            $error = error_get_last();
            if ($error['type'] === E_ERROR) {
                $message = $error['message'];
                $this->history->add($feed, __('Error'), $message);
            }
        }
    }
}
