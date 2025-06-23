<?php

namespace App\Http\Controllers;

use App\Models\Souvenir;
use App\Models\SouvenirUser;
use Illuminate\Http\Request;

class SouvenirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description',
            'cover_image',
        ]);

        $souvenir = Souvenir::create([
            'name' => $request->name,
            'description' => $request->description,
            'cover_image' => $request->cover_image,
            'is_closed' => false
        ]);

        SouvenirUser::create([
            'souvenir_id' => '1',
            'user_id' => '1',
            'role' => 'admin',
        ]);

        return response()->json($souvenir, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Souvenir $souvenir)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Souvenir $souvenir)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Souvenir $souvenir)
    {
        //
    }
}
