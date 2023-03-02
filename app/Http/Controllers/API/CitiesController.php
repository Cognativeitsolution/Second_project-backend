<?php

namespace App\Http\Controllers\API;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;

class CitiesController extends BaseController
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
            $cities = City::select('cities.id', 'cities.name', 'states.name AS state_name', 'countries.name AS country_name', 'cities.status')
            ->join('states', 'cities.state_id', 'states.id')
            ->join('countries', 'states.country_id', 'countries.id')
            ->where('cities.name', 'like', '%' . $search . '%')
            ->orWhere('states.name', 'like', '%' . $search . '%')
            ->orWhere('countries.name', 'like', '%' . $search . '%')
            ->orderBy('cities.id', 'DESC')
            ->paginate(5);
        }

        else {
            $cities = City::select('cities.id', 'cities.name', 'states.name AS state_name', 'countries.name AS country_name', 'cities.status')
            ->join('states', 'cities.state_id', 'states.id')
            ->join('countries', 'states.country_id', 'countries.id')
            ->orderBy('cities.id', 'DESC')
            ->paginate(10);
        }        

        if ($cities) {
            return $this->sendResponse($cities, 'Records retrieved successfully.');
        } else {
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
        $states = State::select('states.id', 'states.name')->orderBy('states.name', 'ASC')->get();

        $data['states'] = $states;

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
            'state_id' => 'required|numeric'
        ]);

        City::create($details);

        $cities = City::select('cities.id', 'cities.name', 'states.name AS state_name', 'countries.name AS country_name', 'cities.status')
            ->join('states', 'cities.state_id', 'states.id')
            ->join('countries', 'states.country_id', 'countries.id')
            ->orderBy('cities.id', 'DESC')
            ->paginate(10);

        return $this->sendResponse($cities, 'Record has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $city = City::select('cities.id', 'cities.name', 'states.name AS state_name', 'countries.name AS country_name', 'cities.status')
            ->join('states', 'cities.state_id', 'states.id')
            ->join('countries', 'states.country_id', 'countries.id')
            ->where('cities.id', $id)
            ->first();

        if ($city) {
            return $this->sendResponse($city, 'Record retrieved successfully.');
        } else {
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
        $city = City::select('cities.id', 'cities.name', 'states.name AS state_name', 'cities.state_id', 'cities.status')
            ->join('states', 'cities.state_id', 'states.id')
            ->where('cities.id', $id)->first();

        $states = State::select('states.id', 'states.name')->orderBy('states.name', 'ASC')->get();

        $data['city'] = $city;
        $data['states'] = $states;

        if ($city) {
            return $this->sendResponse($data, 'Record retrieved successfully.');
        } else {
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
        $city = City::select('cities.id', 'cities.name', 'cities.state_id', 'cities.status')->where('cities.id', $id)->first();

        if (!$city) {
            return $this->sendError('Record with this id is not available.');
        }

        $details = $request->validate([
            'name'      => 'required|string|unique:cities,name,' . $id . '|min:3|max:50',
            'state_id'  => 'required|numeric',
            'status'    => 'required|numeric'
        ]);

        $city->update($details);

        $cities = City::select('cities.id', 'cities.name', 'states.name AS state_name', 'countries.name AS country_name', 'cities.status')
            ->join('states', 'cities.state_id', 'states.id')
            ->join('countries', 'states.country_id', 'countries.id')
            ->orderBy('cities.id', 'DESC')
            ->paginate(10);

        return $this->sendResponse($cities, 'Record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::find($id);

        if ($city) {
            $city->delete();

            $cities = City::select('cities.id', 'cities.name', 'states.name AS state_name', 'countries.name AS country_name', 'cities.status')
            ->join('states', 'cities.state_id', 'states.id')
            ->join('countries', 'states.country_id', 'countries.id')
            ->orderBy('cities.id', 'DESC')
            ->paginate(10);

            return $this->sendResponse($cities, 'Record deleted successfully.');
        } 
        
        else {
            return $this->sendError('Record with this id is not available.');
        }
    }

    public function showCities($state_id)
    {
        $cities = City::select('id', 'name')->where('state_id', $state_id)->get();

        if ($cities) {
            return $this->sendResponse($cities, 'Records retrieved successfully.');
        } else {
            return $this->sendError('Records are not available.');
        }
    }
}
