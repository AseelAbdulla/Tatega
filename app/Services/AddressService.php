<?php

namespace App\Services;

use App\Models\Address;


class AddressService
{


    /**
     * Get all addresses
     */
    public function getAllAddresses()
    {

        return Address::with('user:id,name,email')
            ->get();

    }






    /**
     * Create new address
     */
    public function createAddress(array $data)
    {

        return Address::create([

            'user_id' => $data['user_id'],

            'country' => $data['country'],

            'city' => $data['city'],

            'region' => $data['region'],

            'street' => $data['street'],

            'building' => $data['building'],

            'notes' => $data['notes'] ?? null

        ]);

    }







    /**
     * Get address by id
     */
    public function getAddressById($id)
    {

        return Address::with('user:id,name,email')
            ->find($id);

    }








    /**
     * Update address
     */
    public function updateAddress($id,array $data)
    {

        $address = Address::find($id);



        if(!$address)
        {
            return null;
        }



        $address->update([

            'country' => $data['country'] ?? $address->country,

            'city' => $data['city'] ?? $address->city,

            'region' => $data['region'] ?? $address->region,

            'street' => $data['street'] ?? $address->street,

            'building' => $data['building'] ?? $address->building,

            'notes' => $data['notes'] ?? $address->notes

        ]);



        return $address;

    }








    /**
     * Delete address
     */
    public function deleteAddress($id)
    {

        $address = Address::find($id);



        if(!$address)
        {
            return false;
        }



        $address->delete();



        return true;

    }


}
