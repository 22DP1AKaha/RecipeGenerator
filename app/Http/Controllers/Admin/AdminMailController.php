<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminBroadcast;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AdminMailController extends Controller
{
    public function index()
    {
        $users = User::whereNotNull('email_verified_at')
            ->orderBy('vards')
            ->get(['id', 'vards', 'email']);

        return Inertia::render('Admin/Email', [
            'recipients' => $users,
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'subject'         => 'required|string|max:255',
            'body'            => 'required|string|max:5000',
            'recipient_ids'   => 'required|array|min:1',
            'recipient_ids.*' => 'integer|exists:users,id',
        ]);

        $users = User::whereIn('id', $data['recipient_ids'])
            ->whereNotNull('email_verified_at')
            ->get();

        foreach ($users as $user) {
            Mail::to($user->email)
                ->queue(new AdminBroadcast($data['subject'], $data['body']));
        }

        return redirect()->route('admin.email')
            ->with('status', 'email-sent')
            ->with('sent_count', $users->count());
    }
}
