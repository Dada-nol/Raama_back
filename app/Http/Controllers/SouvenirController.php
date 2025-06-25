<?php

namespace App\Http\Controllers;

use App\Models\Souvenir;
use Illuminate\Http\Request;

class SouvenirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $souvenirs = $user->souvenirs()->with('users')->get();

        if ($souvenirs->isEmpty()) {
            return response()->json(['message' => 'Souvenir introuvable'], 404);
        }

        return response()->json($souvenirs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
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

        $souvenir->users()->attach($user->id, [
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        return response()->json($souvenir, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id, Request $request)
    {
        $user = $request->user();
        $souvenir = $user->souvenirs()->with('users')->find($id);

        if (!$souvenir) {
            return response()->json(['message' => 'Souvenir introuvable'], 404);
        }

        return response()->json($souvenir);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $souvenir = $user->souvenirs()->with('users')->find($id);

        if (!$souvenir) {
            return response()->json(['message' => 'Souvenir introuvable'], 404);
        }

        $request->validate([
            'name',
            'description',
            'cover_image',
            'is_closed'
        ]);

        $souvenir->update([
            'name' => $request->name,
            'description' => $request->description,
            'cover_image' => $request->cover_image,
            'is_closed' => $request->is_closed
        ]);

        return response()->json($souvenir, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Souvenir $souvenir)
    {
        $souvenir->delete();

        return response()->json(['message' => 'souvenir supprimé']);
    }
}
