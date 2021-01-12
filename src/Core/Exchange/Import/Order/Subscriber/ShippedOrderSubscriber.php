<?php declare(strict_types=1);

namespace Tds\PayPalTracking\Core\Exchange\Import\Order\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tds\Merware\Core\Exchange\Import\Event\Order\ShippedOrderEvent;
use Tds\PayPalTracking\Service\TrackingDataService;

/**
 * Class ShippedOrderSubscriber
 *
 * @package Tds\PayPalTracking\Core\Exchange\Import\Order\Subscriber
 */

class ShippedOrderSubscriber implements EventSubscriberInterface
{

    /**
     * @var TrackingDataService
     */

    private $trackingDataService;

    /**
     * @return array
     */

    public static function getSubscribedEvents(): array
    {
        return [
            ShippedOrderEvent::class => 'onShippedOrder'
        ];
    }

    /**
     * ShippedOrderSubscriber constructor.
     *
     * @param TrackingDataService $trackingDataService
     */

    public function __construct(TrackingDataService $trackingDataService)
    {

        $this->trackingDataService = $trackingDataService;

    }

    /**
     * @param ShippedOrderEvent $event
     */

    public function onShippedOrder(ShippedOrderEvent $event)
    {

        $this->trackingDataService->sendPayPalData($event->getOrderEntity(), $event->getOrderStruct());

    }

}