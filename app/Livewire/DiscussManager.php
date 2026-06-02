<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class DiscussManager extends Component
{
    public $activeTab = 'channel'; // channel, direct
    public $activeChannel = '#general';
    public $activeDirectUserId = null;

    public $messageText = '';

    public function selectChannel($channelName)
    {
        $this->activeTab = 'channel';
        $this->activeChannel = $channelName;
        $this->activeDirectUserId = null;
        $this->messageText = '';
    }

    public function selectDirectUser($userId)
    {
        $this->activeTab = 'direct';
        $this->activeDirectUserId = $userId;
        $this->activeChannel = null;
        $this->messageText = '';
    }

    public function sendMessage()
    {
        $this->validate([
            'messageText' => 'required|string|max:1000',
        ]);

        if ($this->activeTab === 'channel') {
            ChatMessage::create([
                'sender_id' => Auth::id(),
                'is_group' => true,
                'channel_name' => $this->activeChannel,
                'message' => $this->messageText,
            ]);
        } else {
            ChatMessage::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $this->activeDirectUserId,
                'is_group' => false,
                'message' => $this->messageText,
            ]);
        }

        $this->messageText = '';

        // Dispatch browser scroll event to keep chat history focused at bottom
        $this->dispatch('chat-updated');
    }

    public function render()
    {
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();

        // Query active messages
        if ($this->activeTab === 'channel') {
            $messages = ChatMessage::with('sender')
                ->where('is_group', true)
                ->where('channel_name', $this->activeChannel)
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            $senderId = Auth::id();
            $receiverId = $this->activeDirectUserId;

            $messages = ChatMessage::with(['sender', 'receiver'])
                ->where(function($q) use ($senderId, $receiverId) {
                    $q->where('sender_id', $senderId)->where('receiver_id', $receiverId);
                })
                ->orWhere(function($q) use ($senderId, $receiverId) {
                    $q->where('sender_id', $receiverId)->where('receiver_id', $senderId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
        }

        $activeDirectUser = $this->activeDirectUserId ? User::find($this->activeDirectUserId) : null;

        return view('livewire.discuss-manager', [
            'users' => $users,
            'messages' => $messages,
            'activeDirectUser' => $activeDirectUser,
        ])->layout('layouts.app');
    }
}
