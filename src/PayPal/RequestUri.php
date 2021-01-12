<?php declare(strict_types=1);

namespace Tds\PayPalTracking\PayPal;

/**
 * Class RequestUri
 *
 * @package Tds\PayPalTracking\PayPal
 */

final class RequestUri
{

    /**
     * shipping trackers resource
     */

    public const SHIPPING_RESOURCE = 'v1/shipping/trackers';

    /**
     * shipping trackers batch resource
     */

    public const SHIPPING_TRACKERS_BATCH_RESOURCE = 'v1/shipping/trackers-batch';

    private function __construct()
    {
    }

}
