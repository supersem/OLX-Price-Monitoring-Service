<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionRequest;
use App\Models\Ad;
use App\Models\Subscription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationEmail;

class SubscriptionController extends Controller
{
    public function subscribe(SubscriptionRequest $subscriptionRequest): \Illuminate\Http\JsonResponse
    {
        $adUrl = $subscriptionRequest->validated()['ad_url'];
        $email = $subscriptionRequest->validated()['email'];

        $ad = Ad::firstOrCreate(['url' => $adUrl]);

        $subscription = Subscription::where('ad_id', $ad->id)
            ->where('email', $email)
            ->first();

        if ($subscription) {
            return response()->json(['message' => 'You are already subscribed. Please check your email to confirm.'], 409);
        }

        $token = Str::random(32);
        $subscription = Subscription::create([
            'ad_id' => $ad->id,
            'email' => $email,
            'verification_token' => $token,
            'status' => 'pending',
        ]);

        Mail::to($email)->send(new VerificationEmail($subscription));

        return response()->json(['message' => 'Subscription created successfully. Please check your email to confirm.'], 201);
    }

    public function confirm($token): \Illuminate\Http\JsonResponse
    {
        $subscription = Subscription::where('verification_token', $token)->first();

        if (!$subscription) {
            return response()->json(['message' => 'Invalid or expired verification token.'], 400);
        }

        if ($subscription->status === 'active') {
            return response()->json(['message' => 'Subscription already confirmed.'], 200);
        }

        $subscription->status = 'active';
        $subscription->save();

        return response()->json(['message' => 'Subscription confirmed successfully.'], 200);
    }
}
