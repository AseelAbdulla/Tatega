<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{

    /**
     * Display all addresses.
     */
    public function index()
    {
        $addresses = Address::with('user:id,name,email')->get();

        return response()->json([
            'status' => true,
            'data' => $addresses
        ]);
    }



    /**
     * Store new address.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'user_id' => 'required|exists:users,id',

            'country' => 'required|string|max:100',

            'city' => 'required|string|max:100',

            'region' => 'required|string|max:100',

            'street' => 'required|string|max:255',

            'building' => 'required|string|max:100',

            'notes' => 'nullable|string'

        ]);


        if ($validator->fails()) {

            return response()->json([
                'errors' => $validator->errors()
            ],422);

        }



        $address = Address::create([

            'user_id' => $request->user_id,

            'country' => $request->country,

            'city' => $request->city,

            'region' => $request->region,

            'street' => $request->street,

            'building' => $request->building,

            'notes' => $request->notes

        ]);



        return response()->json([

            'message' => 'Address created successfully',

            'data' => $address

        ],201);

    }




    /**
     * Display specific address.
     */
    public function show(string $id)
    {

        $address = Address::with('user:id,name,email')
                    ->find($id);


        if(!$address){

            return response()->json([
                'message'=>'Address not found'
            ],404);

        }


        return response()->json([

            'status'=>true,

            'data'=>$address

        ]);

    }





    /**
     * Update address.
     */
    public function update(Request $request, string $id)
    {

        $address = Address::find($id);


        if(!$address){

            return response()->json([
                'message'=>'Address not found'
            ],404);

        }



        $validator = Validator::make($request->all(), [

            'country' => 'nullable|string|max:100',

            'city' => 'nullable|string|max:100',

            'region' => 'nullable|string|max:100',

            'street' => 'nullable|string|max:255',

            'building' => 'nullable|string|max:100',

            'notes' => 'nullable|string'

        ]);



        if($validator->fails()){

            return response()->json([
                'errors'=>$validator->errors()
            ],422);

        }



        $address->update([

            'country'=>$request->country ?? $address->country,

            'city'=>$request->city ?? $address->city,

            'region'=>$request->region ?? $address->region,

            'street'=>$request->street ?? $address->street,

            'building'=>$request->building ?? $address->building,

            'notes'=>$request->notes ?? $address->notes

        ]);



        return response()->json([

            'message'=>'Address updated successfully',

            'data'=>$address

        ]);

    }




    /**
     * Delete address.
     */
    public function destroy(string $id)
    {

        $address = Address::find($id);


        if(!$address){

            return response()->json([
                'message'=>'Address not found'
            ],404);

        }


        $address->delete();


        return response()->json([

            'message'=>'Address deleted successfully'

        ]);

    }

}
