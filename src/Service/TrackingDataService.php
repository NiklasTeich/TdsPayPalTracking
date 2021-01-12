<?php declare(strict_types = 1);

namespace Tds\PayPalTracking\Service;

use Monolog\Logger;
use ReflectionClass;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Swag\PayPal\RestApi\V1\Resource\PaymentResource;
use Swag\PayPal\SwagPayPal;
use Tds\Merware\Core\Exchange\Import\Struct\Order\OrderStruct;
use Tds\PayPalTracking\PayPal\Api\Tracking;
use Tds\PayPalTracking\PayPal\Api\TrackingBatchRequest;
use Tds\PayPalTracking\PayPal\Resource\ShippingResource;

/**
 * Class TrackingDataService
 *
 * @package Tds\PayPalTracking\Service
 */

class TrackingDataService
{

    /**
     * @var Logger
     */

    private $monolog;

    /**
     * @var SystemConfigService
     */

    private $systemConfigService;

    /**
     * @var PaymentResource
     */

    private $paymentResource;

    /**
     * @var ShippingResource
     */

    private $shippingResource;

    /**
     * TrackingDataService constructor.
     *
     * @param Logger $logger
     * @param SystemConfigService $systemConfigService
     * @param PaymentResource $paymentResource
     * @param ShippingResource $shippingResource
     */

    public function __construct(Logger $logger, SystemConfigService $systemConfigService, PaymentResource $paymentResource, ShippingResource $shippingResource)
    {

        $this->monolog = $logger;
        $this->systemConfigService = $systemConfigService;
        $this->paymentResource = $paymentResource;
        $this->shippingResource = $shippingResource;

    }

    /**
     * Main function to send paypal tracking data
     *
     * @param OrderEntity $orderEntity
     * @param OrderStruct $orderStruct
     */

    public function sendPayPalData($orderEntity, $orderStruct)
    {

        $salesChannelId = $orderEntity->getSalesChannelId();

        $orderNumber = $orderStruct->getOrderNumber();

        if (!empty($orderNumber)) {

            $payId = $this->getOrderPayId($orderEntity);

            if (!empty($payId)) {

                $this->sendOrderTracking($orderStruct, $salesChannelId, $payId);

            }

        }

    }

    /**
     * Helper-function to get an order pay id by order struct
     *
     * @param OrderEntity $orderEntity
     *
     * @return string
     */

    private function getOrderPayId($orderEntity)
    {

        $payId = '';

        $transactions = $orderEntity->getTransactions();

        if (!empty($transactions)) {

            $transaction = $transactions->first();

            if (!empty($transaction)) {

                $transactionCustomFields = $transaction->getCustomFields();

                if (!empty($transactionCustomFields[SwagPayPal::ORDER_TRANSACTION_CUSTOM_FIELDS_PAYPAL_TRANSACTION_ID])) {

                    $payId = (string) $transactionCustomFields[SwagPayPal::ORDER_TRANSACTION_CUSTOM_FIELDS_PAYPAL_TRANSACTION_ID];

                }

            }

        }

        return $payId;

    }

    /**
     * Helper-function to send order tracking data
     *
     * @param OrderStruct $orderStruct
     * @param string $salesChannelId
     * @param string $payId
     *
     * @return array|Tracking[]
     */

    private function sendOrderTracking($orderStruct, $salesChannelId, $payId)
    {

        $response = $this->paymentResource->get($payId, $salesChannelId);

        $transactions = $response->getTransactions();

        foreach ($transactions as $transaction) {

            $relatedResources = $transaction->getRelatedResources();

            foreach ($relatedResources as $relatedResource) {

                $sale = $relatedResource->getSale();

                if (!empty($sale)) {

                    $saleId = $this->getSaleId($sale);

                    if (!empty($saleId)) {

                        $trackingNumber = '';

                        $trackingNumbers = $orderStruct->getTrackingNumbers();

                        if (!empty($trackingNumbers)) {

                            $trackingNumber = array_shift($trackingNumbers);

                        }

                        $requests = [];

                        $trackingRequest = $this->buildTracking($saleId, $trackingNumber);

                        $requests[] = $trackingRequest;

                        return $this->sendRequests($requests, $salesChannelId);

                    }

                }

                break;

            }

            break;

        }

    }

    /**
     * Function to send post tracking back request.
     *
     * @param $requests
     * @param $salesChannelId
     *
     * @return array|Tracking[]
     */

    private function sendRequests($requests, $salesChannelId)
    {

        try {

            $trackingBatchRequest = new TrackingBatchRequest();

            $trackingBatchRequest->setTrackers($requests);

            return $results = $this->shippingResource->send($trackingBatchRequest, $salesChannelId);

        } catch (\Exception $ex) {

            $this->monolog->error(
                sprintf(
                    'Error while sending tracking batch request: %s',
                    $ex->getMessage()
                )
            );

        }

    }

    /**
     * Helper-function to get a sale id by sale response.
     *
     * @param $sale
     *
     * @return null|string
     */

    private function getSaleId($sale)
    {

        $saleId = null;

        try {

            $saleReflection = new ReflectionClass(get_class($sale));

            $secret = $saleReflection->getProperty('id');

            $secret->setAccessible(true);

            $saleId = $secret->getValue($sale);

        } catch (\Exception $ex) {

            $this->monolog->error(
                sprintf(
                    'Error while parsing the sale id: %s',
                    $ex->getMessage()
                )
            );

        }

        return $saleId;

    }

    /**
     * Function to build an actual tracking request.
     * Default GLS (with tracking numbers), OTHER if no tracking number is available
     *
     * @param $saleId
     * @param string $trackingNumber
     *
     * @return Tracking
     */

    private function buildTracking($saleId, $trackingNumber = '')
    {

        $trackingRequest = new Tracking();

        $trackingRequest->setTransactionId($saleId);

        $trackingRequest->setStatus('SHIPPED');

        $trackingRequest->setNotifyBuyer(false);

        if (!empty($trackingNumber)) {

            $trackingRequest->setCarrier('GLS');

            $trackingRequest->setTrackingNumber($trackingNumber);

        } else {

            $trackingRequest->setCarrier('OTHER');

            $trackingRequest->setTrackingNumber('');

        }

        return $trackingRequest;

    }

}