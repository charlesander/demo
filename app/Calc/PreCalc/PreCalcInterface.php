<?php


namespace App\Calc\PreCalc;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

interface PreCalcInterface
{
    public function manageRecordGeneration(int $orderStatus);

    public function generateHostessLineObjects(): array;

    public function generateOrderLineObjects(): array;

    public function generateDiscountLineObjects(string $voucherCode): array;

    public function generateDeliveryLines(): array;

    public function generateAmbassadorId(): string;

    public function generateSource(): string;

    public function generateNote(): string;

    public function generateVoucherCode(): string;

    public function getNote(): string;

    public function getSource(): string;

    public function generateSaleChannelId(): int;

    public function getSalesChannelId(): int;

    public function getAmbassadorId(): string;

    public function generateOrderTypeId(): ?int;

    public function getOrderTypeId(): ?int;

    public function getVoucherCode(): string;

    public function getHostessLineObjects(): array;

    public function getOrderLineObjects(): array;

    public function getDiscountLineObjects(): array;

    public function getDeliveryLineObjects(): array;

    public function getOrderStatus(): int;

    public function generateCustomer(): Customer;

    public function generateBillingAddress(): Address;

    public function generateDeliveryAddress(): Address;

    public function getCustomer(): Customer;

    public function getBillingAddress(): Address;

    public function getDeliveryAddress(): Address;

    public function generateShopifyOrderId(): ?int;

    public function getShopifyOrderId(): ?int;

    public function generatePamperId(): ?string;

    public function getPamperId(): ?string;

    public function generateOrderId(): ?string;

    public function getOrderId(): ?string;
}
