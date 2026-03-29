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
        $recipientCount = User::whereNotNull('email_verified_at')->count();

        return Inertia::render('Admin/Email', [
            'recipientCount' => $recipientCount,
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string|max:5000',
        ]);

        $users = User::whereNotNull('email_verified_at')->get();

        foreach ($users as $user) {
            Mail::to($user->email)
                ->queue(new AdminBroadcast($data['subject'], $data['body']));
        }

        return redirect()->route('admin.email')
            ->with('status', 'email-sent')
            ->with('sent_count', $users->count());
    }
}
