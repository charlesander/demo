<?php


namespace Tests\Unit\Calc;

use App\Calc\OrderCalc\OrderCalcDefault;
use App\Calc\PreCalc\PreCalcAmbassadorAppOrder;
use App\Models\StatusOrder;
use App\Models\LineItemHostess;
use App\Models\LineItemProductVariant;
use App\Models\OrderApportion;
use Mockery;
use Tests\LinesGenerator;
use Tests\TestCase;

class OrderCalcDefaultTest extends TestCase
{
    /**
     * @var LinesGenerator
     */
    protected LinesGenerator $linesGenerator;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this-> linesGenerator =$this->app->make(LinesGenerator::class);
    }

    /**
     * @test
     */
    public function simple_test()
    {
        $preCalcOrder = $this->generate_pre_calc_ambassador_app();
        $this->assertEquals($preCalcOrder->getOrderId(), 1);
        $this->assertEquals($preCalcOrder->getNote(), 'Test note');

        $orderCalc = new OrderCalcDefault($preCalcOrder);

        $this->assertEquals($orderCalc->getValueTotal(), 1000);
        $this->assertEquals($orderCalc->getHostessAllowance(), 150.0);
        $this->assertEquals($orderCalc->getHostessAllowanceWithoutHostessLines(), 150.0);
        $this->assertEquals($orderCalc->getQvTotal(), 1000.0);
        $this->assertEquals($orderCalc->getTaxTotal(), 166.6666666666667);
        $this->assertEquals($orderCalc->getApportionBreakdown(), new OrderApportion([
            "sku" => OrderApportion::_PORTION_SKU,
            "gross" => 750.0,
            "value" => 750.0,
            "net" => 625.0,
            "vat" => 125.00000000000003,
            "vat_rate_percent" => 20
        ]));
        $this->assertEquals($orderCalc->getAmbassadorApportionBreakdown(), new OrderApportion([
            "sku" => "ADISC",
            "gross" => 250.0,
            "value" => 250.0,
            "net" => 208.33333333333,
            "vat" => 41.666666666667,
            "vat_rate_percent" => 20
        ]));
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function order_with_hostess_line_test()
    {
        $this->markTestSkipped(
            'Orders should now only have either normal order lines, or hostess lines exclusively'
        );

        $preCalcOrder = $this->generate_pre_calc_ambassador_app_with_hostess_lines();
        $this->assertEquals($preCalcOrder->getOrderId(), 1);
        $this->assertEquals($preCalcOrder->getNote(), 'Test note2');

        $orderCalc = new OrderCalcDefault($preCalcOrder);

        $this->assertEquals($orderCalc->getValueTotal(), 1880.0);
        $this->assertEquals($orderCalc->getHostessAllowance(), 277.5);
        $this->assertEquals($orderCalc->getHostessAllowanceWithoutHostessLines(), 150.0);
        $this->assertEquals($orderCalc->getQvTotal(), 1850.0);
        $this->assertEquals($orderCalc->getTaxTotal(), 313.3333333333335);
        $this->assertEquals($orderCalc->getApportionBreakdown(), new OrderApportion([
            "sku" => OrderApportion::_PORTION_SKU,
            "gross" => 1880.0,
            "value" => 1880.0,
            "net" => 1566.6666666666665,
            "vat" => 313.3333333333335,
            "vat_rate_percent" => 20
        ]));
        $this->assertEquals($orderCalc->getAmbassadorApportionBreakdown(), new OrderApportion([
            "sku" => "ADISC",
            "gross" => 462.5,
            "value" => 462.5,
            "net" => 385.41666666666663,
            "vat" => 77.08333333333337,
            "vat_rate_percent" => 20
        ]));
        $this->assertTrue(true);
    }

    /**
     * @test
     * @dataProvider calculate_hostess_percent_of_gross_provider
     * @param $expected
     * @param $hostessAllowanceVat
     * @throws \Exception
     */
    public function calculate_hostess_percent_of_gross_test($expected, $hostessAllowanceVat)
    {

        $orderCalc = new OrderCalcDefault(
            $this->generate_pre_calc_ambassador_app_with_hostess_lines()
        );
        $result = $orderCalc->calculateHostessPercentOfGross($hostessAllowanceVat);
        $this->assertEquals($expected, $result);
    }

    public function calculate_hostess_percent_of_gross_provider()
    {
        return [
            [20, 100],
            [0, 0]
        ];
    }

    /**
     * @test
     * @dataProvider calculate_hostess_percent_of_vat_provider
     * @param float $hostessAllowanceVat
     * @throws \Exception
     */
    public function calculate_hostess_percent_of_vat_test($expected, $hostessAllowanceVat)
    {
        $orderCalc = new OrderCalcDefault(
            $this->generate_pre_calc_ambassador_app_with_hostess_lines()
        );
        $result = $orderCalc->calculateHostessPercentOfVat($hostessAllowanceVat);
        $this->assertEquals($expected, $result);
    }

    public function calculate_hostess_percent_of_vat_provider()
    {
        return [
            [20, 100],
            [0, 0]
        ];
    }

    /**
     * @return PreCalcAmbassadorAppOrder
     */
    public function generate_pre_calc_ambassador_app_with_hostess_lines(): PreCalcAmbassadorAppOrder
    {
        $hostessLineObjects = [
            // Made up line, this is not an existing variant, just using it for simple maths
            new LineItemHostess([
                'shopify_variant_id' => 45164641620,
                'sku' => 'ABCDEFG',
                'gross' => 1000,
                'quantity' => 1,
                'qv' => true,
                'bv' => false, //Business Value = QV only in order line objects
                'vat' => 166.6666666666667,
                'status_id' => StatusOrder::STATUS_DRAFT_ID,
                'weight' => 623
            ])
        ];
        $orderLineObjects = [
            // Made up line, this is not an existing variant, just using it for simple maths
            new LineItemProductVariant([
                'shopify_variant_id' => 45164641620,
                'sku' => 'ABCDEFG',
                'gross' => 1000,
                'quantity' => 1,
                'qv' => true,
                'bv' => true, //Business Value = QV only in order line objects
                'vat' => 166.6666666666667,
                'status_id' => StatusOrder::STATUS_DRAFT_ID,
                'weight' => 623
            ])
        ];
        return $this->instance(PreCalcAmbassadorAppOrder::class,
            Mockery::mock(PreCalcAmbassadorAppOrder::class,
                function ($mock) use ($orderLineObjects, $hostessLineObjects) {
                    $mock->shouldReceive('getOrderId')->andReturn(1);
                    $mock->shouldReceive('getNote')->andReturn('Test note2');
                    $mock->shouldReceive('getSource')->andReturn('ambassador-app');
                    $mock->shouldReceive('getSalesChannelId')->andReturn(1);
                    $mock->shouldReceive('getAmbassadorId')->andReturn(1023);
                    $mock->shouldReceive('getVoucherCode')->andReturn('');
                    $mock->shouldReceive('getHostessLineObjects')->andReturn($hostessLineObjects);
                    $mock->shouldReceive('getOrderLineObjects')->andReturn($orderLineObjects);
                    $mock->shouldReceive('getDiscountLineObjects')->andReturn([]);
                    $mock->shouldReceive('getDeliveryLineObjects')->andReturn([]);
                    $mock->shouldReceive('getOrderStatus')->andReturn(StatusOrder::STATUS_DRAFT_ID);
                    $mock->shouldReceive('getCustomer')->andReturn(false);
                    $mock->shouldReceive('getBillingAddress')->andReturn(false);
                    $mock->shouldReceive('getDeliveryAddress')->andReturn(false);
                    $mock->shouldReceive('getShopifyOrderId')->andReturn(false);
                    $mock->shouldReceive('getPamperId')->andReturn(1);
                }));
    }

    public function generate_pre_calc_ambassador_app(): PreCalcAmbassadorAppOrder
    {
        $orderLineObjects = [
            // Made up line, this is not an existing variant, just using it for simple maths
            new LineItemProductVariant([
                'shopify_variant_id' => 45164641620,
                'sku' => 'ABCDEFG',
                'gross' => 1000,
                'quantity' => 1,
                'qv' => true,
                'bv' => true, //Business Value = QV only in order line objects
                'vat' => 166.6666666666667,
                'status_id' => StatusOrder::STATUS_DRAFT_ID,
                'weight' => 623
            ])
        ];
        return $this->instance(PreCalcAmbassadorAppOrder::class,
            Mockery::mock(PreCalcAmbassadorAppOrder::class, function ($mock) use ($orderLineObjects) {
                $mock->shouldReceive('getOrderId')->andReturn(1);
                $mock->shouldReceive('getNote')->andReturn('Test note');
                $mock->shouldReceive('getSource')->andReturn('ambassador-app');
                $mock->shouldReceive('getSalesChannelId')->andReturn(1);
                $mock->shouldReceive('getOrderTypeId')->andReturn(1);
                $mock->shouldReceive('getAmbassadorId')->andReturn(1023);
                $mock->shouldReceive('getVoucherCode')->andReturn('');
                $mock->shouldReceive('getHostessLineObjects')->andReturn([]);
                $mock->shouldReceive('getOrderLineObjects')->andReturn($orderLineObjects);
                $mock->shouldReceive('getDiscountLineObjects')->andReturn([]);
                $mock->shouldReceive('getDeliveryLineObjects')->andReturn([]);
                $mock->shouldReceive('getOrderStatus')->andReturn(StatusOrder::STATUS_DRAFT_ID);
                $mock->shouldReceive('getCustomer')->andReturn(false);
                $mock->shouldReceive('getBillingAddress')->andReturn(false);
                $mock->shouldReceive('getDeliveryAddress')->andReturn(false);
                $mock->shouldReceive('getShopifyOrderId')->andReturn(false);
                $mock->shouldReceive('getPamperId')->andReturn(1);
            }));
    }
}
