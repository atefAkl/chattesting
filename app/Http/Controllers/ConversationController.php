<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class ConversationController extends Controller
{
    /**
     * Display a listing of active conversations for the current user.
     */
    public function index()
    {
        $user = Auth::user();
        
        $conversations = Conversation::where('sender', $user->id)
            ->orWhere('receiver', $user->id)
            ->with(['messages' => function($query) {
                $query->latest()->first();
            }])
            ->get();
        
        return Response::json([
            'status' => 'success',
            'data' => $conversations
        ]);
    }

    /**
     * Start or continue a chat with another user
     */
    public function startChat($user_id)
    {
        // Check if user exists
        $otherUser = User::findOrFail($user_id);
        
        $conversation = Conversation::where(function($query) use ($user_id) {
                $query->where('receiver', $user_id)
                      ->where('sender', Auth::id());
            })
            ->orWhere(function($query) use ($user_id) {
                $query->where('receiver', Auth::id())
                      ->where('sender', $user_id);
            })
            ->firstOrCreate([
                'receiver' => $user_id,
                'sender' => Auth::id(),
                'status' => 'active',
                'is_open' => true
            ]);
            
        // Mark all messages as read for the current user
        Message::where('conversation_id', $conversation->id)
            ->where('sender', '!=', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
            
        // Get the first batch of messages (latest 20)
        $messages = Message::where('conversation_id', $conversation->id)
            ->latest()
            ->take(20)
            ->get()
            ->reverse();
            
        return Response::json([
            'status' => 'success',
            'conversation' => $conversation,
            'messages' => $messages,
            'other_user' => $otherUser
        ]);
    }
    
    /**
     * Load more messages for a conversation (pagination)
     */
    public function loadMoreMessages(Request $request, $conversation_id)
    {
        $conversation = Conversation::findOrFail($conversation_id);
        
        // Ensure user is part of this conversation
        if ($conversation->sender != Auth::id() && $conversation->receiver != Auth::id()) {
            return Response::json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $lastMessageId = $request->input('last_message_id');
        
        // Get the next batch of messages before the last loaded message
        $messages = Message::where('conversation_id', $conversation_id)
            ->where('id', '<', $lastMessageId)
            ->latest()
            ->take(20)
            ->get()
            ->reverse();
            
        return Response::json([
            'status' => 'success',
            'messages' => $messages,
            'has_more' => $messages->count() == 20 ? true : false
        ]);
    }
    
    /**
     * Send a new message in a conversation
     */
    public function sendMessage(Request $request, $conversation_id)
    {
        $request->validate([
            'message' => 'required|string'
        ]);
        
        $conversation = Conversation::findOrFail($conversation_id);
        
        // Ensure user is part of this conversation
        if ($conversation->sender != Auth::id() && $conversation->receiver != Auth::id()) {
            return Response::json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        // Determine receiver (the other user in the conversation)
        $receiver_id = ($conversation->sender == Auth::id()) ? $conversation->receiver : $conversation->sender;
        
        // Create new message
        $message = Message::create([
            'conversation_id' => $conversation_id,
            'sender' => Auth::id(),
            'receiver' => $receiver_id,
            'message' => $request->message,
            'is_read' => false
        ]);
        
        // إرسال إشعار للمستلم
        $receiver = User::find($receiver_id);
        if ($receiver) {
            $receiver->notify(new NewMessageNotification($message));
        }
        
        return Response::json([
            'status' => 'success',
            'message' => $message
        ]);
    }
    
    /**
     * Close a chat widget (set is_open to false)
     */
    public function closeChat($conversation_id)
    {
        $conversation = Conversation::findOrFail($conversation_id);
        
        // Ensure user is part of this conversation
        if ($conversation->sender != Auth::id() && $conversation->receiver != Auth::id()) {
            return Response::json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }
        
        $conversation->update([
            'is_open' => false
        ]);
        
        return Response::json([
            'status' => 'success',
            'message' => 'Chat closed successfully'
        ]);
    }
}
