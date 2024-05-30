<?php

namespace App\Http\Controllers;

use App\Models\DeweyClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeweyClassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\DeweyClass $deweyClass
     * @return \Illuminate\Http\Response
     */
    public function show(DeweyClass $deweyClass)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\DeweyClass $deweyClass
     * @return \Illuminate\Http\Response
     */
    public function edit(DeweyClass $deweyClass)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\DeweyClass $deweyClass
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeweyClass $deweyClass)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\DeweyClass $deweyClass
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeweyClass $deweyClass)
    {
        //
    }


    public function apiShow($deweyClass)
    {
        return response()->json(DeweyClass::where('ddc', 'like', $deweyClass . '%')->orderBy('ddc')->get());
    }

    public function apiTree()
    {
        return response()->json(DeweyClass::getTree());
    }

    public function apiFlat()
    {
        if (Storage::exists('dewey_flat.json')) {
            $flat = json_decode(Storage::get('dewey_flat.json'));
        } else {
            $flat = DeweyClass::all();
            Storage::put('dewey_flat.json', json_encode($flat));
        }
        return response()->json($flat);
    }

    public function apiSearch($q)
    {
        $deweyClasses = DeweyClass::where('ddc','like', $q.'%')
            ->orWhere('title', 'like', '%'.$q.'%')
            ->orWhere('description', 'like', '%'.$q.'%')
            ->orderBy('ddc')
            ->get();
        return response()->json($deweyClasses);
    }
}
