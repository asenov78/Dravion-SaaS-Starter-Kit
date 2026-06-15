<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index(Request $request)
    {
        return view('api-tokens', [
            'tokens'    => $request->user()->tokens()->latest()->get(),
            'new_token' => session('new_token'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = $request->user()->createToken($data['name']);

        return redirect()->route('api-tokens.index')
            ->with('new_token', $token->plainTextToken);
    }

    public function destroy(Request $request, int $id)
    {
        $token = PersonalAccessToken::findOrFail($id);

        abort_if($token->tokenable_id !== $request->user()->id, 403);

        $token->delete();

        return redirect()->route('api-tokens.index')
            ->with('success', __('flash.token_revoked'));
    }

    public function destroyAll(Request $request)
    {
        $request->user()->tokens()->delete();

        return redirect()->route('api-tokens.index')
            ->with('success', __('flash.tokens_revoked_all'));
    }
}