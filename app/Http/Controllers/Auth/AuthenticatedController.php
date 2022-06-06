<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthenticatedController extends Controller
{

    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return ApiResponse|string
     * @throws ValidationException
     */
    public function store(LoginRequest $request): ApiResponse|string
    {
        $user = $request->authenticate();

        return $this->success(array_merge([
            'user' => $user,
            'type' => tenant('type')
        ], $user->createToken('Personal Access Token')->toArray()));
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     *
     * @return ApiResponse|string
     */
    public function destroy(Request $request): ApiResponse|string
    {
        $request->user()?->tokens()?->delete();

        return $this->success(message: 'The personal access token has been deleted.');
    }
}
