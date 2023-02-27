<?php

namespace App\Http\Controllers\API;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $countries = Country::select('id', 'name', 'code')->get();

        if ($countries) {
            return response()->json([
                'success' => true,
                'data' => $countries,
                'message' => 'Records retrieved successfully!'
            ]);
        }

        else {
            return response()->json([
                'success' => false,
                'data' => $countries,
                'message' => 'Records are not available!'
            ]);
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

        $country = Country::create($details);

        return response()->json([
            'success' => true,
            'data' => $country,
            'message' => 'Record created successfully!'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = Country::select('id', 'name', 'code')->where('id', $id)->first();

        if ($country) {
            return response()->json([
                'success' => true,
                'data' => $country,
                'message' => 'Record retrieved successfully!'
            ]);
        }

        else {
            return response()->json([
                'success' => false,
                'data' => $country,
                'message' => 'Record is not available!'
            ]);
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
        $country = Country::select('id', 'name', 'code')->where('id', $id)->first();

        if ($country) {
            return response()->json([
                'success' => true,
                'data' => $country,
                'message' => 'Record retrieved successfully!'
            ]);
        }

        else {
            return response()->json([
                'success' => false,
                'data' => $country,
                'message' => 'Record is not available!'
            ]);
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
        $country = Country::select('id', 'name', 'code')->where('id', $id)->first();

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Record with this id is not available!'
            ]);
        }

        $details = $request->validate([
            'name' => 'required|string|unique:countries,name,' . $id . '|min:3|max:50',
            'code' => 'required|string|unique:countries,code,' . $id . '|min:2|max:5'
        ]);

        $country->update($details);

        return response()->json([
            'success' => true,
            'data' => $country,
            'message' => 'Record updated successfully!'
        ]);
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

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully!'
        ]);
    }

    public function showCountries() {
        $countries = Country::select('id', 'name', 'code')->get();

        if ($countries) {
            return response()->json([
                'success' => true,
                'data' => $countries,
                'message' => 'Records retrieved successfully!'
            ]);
        }

        else {
            return response()->json([
                'success' => false,
                'data' => $countries,
                'message' => 'Records are not available!'
            ]);
        }
    }

}
