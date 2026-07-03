<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    // Public API: Raise a support ticket
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:30',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $customer = Customer::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'phone' => $request->phone,
            ]
        );

        $ticket = Ticket::create([
            'ticket_number' => 'TCK-' . strtoupper(str()->random(6)),
            'customer_id' => $customer->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'status' => 'open',
            'secure_token' => str()->random(32),
            'replies' => [],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket raised successfully.',
            'ticket' => [
                'ticket_number' => $ticket->ticket_number,
                'subject' => $ticket->subject,
                'status' => $ticket->status,
                'secure_token' => $ticket->secure_token,
                'view_url' => url('/tickets/' . $ticket->secure_token)
            ]
        ], 210);
    }

    // Public API: Request OTP to authenticate customer
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        $customer = Customer::where('email', $request->email)->firstOrFail();

        $otp = (string) rand(100000, 999999);
        $customer->update([
            'otp_code' => Hash::make($otp),
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        // Log the OTP (acting as mail send simulation for debugging and testing)
        Log::info("Support OTP for {$customer->email}: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email.',
            'otp_debug' => $otp // Exposing the OTP in response for direct frontend convenience/test runs
        ]);
    }

    // Public API: Verify OTP and issue customer token
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'otp_code' => 'required|string',
        ]);

        $customer = Customer::where('email', $request->email)->firstOrFail();

        if (!$customer->otp_code || !$customer->otp_expires_at || $customer->otp_expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'OTP has expired or is invalid.'], 422);
        }

        if (!Hash::check($request->otp_code, $customer->otp_code)) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP code.'], 422);
        }

        // Clear OTP
        $customer->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        // Create Sanctum Token
        $token = $customer->createToken('customer-session')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Authenticated successfully.',
            'token' => $token,
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
            ]
        ]);
    }

    // Customer API: View all raised tickets
    public function index(Request $request)
    {
        $customer = $request->user();

        if (!$customer instanceof Customer) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $tickets = Ticket::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'tickets' => $tickets
        ]);
    }

    // Customer API: Reply to a ticket
    public function reply(Request $request, $ticketId)
    {
        $customer = $request->user();

        if (!$customer instanceof Customer) {
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        $ticket = Ticket::where('id', $ticketId)
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        $request->validate([
            'message' => 'required|string',
        ]);

        $replies = $ticket->replies ?: [];
        $replies[] = [
            'sender' => 'customer',
            'author' => $customer->name,
            'message' => $request->message,
            'date' => now()->toDateTimeString()
        ];

        $ticket->update([
            'replies' => $replies,
            'status' => 'open' // Reopen/update status when client replies
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reply posted successfully.',
            'replies' => $replies
        ]);
    }

    // Public API: Fetch a single ticket by secure token
    public function showByToken($token)
    {
        $ticket = Ticket::where('secure_token', $token)->with('customer')->firstOrFail();

        return response()->json([
            'success' => true,
            'ticket' => $ticket
        ]);
    }
}
