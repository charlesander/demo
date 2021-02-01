<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryCreateRequest;
use App\Http\Requests\CountryUpdateRequest;
use App\Http\Resources\CountryCollection;
use App\Models\Country;
use App\Http\Resources\Country as CountryResource;
use App\Repositories\CountryRepository;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    private CountryRepository $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @OA\Get(
     *     path="/xxxxxxxxxxx",
     *     @OA\Parameter(name="column",in="path",description="Field name that is used for sorting"),
     *     @OA\Parameter(name="dir",in="path",description="Sorting direction"),
     *     @OA\Parameter(name="search",in="path",description="Search value"),
     *     @OA\Parameter(name="length",in="path",description="Numer of records per page"),
     *     @OA\Response(response="200", description="Get a list of countries")
     * )
     */
    public function index(Request $request)
    {
        $length = $request->input('length') ?? 10;
        $sortBy = $request->input('column');
        $orderBy = $request->input('dir');
        $searchValues['search'] = $request->input('search');

        $countries = $this->countryRepository->getPaginatedBy($length, $sortBy, $orderBy, $searchValues);
        return new CountryCollection($countries);
    }

    /**
     * @OA\Get(
     *     path="/xxxxxxxxxxx/{countryId}",
     *     @OA\Parameter(name="id",in="path",description="Country code",required=true),
     *     @OA\Parameter(name="countryId",in="path",description="Id of country",required=true),
     *     @OA\Response(response="200", description="Get an country record")
     * )
     */
    public function one(Country $country)
    {
        return new CountryResource($country);
    }

    /**
     * @OA\Post(
     *     path="/countries",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       required={"id","name"},
     *       @OA\Property(property="id", type="string", example="PL"),
     *       @OA\Property(property="name", type="string", example="Poland"),
     *    ),
     * ),
     * @OA\Response(response="201", description="Create a new country record")
     * )
     */
    public function create(CountryCreateRequest $request)
    {
        $values = $request->validated();

        return new CountryResource(Country::create($values));
    }

    /**
     * @OA\Put(
     *     path="/countries/{countryId}",
     * @OA\RequestBody(
     *    required=true,
     *    @OA\JsonContent(
     *       @OA\Property(property="id", type="string", example="PL"),
     *       @OA\Property(property="name", type="string", example="Poland"),
     *    ),
     * ),
     * @OA\Response(response="200", description="Update an country record")
     * )
     */
    public function update(Country $country, CountryUpdateRequest $request)
    {
        $values = $request->validated();

        $country->update($values);

        return new CountryResource($country);
    }

    /**
     * @OA\Delete(
     *     path="/countries/{countryId}",
     *     @OA\Parameter(name="countryId",in="path",description="Id of country",required=true),
     *     @OA\Response(response="204", description="Delete an country record")
     * )
     */
    public function delete(Country $country)
    {
        $country->delete();
        return response([], 204);
    }
}
