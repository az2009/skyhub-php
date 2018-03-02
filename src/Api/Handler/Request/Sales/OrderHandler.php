<?php
/**
 * B2W Digital - Companhia Digital
 *
 * Do not edit this file if you want to update this SDK for future new versions.
 * For support please contact the e-mail bellow:
 *
 * sdk@e-smart.com.br
 *
 * @category  SkuHub
 * @package   SkuHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br).
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */

namespace SkyHub\Api\Handler\Request\Sales;

use SkyHub\Api\EntityInterface\Sales\Order;
use SkyHub\Api\Handler\Request\HandlerAbstract;

use SkyHub\Api\DataTransformer\Sales\Order\ApproveTest as ApproveTestTransformer;
use SkyHub\Api\DataTransformer\Sales\Order\Invoice as InvoiceTransformer;
use SkyHub\Api\DataTransformer\Sales\Order\Cancel as CancelTransformer;
use SkyHub\Api\DataTransformer\Sales\Order\Delivery as DeliveryTransformer;
use SkyHub\Api\DataTransformer\Sales\Order\Shipment as ShipmentTransformer;
use SkyHub\Api\DataTransformer\Sales\Order\ShipmentException as ShipmentExceptionTransformer;

class OrderHandler extends HandlerAbstract
{

    const STATUS_CANCELLED = 'order_canceled';
    const STATUS_PAID      = 'payment_received';
    const STATUS_COMPLETE  = 'complete';
    const STATUS_SHIPPED   = 'order_shipped';


    /** @var string */
    protected $baseUrlPath = '/orders';


    /**
     * Retrieves a list of all orders available in SkyHub.
     *
     * @param int    $page
     * @param int    $perPage
     * @param string $saleSystem
     * @param array  $statuses
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function orders($page = 1, $perPage = 30, $saleSystem = null, array $statuses = [])
    {
        $filters = [];

        if (!is_null($saleSystem)) {
            $filters['sale_system'] = (string) $saleSystem;
        }

        if (!is_null($statuses)) {
            $filters['statuses'] = (array) $statuses;
        }

        $query = [
            'page'     => (int)   $page,
            'per_page' => (int)   $perPage,
            'filters'  => (array) $filters,
        ];

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->get($this->baseUrlPath(null, $query));
        return $responseHandler;
    }


    /**
     * Retrieves an order according to an order ID.
     *
     * @param string $orderId
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function order($orderId)
    {
        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->get($this->baseUrlPath("$orderId"));
        return $responseHandler;
    }


    /**
     * Creates a test order in SkyHub.
     *
     * @todo Finish this method afterwards.
     *
     * @param array ...$orderData
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function createTest(...$orderData)
    {
        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
         $responseHandler = $this->service()->post($this->baseUrlPath());
         return $responseHandler;
    }


    /**
     * Updates and order with approval data. Just for tests.
     *
     * @var string $orderId
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function approveTest($orderId)
    {
        $transformer = new ApproveTestTransformer($orderId, self::STATUS_PAID);
        $body        = $transformer->output();

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->post($this->baseUrlPath("$orderId/approval"), $body);
        return $responseHandler;
    }


    /**
     * Invoice an order in SkyHub.
     *
     * @var string $orderId
     * @var string $invoiceKey
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function invoice($orderId, $invoiceKey)
    {
        $transformer = new InvoiceTransformer(self::STATUS_PAID, $invoiceKey);
        $body        = $transformer->output();

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->post($this->baseUrlPath("$orderId/invoice"), $body);
        return $responseHandler;
    }


    /**
     * Cancel an order in SkyHub.
     *
     * @var string $orderId
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function cancel($orderId)
    {
        $transformer = new CancelTransformer(self::STATUS_CANCELLED);
        $body        = $transformer->output();

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->post($this->baseUrlPath("$orderId/invoice"), $body);
        return $responseHandler;
    }


    /**
     * @var string $orderId
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function delivery($orderId)
    {
        $transformer = new DeliveryTransformer(self::STATUS_COMPLETE);
        $body        = $transformer->output();

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->post($this->baseUrlPath("$orderId/delivery"), $body);
        return $responseHandler;
    }


    /**
     * @param string $orderId
     * @param array  $items
     * @param string $trackCode
     * @param string $trackCarrier
     * @param string $trackMethod
     * @param string $trackUrl
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function shipment($orderId, array $items, $trackCode, $trackCarrier, $trackMethod, $trackUrl)
    {
        $transformer = new ShipmentTransformer(
            $orderId,
            self::STATUS_SHIPPED,
            $items,
            $trackCode,
            $trackCarrier,
            $trackMethod,
            $trackUrl
        );

        $body = $transformer->output();

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->post($this->baseUrlPath("$orderId/shipments"), $body);
        return $responseHandler;
    }


    /**
     * Retrieves the shipment labels for an order ID.
     *
     * @param string $orderId
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function shipmentLabels($orderId)
    {
        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->get($this->baseUrlPath("$orderId/shipment_labels"));
        return $responseHandler;
    }


    /**
     * @param string $orderId
     * @param string $datetime
     * @param string $observation
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    public function shipmentException($orderId, $datetime, $observation)
    {
        $transformer = new ShipmentExceptionTransformer($orderId, $datetime, $observation);
        $body        = $transformer->output();

        /** @var \SkyHub\Api\Handler\Response\HandlerInterface $responseHandler */
        $responseHandler = $this->service()->post($this->baseUrlPath("$orderId/shipment_exception"), $body);
        return $responseHandler;
    }
    
    
    /**
     * @return Order
     */
    public function entityInterface()
    {
        return new Order($this);
    }
}
