<?php

namespace App\Http\Controllers\API;

use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;

class StatesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $states = State::select('states.id', 'states.name', 'countries.name AS country', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->orderBy('states.id', 'DESC')
                    ->get();

        if ($states) {
            return $this->sendResponse($states, 'Records retrieved successfully.');
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
            'name' => 'required|string|unique:states,name|min:3|max:50',
            'country_id' => 'required|numeric'
        ]);

        State::create($details);

        $states = State::select('states.id', 'states.name', 'countries.name AS country', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->orderBy('states.id', 'DESC')
                    ->get();

        return $this->sendResponse($states, 'Record has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $state = State::select('states.id', 'states.name', 'countries.name AS country', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->where('states.id', $id)->first();

        if ($state) {
            return $this->sendResponse($state, 'Record retrieved successfully.');
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
        $state = State::select('states.id', 'states.name', 'countries.name AS country', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->where('states.id', $id)->first();

        $countries = Country::select('id', 'name', 'code', 'status')->orderBy('id', 'DESC')->get();

        $data['state'] = $state;
        $data['countries'] = $countries;

        if ($state) {
            return $this->sendResponse($data, 'Record retrieved successfully.');
        }

        else {
            return $this->sendError('Record with this id is not available.');
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
        $state = State::select('id', 'name', 'country_id', 'status')->where('id', $id)->first();

        if (!$state) {
            return $this->sendError('Record with this id is not available.');
        }

        $details = $request->validate([
            'name' => 'required|string|unique:countries,name,' . $id . '|min:3|max:50',
        ]);

        $details['country_id'] = $request->country_id == '' ? $state->country_id : $request->country_id;
        $details['status'] = $request->status == '' ? $state->status : $request->status;

        $state->update($details);

        $states = State::select('states.id', 'states.name', 'countries.name AS country', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->orderBy('states.id', 'DESC')
                    ->get();

        return $this->sendResponse($states, 'Record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $state = State::find($id);

        $state->delete();

        $states = State::select('states.id', 'states.name', 'countries.name AS country', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->orderBy('states.id', 'DESC')
                    ->get();

        return $this->sendResponse($states, 'Record deleted successfully.');
    }

    public function showStates($country_id) {
        $states = State::select('id', 'name')->where('country_id', $country_id)->get();

        if ($states) {
            return response()->json([
                'success' => true,
                'data' => $states,
                'message' => 'Records retrieved successfully!'
            ]);
        }

        else {
            return response()->json([
                'success' => false,
                'data' => $states,
                'message' => 'Records are not available!'
            ]);
        }
    }
}
