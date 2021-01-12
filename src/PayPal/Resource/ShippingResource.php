<?php declare(strict_types=1);

namespace Tds\PayPalTracking\PayPal\Resource;

use Swag\PayPal\RestApi\Client\PayPalClientFactory;
use Swag\PayPal\RestApi\Client\PayPalClientFactoryInterface;
use Tds\PayPalTracking\PayPal\Api\Tracking;
use Tds\PayPalTracking\PayPal\Api\TrackingBatchRequest;
use Tds\PayPalTracking\PayPal\RequestUri;

/**
 * Class ShippingResource
 *
 * @package Tds\PayPalTracking\PayPal\Resource
 */

class ShippingResource
{
    /**
     * @var PayPalClientFactoryInterface
     */

    private $payPalClientFactory;

    /**
     * ShippingResource constructor.
     *
     * @param PayPalClientFactory $payPalClientFactory
     */

    public function __construct(PayPalClientFactory $payPalClientFactory)
    {
        $this->payPalClientFactory = $payPalClientFactory;
    }

    /**
     * API function to get a shipping by it's sale id.
     *
     * @param string $saleId
     * @param string $trackingNumber
     * @param string $salesChannelId
     *
     * @return Tracking
     */

    public function get(string $saleId, string $trackingNumber, string $salesChannelId): Tracking
    {
        $response = $this->payPalClientFactory->getPayPalClient($salesChannelId)->sendGetRequest(
            \sprintf('%s/%s', RequestUri::SHIPPING_RESOURCE, $saleId . '-' . $trackingNumber)
        );

        return (new Tracking())->assign($response);
    }

    /**
     * Function to send shipping tracker batch requests.
     *
     * @param TrackingBatchRequest $trackingBatch
     * @param string $salesChannelId
     *
     * @return Tracking[]
     */

    public function send($trackingBatch, string $salesChannelId): array
    {

        $response = $this->payPalClientFactory->getPayPalClient($salesChannelId)->sendPostRequest(
            RequestUri::SHIPPING_TRACKERS_BATCH_RESOURCE,
            $trackingBatch
        );

        $trackingResponses = [];

        if (!empty($response['tracker_identifiers'])) {

            foreach ($response['tracker_identifiers'] as $responseTracking) {

                $trackingResponses[] = (new Tracking())->assign($responseTracking);

            }

        }

        return $trackingResponses;

    }

}