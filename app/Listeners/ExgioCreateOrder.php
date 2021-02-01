<?php

namespace App\Listeners;

use App\Events\CreateOrder;
use App\Exigo\Exigo;
use App\Models\ExigoOrderType;
use App\Models\Order;
use App\Models\TransactionExigoOrder;
use Exception;
use Illuminate\Support\Facades\Log;

class ExgioCreateOrder
{
    /**
     * @var Exigo
     */
    protected Exigo $exigo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Exigo $exigo)
    {
        $this->exigo = $exigo;
    }

    /**
     * Handle the event.
     *
     * @param CreateOrder $event
     * @return void
     */
    public function handle(CreateOrder $event)
    {
        $created = $this->exigo->createOrder(
            $this->exigo->generateExigoOrderJson(
                $event->order,
                $event->exigoStatusID
            )
        );

        if ($created->getStatusCode() == 200) {
            //If synced succesfully, record the exigo order id against s order record
            $this->recordExigoSyncIds($event->order->id, $created->getResponseContent()->orderID, $event->order);
            return $created->getResponseContent()->orderID;
        }


        Log::error(' exigo create order request:' .
            $this->exigo->generateExigoOrderJson(
                $event->order,
                $event->exigoStatusID
            ) . ' exigo create order response:' . json_encode($created->getResponseContent()));
        throw new Exception('Failed to sync create record to exigo (' . $event->order . ')');
    }

    /**
     * @param int $OrderId
     * @param int $exigoOrderId
     * @param Order $Order
     * @throws Exception
     */
    protected function recordExigoSyncIds(string $OrderId, int $exigoOrderId, Order $Order)
    {
        if ($exigoOrderId) {
            $OrderExigoOrder = new TransactionExigoOrder([
                '_id' => $Order->id,
                'exigo_id' => $exigoOrderId,
                'exigo_type_id' => ExigoOrderType::EXIGO_TYPE_PURCHASE_ID
            ]);
            $OrderExigoOrder->save();
        } else {
            $errorMessage = sprintf(
                'Failed to sync shopify order %s to Exigo',
                $OrderId
            );
            Log::error($errorMessage);
            throw new Exception($errorMessage);
        }
    }
}
