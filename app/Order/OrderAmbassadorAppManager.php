<?php


namespace App\Order;

use App\Calc\OrderCalc\OrderCalcHostessLines;
use App\Calc\OrderCalc\OrderCalcDefault;
use App\Calc\OrderCalc\OrderCalcInterface;
use App\Calc\PreCalc\PreCalcAmbassadorAppOrder;
use App\Calc\PreCalc\PreCalcExistingOrder;
use App\Calc\PreCalc\PreCalcInterface;
use App\Exigo\Exigo;
use App\Models\ExigoOrderType;
use App\Models\StatusOrder;
use App\Models\StatusOrderLine;
use App\Models\LineItem;
use App\Models\Order;
use App\Models\TransactionExigoOrder;
use App\Models\TransactionType;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderAmbassadorAppManager extends OrderManager implements OrderManagerInterface
{
    /**
     * @param Request $request
     * @return Order
     * @throws GuzzleException
     */
    public function updateOrderLines(Request $request): Order
    {
        Log::info('updateOrderLines order id: ' . $request->input('order_id'));
        $preCalcOrder = new PreCalcAmbassadorAppOrder(
            $request,
            StatusOrder::STATUS_DRAFT_ID
        );
        $orderCalc = new OrderCalcHostessLines($preCalcOrder);
        $Order = $this->cancelSoftDeleteOrderAndReplace($preCalcOrder, $orderCalc);

        //Hostess lines are calculated using 'hostess allowance', which is derived from all the other orders
        // in a party, so when an order changes, this will affect the hostess order in a party
        // so it will need to be recalculated
        $this->recalculateOrdersHostessLines($preCalcOrder);

        return $Order;
    }

    /**
     * @param PreCalcInterface $preCalcOrder
     * @param OrderCalcInterface $orderCalc
     * @return Order
     * @throws GuzzleException
     */
    protected function cancelSoftDeleteOrderAndReplace(
        PreCalcInterface $preCalcOrder,
        OrderCalcInterface $orderCalc
    ): Order {
        //Cancel and soft delete original order (to be replace later) Also cancels the Exigo record
        $this->handleCancel($preCalcOrder->getOrderId());
        $this->handleSoftDeleting($preCalcOrder->getOrderId());

        //Create new Order to replace original that's been canceled and soft deleted
        $Order = $this->save(
            $preCalcOrder,
            Exigo::ORDER_STATUS_PENDING_ID,
            $orderCalc
        );

        return $Order;
    }

    /**
     * @param string $orderId
     */
    public function handleSoftDeleting(string $orderId)
    {
        $this->OrderRepository->softDeleteOrderById($orderId);
        $this->OrderLineRepository->softDeleteOrdersOrderLines($orderId);
    }

    /**
     * Hostess lines are calculated using 'hostess allowance', which is derived from all the other orders
     * in a party, so when an order changes, this will affect the hostess order in a party
     * so it will need to be recalculated
     * @param PreCalcAmbassadorAppOrder $preCalcOrder
     * @throws GuzzleException
     */
    protected function recalculateOrdersHostessLines(PreCalcAmbassadorAppOrder $preCalcOrder): void
    {
        //Check that the party the order is assocuated with has a hostess order associated with it as well
        $partyHostessOrderId = $this->OrderRepository->getHostessOrderIdfromPartyId(
            $preCalcOrder->getPamperId()
        );

        if ($partyHostessOrderId) {
            // Update Hostess order for pamper party
            $preCalcHostessOrder = new PreCalcExistingOrder(
                $partyHostessOrderId,
                StatusOrder::STATUS_DRAFT_ID
            );
            $hostessOrderCalc = new OrderCalcHostessLines($preCalcHostessOrder);

            $this->cancelSoftDeleteOrderAndReplace($preCalcHostessOrder, $hostessOrderCalc);
        }
    }

    /**
     * @param Request $request
     * @return Order
     * @throws GuzzleException
     */
    public function updateHostessLines(Request $request): Order
    {
        $preCalcOrder = new PreCalcAmbassadorAppOrder(
            $request,
            StatusOrder::STATUS_DRAFT_ID
        );
        $orderCalc = new OrderCalcHostessLines($preCalcOrder);

        $Order = $this->cancelSoftDeleteOrderAndReplace($preCalcOrder, $orderCalc);

        return $Order;
    }

    /**
     * @param Request $request
     * @return Order
     * @throws GuzzleException
     */
    public function createHostessLines(Request $request): Order
    {
        $preCalcOrder = new PreCalcAmbassadorAppOrder(
            $request,
            StatusOrder::STATUS_DRAFT_ID
        );

        $Order = $this->save(
            $preCalcOrder,
            Exigo::ORDER_STATUS_PENDING_ID,
            new OrderCalcHostessLines($preCalcOrder)
        );

        return $Order;
    }

    /**
     * @param Request $request
     * @return Order
     */
    public function create(Request $request): Order
    {
        $preCalcOrder = new PreCalcAmbassadorAppOrder(
            $request,
            StatusOrder::STATUS_DRAFT_ID
        );

        $Order = $this->save(
            $preCalcOrder,
            Exigo::ORDER_STATUS_PENDING_ID,
            new OrderCalcDefault($preCalcOrder)
        );

        //Hostess lines are calculated using 'hostess allowance', which is derived from all the other orders
        // in a party, so when an order changes, this will affect the hostess order in a party
        // so it will need to be recalculated
        $this->recalculateOrdersHostessLines($preCalcOrder);

        return $Order;
    }

    /**
     * @param Request $request
     * @return Order
     * @throws Exception
     */
    public function refund(Request $request): Order
    {
        $Order = Order::where('source', '=', Order::SOURCE_AMABASSADOR_APP)
            ->where('id', '=', $request->input('order_id'))
            ->firstOrFail();

        $refundTotalGross = 0;
        $refundTotalNet = 0;
        $refundTotalvat = 0;
        if ($request->input('refund_lines')) {
            foreach ($request->input('refund_lines') as $RefundLine) {
                $LineItem = LineItem::where('id', '=', $RefundLine['id'])
                    ->firstOrFail();

                $refundTotalGross -= $LineItem->gross;
                $refundTotalNet -= $LineItem->net;
                $refundTotalvat -= $LineItem->vat;

                $LineItem->status_financial_id = StatusOrderLine::STATUS_REFUNDED_ID;
                $LineItem->save();
            }
        }

        $Order->gross += $refundTotalGross;
        $Order->net += $refundTotalNet;
        $Order->vat += $refundTotalvat;

        $Order->status_id = $Order->calcOrderStatus($Order->gross);
        $Order->save();

        $this->saveTransaction(
            $Order->calcTransactionType($Order->gross),
            $Order->id,
            $request->input('refund_line_items.order_adjustments.0.reason'),
            'unknown001',
            $refundTotalGross
        );


        return $Order;
    }

    /**
     * @param Request $request
     * @return Order
     * @throws GuzzleException
     */
    public function cancel(Request $request): Order
    {
        $OrderOriginalState = Order::where('id', '=', $request->input('order_id'))
            ->firstOrFail();

        $Order = $this->handleCancel($request->input('order_id'));

        //Only issue refund transaction if the order has come out of draft
        if ($OrderOriginalState->status_id != StatusOrder::STATUS_DRAFT_ID) {
            $cancelAmount = $Order->calcCancellationGross($Order->gross);

            $this->saveTransaction(
                TransactionType::TYPE_CANCELLATION_ID,
                $Order->id,
                $request->input('refund_line_items.order_adjustments.0.reason'),
                'unknown001',
                $cancelAmount
            );
        }

        return $Order;
    }
}
