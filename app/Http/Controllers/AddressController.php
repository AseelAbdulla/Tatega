<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Services\AddressService;


class AddressController extends Controller
{

    protected $addressService;


    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }




    /**
     * Display all addresses
     */
    public function index()
    {

        $addresses = $this->addressService->getAllAddresses();


        return response()->json([

            'status' => true,

            'data' => $addresses

        ]);

    }





    /**
     * Store address
     */
    public function store(StoreAddressRequest $request)
    {

        $address = $this->addressService->createAddress(
            $request->validated()
        );


        return response()->json([

            'message' => 'Address created successfully',

            'data' => $address

        ],201);

    }







    /**
     * Show address
     */
    public function show(string $id)
    {

        $address = $this->addressService->getAddressById($id);



        if(!$address)
        {

            return response()->json([

                'message' => 'Address not found'

            ],404);

        }



        return response()->json([

            'status' => true,

            'data' => $address

        ]);

    }








    /**
     * Update address
     */
    public function update(UpdateAddressRequest $request,string $id)
    {


        $address = $this->addressService->updateAddress(

            $id,

            $request->validated()

        );



        if(!$address)
        {

            return response()->json([

                'message' => 'Address not found'

            ],404);

        }



        return response()->json([

            'message' => 'Address updated successfully',

            'data' => $address

        ]);

    }








    /**
     * Delete address
     */
    public function destroy(string $id)
    {


        $deleted = $this->addressService->deleteAddress($id);



        if(!$deleted)
        {

            return response()->json([

                'message' => 'Address not found'

            ],404);

        }



        return response()->json([

            'message' => 'Address deleted successfully'

        ]);

    }


}
