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
        $search = request('search');

        if (!empty($search)) {
            $states = State::select('states.id', 'states.name', 'countries.name AS country_name', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->where('states.name', 'like', '%' . $search . '%')
                    ->orWhere('countries.name', 'like', '%' . $search . '%')
                    ->orderBy('states.id', 'DESC')
                    ->paginate(5);
        }

        else {
            $states = State::select('states.id', 'states.name', 'countries.name AS country_name', 'states.status')
            ->join('countries', 'states.country_id', 'countries.id')
            ->orderBy('states.id', 'DESC')
            ->get();
        }        

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
        $countries = Country::select('countries.id', 'countries.name')->orderBy('countries.name', 'ASC')->get();

        $data['countries'] = $countries;

        return $this->sendResponse($data, 'Records retrieved successfully.');
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

        $states = State::select('states.id', 'states.name', 'countries.name AS country_name', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->orderBy('states.id', 'DESC')
                    ->paginate(10);

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
        $state = State::select('states.id', 'states.name', 'countries.name AS country_name', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->where('states.id', $id)->first();

        if ($state) {
            return $this->sendResponse($state, 'Record retrieved successfully.');
        }

        else {
            return $this->sendError('Record with this id is not available.');
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
        $state = State::select('states.id', 'states.name', 'countries.name AS country_name', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->where('states.id', $id)->first();

        $countries = Country::select('countries.id', 'countries.name', 'countries.code', 'countries.status')->orderBy('countries.name', 'ASC')->get();

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
        $state = State::select('states.id', 'states.name', 'states.country_id', 'states.status')->where('states.id', $id)->first();

        if (!$state) {
            return $this->sendError('Record with this id is not available.');
        }

        $details = $request->validate([
            'name'          => 'required|string|unique:countries,name,' . $id . '|min:3|max:50',
            'country_id'    => 'required|numeric',
            'status'        => 'required|numeric'
        ]);

        $state->update($details);

        $states = State::select('states.id', 'states.name', 'countries.name AS country_name', 'states.status')
                    ->join('countries', 'states.country_id', 'countries.id')
                    ->orderBy('states.id', 'DESC')
                    ->paginate(10);

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

        if ($state) {

            $state->delete();

            $states = State::select('states.id', 'states.name', 'countries.name AS country_name', 'states.status')
                        ->join('countries', 'states.country_id', 'countries.id')
                        ->orderBy('states.id', 'DESC')
                        ->paginate(10);
    
            return $this->sendResponse($states, 'Record deleted successfully.');
        }

        else {
            return $this->sendError('Record with this id is not available.');
        }        
    }

    public function showStates($country_id) {
        $states = State::select('states.id', 'states.name')->where('states.country_id', $country_id)->get();

        if ($states) {
            return $this->sendResponse($states, 'Records retrieved successfully.');
        }

        else {
            return $this->sendError('Records are not available.');
        }
    }
}
