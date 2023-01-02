<?php

namespace App\Http\Controllers\API;

use App\Models\Announcement;
use App\Models\Logs;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Validator;


class AnnouncementController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $announcements = Announcement::select('id','detail')->orderBy('id', 'DESC')->get();
        return $this->sendResponse($announcements, 'Record retrieved successfully.');
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
        $request->validate([
            'detail' => 'required|max:80'
        ]);

        $input = $request->all();
        Announcement::create($input);

        $announcements = Announcement::select('id','detail')->orderBy('id', 'DESC')->get();
        return $this->sendResponse($announcements, 'Record has been added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function show(Announcement $announcement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function edit(Announcement $announcement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Announcement $announcement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Announcement  $announcement
     * @return \Illuminate\Http\Response
     */
    public function destroy(Announcement $announcement)
    {
        $announcement = Announcement::find($announcement->id);

        $announcement->delete();
        $data = Announcement::select('id','detail')->orderBy('id', 'DESC')->get();
        return $this->sendResponse($data, 'Record has been deleted successfully.');
    }
}
