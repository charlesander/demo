<?php

namespace App\Calc\PreCalc;

use App\Calc\SpecialOfferCalc;
use App\Models\Address;
use App\Models\Customer;
use Illuminate\Http\Request;

abstract class PreCalcOrder implements PreCalcInterface
{
    /**
     * @var
     */
    protected ?string $orderId;

    /**
     * @var int
     */
    protected ?string $ambassadorId;

    /**
     * @var int
     */
    protected ?int $OrderTypeId;

    /**
     * @var string
     */
    protected ?string $voucherCode;

    /**
     * @var string
     */
    protected string $source;
    /**
     * @var string
     */
    protected ?string $note;
    /**
     * @var int
     */
    protected int $salesChannelId;
    /**
     * @var int|null
     */
    protected ?string $shopifyOrderId;
    /**
     * @var string|null
     */
    protected ?string $pamperPartyId;

    /**
     * @var array
     */
    protected array $hostessLineObjects;
    /**
     * @var array
     */
    protected array $orderLineObjects;
    /**
     * @var array
     */
    protected array $discountLineObjects;
    /**
     * @var array
     */
    protected array $deliveryLineObjects;

    /**
     * @var int
     */
    protected int $orderStatus;
    /**
     * @var Customer
     */
    protected $customer;
    /**
     * @var Address
     */
    protected $deliveryAddress;
    /**
     * @var Address
     */
    protected $billingAddress;
    /**
     * @var SpecialOfferCalc
     */
    protected SpecialOfferCalc $specialOfferCalc;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @param int $orderStatus
     */
    public function manageRecordGeneration(int $orderStatus)
    {
        //needs to be calculated first
        $this->orderId = $this->generateOrderId();

        $this->ambassadorId = $this->generateAmbassadorId();

        $this->OrderTypeId = $this->generateOrderTypeId();
        $this->voucherCode = $this->generateVoucherCode();

        $this->source = $this->generateSource();

        $this->note = $this->generateNote();

        $this->salesChannelId = $this->generateSaleChannelId();

        $this->shopifyOrderId = $this->generateShopifyOrderId();

        $this->pamperPartyId = $this->generatePamperId();

        $this->hostessLineObjects = $this->generateHostessLineObjects();

        $this->orderLineObjects = $this->generateOrderLineObjects();

        $this->discountLineObjects = $this->generateDiscountLineObjects($this->voucherCode);

        $this->deliveryLineObjects = $this->generateDeliveryLines();

        $this->orderStatus = $orderStatus;

        /**
         * Addresses are handled seperatly from the customer (unless a new customer is being created). This is because
         * the addresses can be different to the customer's existing address records. The address is taken directly from
         * the Shopify record's addresses.
         */
        $this->customer = $this->generateCustomer();

        $this->deliveryAddress = $this->generateDeliveryAddress();

        $this->billingAddress = $this->generateBillingAddress();
    }

    /**
     * @return string
     */
    public function getAmbassadorId(): string
    {
        return $this->ambassadorId;
    }

    /**
     * @return string
     */
    public function getVoucherCode(): string
    {
        return $this->voucherCode;
    }

    /**
     * @return array
     */
    public function getHostessLineObjects(): array
    {
        return $this->hostessLineObjects;
    }

    /**
     * @return array
     */
    public function getOrderLineObjects(): array
    {
        return $this->orderLineObjects;
    }

    /**
     * @return array
     */
    public function getDiscountLineObjects(): array
    {
        return $this->discountLineObjects;
    }

    /**
     * @return array
     */
    public function getDeliveryLineObjects(): array
    {
        return $this->deliveryLineObjects;
    }

    /**
     * @return int
     */
    public function getOrderStatus(): int
    {
        return $this->orderStatus;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getOrderTypeId(): ?int
    {
        return $this->OrderTypeId;
    }

    /**
     * @return Address
     */
    public function getDeliveryAddress(): Address
    {
        return $this->deliveryAddress;
    }

    /**
     * @return Address
     */
    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function getSalesChannelId(): int
    {
        return $this->salesChannelId;
    }

    public function getShopifyOrderId(): ?int
    {
        return $this->shopifyOrderId;
    }

    public function getPamperId(): ?string
    {
        return $this->pamperPartyId;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }
}
