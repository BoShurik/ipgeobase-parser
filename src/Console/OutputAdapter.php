<?php
/**
 * User: boshurik
 * Date: 30.10.16
 * Time: 16:45
 */

namespace BoShurik\IPGeoBase\Console;

use BoShurik\IPGeoBase\Event\CityEvent;
use BoShurik\IPGeoBase\Event\Events;
use BoShurik\IPGeoBase\Event\RangeEvent;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OutputAdapter implements EventSubscriberInterface
{
    /**
     * @var StyleInterface
     */
    private $io;

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::BEFORE_PARSE => 'onBeforeParse',
            Events::BEFORE_CITIES => 'onBeforeCities',
            Events::CITY => 'onCity',
            Events::AFTER_CITIES => 'onAfterCities',
            Events::BEFORE_RANGE => 'onBeforeRange',
            Events::RANGE => 'onRange',
            Events::AFTER_RANGE => 'onAfterRange',
            Events::AFTER_PARSE => 'onAfterParse',
        );
    }

    public function __construct(StyleInterface $io)
    {
        $this->io = $io;
    }

    /**
     * @param Event $event
     */
    public function onBeforeParse(Event $event)
    {
        $this->io->section('Starting parsing');
    }

    /**
     * @param Event $event
     */
    public function onBeforeCities(Event $event)
    {
        $this->io->section('Starting parsing cities');
        $this->io->progressStart();
    }

    /**
     * @param CityEvent $event
     */
    public function onCity(CityEvent $event)
    {
        $this->io->progressAdvance();
    }

    /**
     * @param Event $event
     */
    public function onAfterCities(Event $event)
    {
        $this->io->progressFinish();
        $this->io->success('Done');
    }

    /**
     * @param Event $event
     */
    public function onBeforeRange(Event $event)
    {
        $this->io->section('Stating parsing ranges');
        $this->io->progressStart();
    }

    /**
     * @param RangeEvent $event
     */
    public function onRange(RangeEvent $event)
    {
        $this->io->progressAdvance();
    }

    /**
     * @param Event $event
     */
    public function onAfterRange(Event $event)
    {
        $this->io->progressFinish();
        $this->io->success('Done');
    }

    /**
     * @param Event $event
     */
    public function onAfterParse(Event $event)
    {
        $this->io->success('Done');
    }
}