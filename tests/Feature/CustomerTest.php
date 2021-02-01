<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Ambassador;
use App\Models\AmbassadorLegalStatus;
use App\Models\Country;
use App\Models\Pamper;
use App\Models\PamperType;
use App\Models\Customer;
use App\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\ResetDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use WithFaker, ResetDatabase;

    private static Country $country;
    private static string $customerId;

    private Pamper $pamper;
    private Ambassador $ambassador;

    protected function setUp(): void
    {
        parent::setUp();
        static::$country = Country::where('name', 'Poland')->first();
    }

    /** @test */
    public function get_422_error_when_validation_error()
    {
        $input = [
            'email' => 'george@email.com',
        ];

        $response = $this->json('POST', '/customers', $input);
        $response->assertStatus(422);
        $response->assertJsonFragment([
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        );
    }

    /** @test */
    public function create_customer()
    {
        $input = [
            'email' => 'george@email.com',
            'title' => 'Mr',
            'first_name' => 'George',
            'last_name' => 'Smith',
            'DOB' => '1990-01-01',
            'mobile' => '+44 7720845124',
            'exigo_id' => 123,
            'home_address' => [
                'address_line1' => 'Home Address Line 1',
                'address_line2' => 'Home Address Line 2',
                'address_line3' => 'Home Address Line 3',
                'town' => 'Home Town',
                'postcode' => 'SE10 7T5',
                'county' => 'Home County',
                'country_id' => static::$country->id,
            ],
            'delivery_address' => [
                'address_line1' => 'Delivery Address Line 1',
                'address_line2' => 'Delivery Address Line 2',
                'address_line3' => 'Delivery Address Line 3',
                'town' => 'Delivery Town',
                'postcode' => 'SE10 7T5',
                'county' => 'Delivery County',
                'country_id' => static::$country->id,
            ],
        ];

        $response = $this->json('POST', '/customers', $input);
        $response->assertCreated();
        $customer = $response->getOriginalContent();
        static::$customerId = $customer->id;
    }

    /** @test */
    public function get_customer_details()
    {
        $response = $this->get(sprintf('/customers/%s/', static::$customerId));
        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => static::$customerId,
                'title' => 'Mr',
                'first_name' => 'George',
                'last_name' => 'Smith',
                'home_address' => [
                    'address_line1' => 'Home Address Line 1',
                    'address_line2' => 'Home Address Line 2',
                    'address_line3' => 'Home Address Line 3',
                    'town' => 'Home Town',
                    'postcode' => 'SE10 7T5',
                    'county' => 'Home County',
                    'country' => static::$country->name,
                ],
                'delivery_address' => [
                    'address_line1' => 'Delivery Address Line 1',
                    'address_line2' => 'Delivery Address Line 2',
                    'address_line3' => 'Delivery Address Line 3',
                    'town' => 'Delivery Town',
                    'postcode' => 'SE10 7T5',
                    'county' => 'Delivery County',
                    'country' => static::$country->name,
                ],
                'DOB' => '1990-01-01',
                'mobile' => '+44 7720845124',
                'picture' => '/noimage.png',
                'initial' => 'G',
                '_links' => []
            ]
        ]);
    }

    /** @test */
    public function edit_user_details()
    {
        $input = [
            'email' => 'george.smith@email.com',
            'delivery_address' => [
                'address_line1' => '1 Westminster',
                'address_line2' => 'Delivery Address Line 2',
                'address_line3' => 'Delivery Address Line 3',
                'town' => 'London',
                'postcode' => 'SE10 7T5',
                'county' => 'Greater London',
                'country_id' => static::$country->id,
            ],
        ];

        $response = $this->put(sprintf('/customers/%s/', static::$customerId), $input);
        $response->assertStatus(200);
        $customer = Customer::find(static::$customerId);
        $this->assertEquals('george.smith@email.com', $customer->email);
        $this->assertEquals('1 Westminster', $customer->deliveryAddress()->first()->address_line1);
        $this->assertEquals('Greater London', $customer->deliveryAddress()->first()->county);
    }

    /** @test */
    public function get_customer_without_the_address()
    {
        $input = [
            'title' => 'Mr',
            'first_name' => 'George',
            'last_name' => 'Smith',
            'DOB' => '1990-01-01',
            'mobile' => '+44 7720845124',
            'exigo_id' => 123,
        ];

        $customer = factory(Customer::class)->create($input);

        $response = $this->get(sprintf('/customers/%s/', $customer->id));
        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => $customer->id,
                'title' => 'Mr',
                'first_name' => 'George',
                'last_name' => 'Smith',
                'home_address' => [],
                'delivery_address' => [],
                'mobile' => '+44 7720845124',
                'exigo_id' => 123,
                'picture' => '/noimage.png',
                'initial' => 'G',
                '_links' => []
            ]
        ]);
    }

    /** @test */
    public function customer_got_the_same_home_and_delivery_address()
    {
        $address = factory(Address::class)->create([
            'address_line1' => 'Home Address Line 1',
            'address_line2' => 'Home Address Line 2',
            'address_line3' => 'Home Address Line 3',
            'town' => 'Home Town',
            'postcode' => 'SE10 7T5',
            'county' => 'Home County',
            'country_id' => static::$country->id,
        ]);

        $input = [
            'title' => 'Mr',
            'first_name' => 'George',
            'last_name' => 'Smith',
            'home_address' => $address,
            'delivery_address' => $address,
            'DOB' => '1990-01-01',
            'mobile' => '+44 7720845124',
            'exigo_id' => 123,
        ];

        $customer = factory(Customer::class)->create($input);

        $response = $this->get(sprintf('/customers/%s/', $customer->id));
        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => $customer->id,
                'title' => 'Mr',
                'first_name' => 'George',
                'last_name' => 'Smith',
                'home_address' => [
                    'address_line1' => 'Home Address Line 1',
                    'address_line2' => 'Home Address Line 2',
                    'address_line3' => 'Home Address Line 3',
                    'town' => 'Home Town',
                    'postcode' => 'SE10 7T5',
                    'county' => 'Home County',
                    'country' => static::$country->name,
                ],
                'delivery_address' => [
                    'address_line1' => 'Home Address Line 1',
                    'address_line2' => 'Home Address Line 2',
                    'address_line3' => 'Home Address Line 3',
                    'town' => 'Home Town',
                    'postcode' => 'SE10 7T5',
                    'county' => 'Home County',
                    'country' => static::$country->name,
                ],
                'DOB' => '1990-01-01',
                'mobile' => '+44 7720845124',
                'exigo_id' => 123,
                'picture' => '/noimage.png',
                'initial' => 'G',
                '_links' => []
            ]
        ]);
    }

    /** @test */
    public function when_model_not_found_then_return_json_404()
    {
        $response = $this->get(sprintf('/customers/%s/', 999));
        $response->assertStatus(404);
        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJson([
            'message' => 'Record not found.'
        ]);
    }

    /** @test */
    public function filter_customers_by_last_name()
    {
        $pamperType = PamperType::find(1);

        $hostess = factory(Customer::class)->create([
            'title' => 'Mr',
            'first_name' => 'Andrew',
            'last_name' => 'Joshua',
            'exigo_id' => 123
        ]);

        $pamper = factory(Pamper::class)->create([
            'hostess_id' => $hostess->id,
            'type_id' => $pamperType
        ]);


        $response = $this->get(sprintf("/customers/?search=%s", 'Joshua'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertCount(1, $response->getOriginalContent());
    }

    /** @test */
    public function filters_customers_by_non_existing_last_name()
    {
        $response = $this->get(sprintf("/customers/?search=%s", 'WatsonASD'));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertCount(0, $response->getOriginalContent());
    }

    /** @test */
    public function get_list_of_the_customers()
    {
        $address = factory(Address::class)->create([
            'address_line1' => 'Home Address Line 1',
            'address_line2' => 'Home Address Line 2',
            'address_line3' => 'Home Address Line 3',
            'town' => 'Home Town',
            'postcode' => 'SE10 7T5',
            'county' => 'Home County',
            'country_id' => static::$country->id,
        ]);

        $input = [
            'title' => 'Mr',
            'first_name' => 'George',
            'last_name' => 'Smith',
            'home_address' => $address,
            'DOB' => '1990-01-01',
            'mobile' => '+44 7720845124',
            'exigo_id' => 123,
        ];

        $customer = factory(Customer::class)->create($input);

        $response = $this->get('/customers');
        $response->assertStatus(200)->assertJsonFragment([
            'id' => $customer->id,
            'title' => 'Mr',
            'first_name' => 'George',
            'last_name' => 'Smith',
            'DOB' => $customer->DOB,
            'mobile' => '+44 7720845124',
            'exigo_id' => 123,
        ]);
    }

    /**
     * @test
     */
    public function get_list_of_the_customers_by_pamper()
    {
        $this->createFixtures();

        // get all customers of the party
        $response = $this->get(sprintf('/customers?pamper_id=%s', $this->pamper->id));
        $response->assertStatus(200);
        $this->assertCount(6, $response->getOriginalContent());
    }

    /**
     * @test
     */
    public function get_list_of_the_customers_by_pamper3()
    {
        $this->createFixtures();

        // get hostess only
        $response = $this->get(sprintf('/customers?pamper_id=%s&role=%d',
            $this->pamper->id, Role::TYPE_HOSTESS));
        $response->assertStatus(200);
        $this->assertCount(1, $response->getOriginalContent());
    }

    /**
     * @test
     */
    public function get_list_of_the_customers_by_date_birth()
    {
        $this->truncateTable(Customer::class);
        factory(Customer::class)->create([
            'DOB' => '1990-02-23',
        ]);
        factory(Customer::class)->create([
            'DOB' => '1990-02-10',
        ]);
        factory(Customer::class)->create([
            'DOB' => '2020-02-12',
        ]);

        // get all customers of the party
        $response = $this->get(sprintf('/customers?birthday_from=%s&birthday_to=%s',
            '02-01', '02-28'));
        $response->assertStatus(200);

        $responseContent = json_decode($response->getContent(), true);
        $this->assertCount(3, $responseContent['data']);
    }

    /**
     * @test
     */
    public function get_active_customers()
    {
        $this->truncateTable(Customer::class);
        factory(Customer::class, 5)->create(['active' => Customer::ACTIVE]);
        $response = $this->get(sprintf('/customers?active=%d', Customer::ACTIVE));
        $this->assertCount(5, $response->getOriginalContent());
    }

    /**
     * @test
     */
    public function get_inactive_customers()
    {
        $this->truncateTable(Customer::class);
        factory(Customer::class, 5)->create(['active' => Customer::INACTIVE]);
        $response = $this->get(sprintf('/customers?active=%d', Customer::INACTIVE));
        $this->assertCount(5, $response->getOriginalContent());
    }

    /**
     * @test
     * todo: complete the test
     */
    public function get_customers_by_ambassador_id()
    {
        $this->createFixtures();

        $this->get(
            sprintf(
                "/customers/?ambassador_id=%s",
                $this->ambassador->id,
            )
        )->assertOk();
    }

    /** @test
     */
    public function filter_customers_by_active_and_last_name()
    {
        $lastName = 'Test Active';
        $otherLastName = 'Test Other Last Name';

        factory(Customer::class, 3)->create([
            'last_name' => $lastName,
            'active' => Customer::ACTIVE
        ]);

        factory(Customer::class, 4)->create([
            'last_name' => $lastName,
            'active' => Customer::INACTIVE
        ]);

        factory(Customer::class, 5)->create([
            'last_name' => $otherLastName,
            'active' => Customer::ACTIVE
        ]);

        factory(Customer::class, 6)->create([
            'last_name' => $otherLastName,
            'active' => Customer::INACTIVE
        ]);

        $response = $this->get(
            sprintf(
                "/customers/?search=%s&active=%d",
                $lastName,
                Customer::ACTIVE
            )
        );
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');
        $this->assertCount(3, $response->getOriginalContent());
    }

    protected function createFixtures(): void
    {
        $legalStatus = AmbassadorLegalStatus::all()->first();
        $this->ambassador = factory(Ambassador::class)->create([
            'title' => 'Mr',
            'first_name' => 'John',
            'last_name' => 'King',
            'legal_status_id' => $legalStatus->id,
            'vat_registered' => 1,
            'accepted_terms_and_conditions' => 1
        ]);
        $hostess = factory(Customer::class)->create();
        $guests = factory(Customer::class, 5)->create([
            'ambassador_id' => $this->ambassador
        ]);

        $pamperType = PamperType::find(1);

        $this->pamper = factory(Pamper::class)->create([
            'type_id' => $pamperType,
            'ambassador_id' => $this->ambassador->id,
            'hostess_id' => $hostess->id
        ]);

        foreach ($guests as $guest) {
            $this->pamper->guests()->attach($guest);
        }
    }
}
