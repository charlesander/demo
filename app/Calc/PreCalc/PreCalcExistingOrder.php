<?php

namespace App\Calc\PreCalc;

use App\Calc\SpecialOfferCalc;
use App\Models\Address;
use App\Models\Customer;
use App\Models\Status;
use App\Models\ApportionRecord;
use App\Models\LineItemDelivery;
use App\Models\LineItemHostess;
use App\Models\LineItemProductVariant;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PreCalcExistingOrder extends PreCalcOrder implements PreCalcInterface, PreCalcFromExistingOrderInterface
{
    /**
     * @var Order
     */
    protected Order $Order;

    public function __construct(
        string $orderId,
        int $orderStatus
    ) {
        $this->specialOfferCalc = new SpecialOfferCalc();
        $this->Order = Order::findOrFail($orderId);
        $this->manageRecordGeneration($orderStatus);
    }

    /**
     * @return array
     */
    public function generateHostessLineObjects(): array
    {
        $orderLineObjects = [];
        if ($this->Order->OrderLinesHostess) {
            foreach ($this->Order->OrderLinesHostess as $arrayLine) {
                $arrayLine = [
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['qv'],
                    'bv' => false, //Business Value not used in hostess lines
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx']
                ];
                $orderLineObjects[] = new LineItemHostess($arrayLine);
            }
        }
        $orderLineObjects = $this->specialOfferCalc->assignOfferToOrderLineItems($orderLineObjects);

        return $orderLineObjects;
    }

    /**
     * @return array
     */
    public function generateOrderLineObjects(): array
    {
        $orderLineObjects = [];
        if ($this->Order->OrderLines) {
            foreach ($this->Order->OrderLines as $arrayLine) {
                $arrayLine = [
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => false, //Business Value = QV only in order line objects
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx']
                ];
                $orderLineObjects[] = new LineItemProductVariant($arrayLine);
            }
        }

        $orderLineObjects = $this->specialOfferCalc->assignOfferToOrderLineItems($orderLineObjects);

        return $orderLineObjects;
    }

    /**
     * Generate discount line lines from the supplied voucher code
     * One voucher = one discount item line
     * Currently we only allow one voucher per order
     * On order can be made up of order, delivery, hostess and discount lines
     * @param string $voucherCode Currently only one vouchercode per order may be used
     * @return array
     */
    public function generateDiscountLineObjects(string $voucherCode): array
    {
        Log::info('Vouchercode used:' . $voucherCode);
        $discountLineObjects = [];
        if ($this->Order->OrderLinesDiscount) {
            foreach ($this->Order->OrderLinesDiscount as $arrayLine) {
                $arrayLine = [
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => false, //Business Value not used in discount lines
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx']
                ];
                $discountLineObjects[] = new LineItemDelivery($arrayLine);
            }
        }
        return $discountLineObjects;
    }

    /**
     * Addresses are handled speratly from the customer (unless a new customer is being created). This is because the
     * addresses can be different to the customer's existing address records. The address is taken directly from
     * the Shopify record's addresses.
     * @param
     * @return Customer
     */
    public function generateCustomer(): Customer
    {
        $customer = Customer::where('id', '=', $this->Order->customer_id)->first();
        return $customer;
    }

    /**
     * @param
     * @return Address
     */
    public function generateDeliveryAddress(): Address
    {
        return $this->customer->deliveryAddress()->first();
    }

    /**
     * @param
     * @return Address
     */
    public function generateBillingAddress(): Address
    {
        return $this->customer->homeAddress()->first();
    }

    /**
     * @return array
     */
    public function generateDeliveryLines(): array
    {
        $shippingLineObjects = [];
        if ($this->Order->OrderLinesDelivery) {
            foreach ($this->Order->OrderLinesDelivery as $arrayLine) {
                $arrayLine = [
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => false, //Business Value not used in delivery lines
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx'],
                    'xxxxxxxxxxxx' => $arrayLine['xxxxxxxxxxxx']
                ];
                $shippingLineObjects[] = new LineItemDelivery($arrayLine);
            }
        }
        return $shippingLineObjects;
    }

    /**
     * @return string
     */
    public function generateAmbassadorId(): string
    {
        return $this->Order->ambassador_id;
    }

    /**
     * @param
     * @return string
     */
    public function generateSource(): string
    {
        return $this->Order->source;
    }

    /**
     * @param
     * @return int
     */
    public function generateSaleChannelId(): int
    {
        return $this->Order->sales_channel_id;
    }

    /**
     * @param
     * @return string
     */
    public function generateNote(): string
    {
        return $this->Order->note;
    }

    /**
     * @param
     * @return string
     */
    public function generateVoucherCode(): string
    {
        //We dont know what it is, BUT, we'll be copying over the existing discount line(s)
        // in generateDiscountLineObjects() so this doesn't matter
        return '';
    }

    /**
     * @return int
     */
    public function generateShopifyOrderId(): ?int
    {
        //Orders generated in the ambassador app will not be associated with a shopify order
        return null;
    }

    /**
     * @param
     * @return string|null
     */
    public function generatePamperId(): ?string
    {
        return $this->Order->pamper_id;
    }

    /**
     * @param
     * @return string|null
     */
    public function generateOrderId(): ?string
    {
        return $this->Order->id;
    }

    /**
     * @return ?int
     */
    public function generateOrderTypeId(): ?int
    {
        return $this->Order->_type_id;
    }
}
