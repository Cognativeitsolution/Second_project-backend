<?php

namespace App\Http\Controllers\API;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;

class CountriesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::select('id', 'name', 'code', 'status')->orderBy('id', 'DESC')->get();        

        if ($countries) {
            return $this->sendResponse($countries, 'Records retrieved successfully.');
        }

        else {
            return $this->sendError('Records are not available.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $details = $request->validate([
            'name' => 'required|string|unique:countries,name|min:3|max:50',
            'code' => 'required|string|unique:countries,code|min:2|max:5'
        ]);

        Country::create($details);

        $countries = Country::select('id', 'name', 'code', 'status')->orderBy('id', 'DESC')->get(); 

        return $this->sendResponse($countries, 'Record has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = Country::select('id', 'name', 'code', 'status')->where('id', $id)->first();

        if ($country) {
            return $this->sendResponse($country, 'Record retrieved successfully.');
        }

        else {
            return $this->sendError('Record is not available.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $country = Country::select('id', 'name', 'code', 'status')->where('id', $id)->first();

        if ($country) {
            return $this->sendResponse($country, 'Record retrieved successfully.');
        }

        else {
            return $this->sendError('Record is not available.');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $country = Country::select('id', 'name', 'code', 'status')->where('id', $id)->first();

        if (!$country) {
            return $this->sendError('Record is not available.');
        }

        $details = $request->validate([
            'name' => 'required|string|unique:countries,name,' . $id . '|min:3|max:50',
            'code' => 'required|string|unique:countries,code,' . $id . '|min:2|max:5'
        ]);

        $country->update($details);

        $countries = Country::select('id', 'name', 'code', 'status')->orderBy('id', 'DESC')->get(); 

        return $this->sendResponse($countries, 'Record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $country = Country::find($id);

        $country->delete();

        $countries = Country::select('id', 'name', 'code')->get();

        return $this->sendResponse($countries, 'Record deleted successfully.');
    }

    public function showCountries() {
        $countries = Country::select('id', 'name', 'code')->get();

        if ($countries) {
            return $this->sendResponse($countries, 'Records retrieved successfully.');
        }

        else {
            return $this->sendError('Records are not available.');
        }
    }

}
