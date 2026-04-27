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
        // Ielasam verificētos lietotājus kā iespējamos saņēmējus
        $users = User::whereNotNull('email_verified_at')
            ->orderBy('vards')
            ->get(['id', 'vards', 'email']);

        return Inertia::render('Admin/Email', [
            'recipients' => $users,
        ]);
    }

    public function send(Request $request)
    {
        // Validējam vēstules datus un saņēmēju sarakstu
        $data = $request->validate([
            'subject'         => 'required|string|max:255',
            'body'            => 'required|string|max:5000',
            'recipient_ids'   => 'required|array|min:1',
            'recipient_ids.*' => 'integer|exists:users,id',
        ]);

        // Ielasam tikai verificētos saņēmējus
        $users = User::whereIn('id', $data['recipient_ids'])
            ->whereNotNull('email_verified_at')
            ->get();

        // Sūtam e-pasta vēstuli katram saņēmējam
        foreach ($users as $user) {
            Mail::to($user->email)
                ->queue(new AdminBroadcast($data['subject'], $data['body']));
        }

        return redirect()->route('admin.email')
            ->with('status', 'email-sent')
            ->with('sent_count', $users->count());
    }
}
