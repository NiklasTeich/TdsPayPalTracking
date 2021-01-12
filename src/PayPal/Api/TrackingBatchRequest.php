<?php declare(strict_types=1);

namespace Tds\PayPalTracking\PayPal\Api;

use Swag\PayPal\RestApi\PayPalApiStruct;

/**
 * Class TrackingBatchRequest
 *
 * @package Tds\PayPalTracking\PayPal\Api
 */

class TrackingBatchRequest extends PayPalApiStruct
{

    /**
     * @var Tracking[]
     */

    protected $trackers;

    /**
     * @return Tracking[]
     */
    public function getTrackers(): array
    {
        return $this->trackers;
    }

    /**
     * @param Tracking[] $trackers
     */
    public function setTrackers(array $trackers): void
    {
        $this->trackers = $trackers;
    }

}