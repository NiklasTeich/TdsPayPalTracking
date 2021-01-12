<?php declare(strict_types=1);

namespace Tds\PayPalTracking\PayPal\Api;

use Swag\PayPal\RestApi\PayPalApiStruct;

/**
 * Class Tracking
 *
 * @package Tds\PayPalTracking\PayPal\Api
 */

class Tracking extends PayPalApiStruct
{

    /**
     * @var string
     */

    protected $transactionId;

    /**
     * @var string
     */

    protected $status;

    /**
     * @var string
     */

    protected $carrier;

    /**
     * @var string
     */

    protected $shipmentDate;

    /**
     * @var bool
     */

    protected $notifyBuyer;

    /**
     * @var string
     */

    protected $trackingNumber;

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param string $transactionId
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getCarrier()
    {
        return $this->carrier;
    }

    /**
     * @param string $carrier
     */
    public function setCarrier($carrier)
    {
        $this->carrier = $carrier;
    }

    /**
     * @return string
     */
    public function getShipmentDate()
    {
        return $this->shipmentDate;
    }

    /**
     * @param string $shipmentDate
     */
    public function setShipmentDate($shipmentDate)
    {
        $this->shipmentDate = $shipmentDate;
    }

    /**
     * @return bool
     */
    public function getNotifyBuyer()
    {
        return $this->notifyBuyer;
    }

    /**
     * @param bool $notifyBuyer
     */
    public function setNotifyBuyer(bool $notifyBuyer)
    {
        $this->notifyBuyer = $notifyBuyer;
    }

    /**
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * @param string $trackingNumber
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;
    }

}