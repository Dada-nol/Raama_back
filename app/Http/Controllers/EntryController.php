<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Souvenir;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntryController extends Controller
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
    public function store(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $souvenir = $user->souvenirs()->with('users')->findOrFail($id);

        $request->validate([
            'image_path' => 'required|file|mimes:png,jpg,jpeg|max:5120',
            'caption' => 'nullable|string',
        ]);

        // Règle : un seul upload par jour
        $alreadyUploaded = Entry::where('user_id', $user->id)
            ->where('souvenir_id', $souvenir->id)
            ->whereDate('created_at', now()->toDateString())
            ->exists();

        if ($alreadyUploaded) {
            return response()->json([
                'message' => 'Vous avez déjà uploadé une image aujourd\'hui.'
            ], 403);
        }

        $path = $request->file('image_path')->store('souvenirs/entries', 'public');

        $entry = Entry::create([
            'user_id' => $user->id,
            'souvenir_id' => $souvenir->id,
            'image_path' => $path,
            'caption' => $request->caption,
        ]);

        return response()->json($entry, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
