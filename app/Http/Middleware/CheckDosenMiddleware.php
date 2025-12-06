<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckDosenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->attributes->get('auth');

        if (!$auth) {
            return redirect()->route('auth.login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        }

        // Cek apakah user adalah dosen di database LPPM
        try {
            $dosen = DB::connection('pgsql_lppm')
                ->table('m_dosen')
                ->where('user_id', $auth->id)
                ->first();

            if (!$dosen) {
                return redirect()->route('home')
                    ->with('error', 'Akses ditolak. Halaman ini hanya untuk dosen yang terdaftar.');
            }

            // Attach data dosen ke request untuk digunakan di controller
            $request->attributes->set('dosen', $dosen);

            return $next($request);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('CheckDosenMiddleware Error: ' . $e->getMessage());
            
            return redirect()->route('home')
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }
}