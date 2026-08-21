<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WhatsappInbox;

class WhatsappInboxController extends Controller
{
    public function index()
    {
        return view('whatsapp_inbox.index');
    }

    public function fetch(Request $request)
    {
        $messages = WhatsappInbox::orderBy('received_at', 'desc')->paginate(20);
        return response()->json($messages);
    }
}
