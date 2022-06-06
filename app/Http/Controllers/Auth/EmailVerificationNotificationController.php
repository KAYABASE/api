<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!$request->user() instanceof MustVerifyEmail) {
            return $this->success(message: 'email_verification_not_required');
        }

        if ($request->user()->hasVerifiedEmail()) {
            return $this->error('Your email address has already been verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return $this->success(message: 'A fresh verification link has been sent to your email address.');
    }
}
