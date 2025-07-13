<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        return Response::json([
            'status' => 'success',
            'data' => Conversation::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Conversation $conversation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Conversation $conversation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conversation $conversation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conversation $conversation)
    {
        //
    }

    /**
     * get conversation between two users
     */
    public function startChat($user_id)
    {
        $conversation = Conversation::where(['receiver' => $user_id, 'sender' => Auth::user()->id])
            ->orWhere(['receiver' => Auth::user()->id, 'sender' => $user_id])
            ->firstOrCreate([
                'receiver' => $user_id,
                'sender' => Auth::user()->id
            ]);
        return Response::json([
            'status' => 'success',
            'data' => $conversation
        ]);
    }
}
