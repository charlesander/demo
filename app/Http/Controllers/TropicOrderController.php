<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderAmbassadorAppCancelRequest;
use App\Http\Requests\OrderAmbassadorAppCreateHostessLineRequest;
use App\Http\Requests\OrderAmbassadorAppHostessLineUpdateRequest;
use App\Http\Requests\OrderAmbassadorAppOrderLineCreateRequest;
use App\Http\Requests\OrderAmbassadorAppRefundRequest;
use App\Http\Requests\OrderAmbassadorAppOrderLineUpdateRequest;
use App\Http\Requests\OrderShopifyCancelRequest;
use App\Http\Requests\OrderShopifyRefundRequest;
use App\Http\Requests\ShopifyOrderCreateRequest;
use App\Http\Resources\Order as OrderResource;
use App\Models\StatusOrder;
use App\Http\Resources\OrderCollection;
use App\Models\Order;
use App\Models\OrderType;
use App\Models\TransactionType;
use App\Repositories\OrderRepository;
use App\Services\SnsObserver;
use App\Order\OrderManagerFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController
{
    private OrderRepository $OrderRepository;

    protected SnsObserver $snsObserver;

    public function __construct(OrderRepository $OrderRepository, SnsObserver $snsObserver)
    {
        $this->OrderRepository = $OrderRepository;
        $this->snsObserver = $snsObserver;
    }

    /**
     * @OA\Post(
     * path="/xxxxxxxxxxxto/xxxxxxxxxxx",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"order_id","x_id","x_id","x_id","x_id"},
     *       @OA\Property(property="x_id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
	 * [snip]
     *      ),
     *       @OA\Property(property="hostess_lines", type="array",
     *          @OA\Items(
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="quantity", type="integer", example="5"),
     *          ),
     *      ),
     *    ),
     * ),
     * @OA\Response(response="200", description="Calculate the aportion values of a  Order record (no saving)")
     * )
     */
    public function updateOrderLinesFromAmbassadorApp(OrderAmbassadorAppOrderLineUpdateRequest $request)
    {
        Log::info("[orders/updateOLFAA][{$request->id}] Incoming request " .
            "updateOrderLinesFromAmbassadorApp {$request->getContent()}");

        $orderManager = OrderManagerFactory::create('ambassador-app');
        $Order = $orderManager->updateOrderLines($request);

        return new OrderResource($Order);
    }

    /**
     * @OA\Post(
     * path="//xxxxxxxxxxxto/xxxxxxxxxxx",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"x_id","x_id","x_id","source","x_id"},
     *       @OA\Property(property="x_id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *       [snip]
     *       @OA\Property(property="hostess_lines", type="array",
     *          @OA\Items(
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="quantity", type="integer", example="5"),
     *          ),
     *      ),
     *    ),
     * ),
     * @OA\Response(response="200", description="Calculate the aportion values of a  Order record (no
     * saving)")
     * )
     */
    public function updateHostessLinesFromAmbassadorApp(OrderAmbassadorAppHostessLineUpdateRequest $request)
    {
        Log::info("[orders/updateHLFAA][{$request->id}] Incoming request " .
            "updateHostessLinesFromAmbassadorApp {$request->getContent()}");

        $orderManager = OrderManagerFactory::create('ambassador-app');
        $Order = $orderManager->updateHostessLines($request);

        return new OrderResource($Order);
    }

    /**
     * @OA\Get(
     *     path="/xxxxxxxxxto",
     *     @OA\Response(response="200", description="Get a list of  Orders")
     * )
     */
    public function index(Request $request)
    {
        $length = $request->input('length') ?? 10;
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValues['search'] = $request->input('search');

        $orders = $this->OrderRepository->getPaginatedBy($length, $sortBy, $orderBy, $searchValues);
        return new OrderCollection($orders);
    }

	// [snip]
}
