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


namespace Mirasvit\Feed\Console\Command;

use Magento\Framework\App\State;
use Mirasvit\Feed\Model\FeedFactory;
use Mirasvit\Feed\Model\Feed\Deliverer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DeliveryCommand extends AbstractCommand
{
    const INPUT_FEED_ID = 'id';

    /**
     * @var FeedFactory
     */
    protected $feedFactory;

    /**
     * @var Deliverer
     */
    protected $deliverer;

    /**
     * {@inheritdoc}
     * @param FeedFactory $feedFactory
     * @param Deliverer   $deliverer
     * @param State       $appState
     */
    public function __construct(
        FeedFactory $feedFactory,
        Deliverer $deliverer,
        State $appState
    ) {
        $this->feedFactory = $feedFactory;
        $this->deliverer = $deliverer;

        parent::__construct($appState);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::INPUT_FEED_ID,
                null,
                InputOption::VALUE_REQUIRED,
                'Feed ID',
                false
            )
        ];
        $this->setName('mirasvit:feed:delivery')
            ->setDescription('Delivery Feed')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('frontend');

        $feedId = $input->getOption(self::INPUT_FEED_ID);
        $verbose = $output->getVerbosity() == 2 ? true : false;

        if (!intval($feedId)) {
            $output->writeln('<error>Invalid feed id for option "id".</error>');

            return;
        }

        /** @var \Mirasvit\Feed\Model\Feed $feed */
        $feed = $this->feedFactory->create()->load($feedId);

        if (!$feed->getId()) {
            $output->writeln('<error>Feed not exists.</error>');

            return;
        }

        if ($verbose) {
            $output->writeln('<info>' . $feed->getName() . '</info>');
        }

        try {
            $this->deliverer->delivery($feed);

            $output->writeln('<info>' . __('Feed was successfully delivered to "%1"', $feed->getFtpHost()) . '</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . __('Unable to delivery feed. %1', $e->getMessage()) . '</error>');
        }
    }
}
