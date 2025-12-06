<?php

namespace App\Http\Controllers\App\Home;

use App\Helper\ToolsHelper;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $authToken = ToolsHelper::getAuthToken();

        // Check apakah user adalah dosen
        $isDosen = $this->checkIsDosen($auth);

        return Inertia::render('app/home/home-page', [
            'auth' => Inertia::always($auth),
            'pageName' => Inertia::always('Beranda'),
            'authToken' => Inertia::always($authToken),
            'isDosen' => Inertia::always($isDosen),
        ]);
    }

    /**
     * Check apakah user adalah dosen dengan memeriksa di database LPPM
     */
    private function checkIsDosen($auth): bool
    {
        try {
            $dosen = DB::connection('pgsql_lppm')
                ->table('m_dosen')
                ->where('user_id', $auth->id)
                ->first();

            return $dosen !== null;
        } catch (\Exception $e) {
            // Log error jika diperlukan
            return false;
        }
    }
}