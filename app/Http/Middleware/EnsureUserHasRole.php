<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login')
                ->with('error', 'Silahkan Login terlebih dahulu.')->withInput();
        }

        $user = Auth::user();
        if ($user && $user->role && in_array(strtolower($user->role->name), array_map('strtolower', $roles))) {
            return $next($request);
        }

        return redirect()->route('landing')
            ->with('error', 'Anda tidak memiliki izin memasuki Halaman ini.')->withInput();;
    }
}
