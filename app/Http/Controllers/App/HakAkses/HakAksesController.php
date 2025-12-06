<?php

namespace App\Http\Controllers\App\HakAkses;

use App\Helper\ConstHelper;
use App\Helper\ToolsHelper;
use App\Http\Api\UserApi;
use App\Http\Controllers\Controller;
use App\Models\HakAksesModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HakAksesController extends Controller
{
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);

        if (! $isEditor) {
            return redirect()->route('home');
        }

        return Inertia::render('app/hak-akses/hak-akses-page', [
            'aksesList' => function () {
                $aksesList = HakAksesModel::all();

                // Ambil data user dari API berdasarkan user_id di tabel hak_akses
                $response = UserApi::postReqUsersByIds(
                    ToolsHelper::getAuthToken(),
                    $aksesList->pluck('user_id')->unique()->toArray(),
                );

                $usersList = [];
                if ($response && isset($response->data->users)) {
                    $usersList = collect($response->data->users)
                        ->map(function ($user) {
                            return (object) $user;
                        })
                        ->all();
                }

                // buat mapping urutan role berdasarkan PRIORITAS
                // contoh: Admin, HRD, Ketua LPPM, Anggota LPPM, Dosen, Todo
                $rolesOrder = array_flip(ConstHelper::OPTION_ROLES);

                foreach ($aksesList as $akses) {
                    // attach object user ke setiap row
                    $akses->user = collect($usersList)->firstWhere('id', $akses->user_id);

                    // pecah string akses -> array
                    $data_akses = $akses->akses
                        ? explode(',', $akses->akses)
                        : [];

                    // urutkan $data_akses berdasarkan urutan di ConstHelper::OPTION_ROLES
                    usort($data_akses, function ($a, $b) use ($rolesOrder) {
                        $wa = $rolesOrder[$a] ?? PHP_INT_MAX;
                        $wb = $rolesOrder[$b] ?? PHP_INT_MAX;

                        return $wa <=> $wb;
                    });

                    $akses->data_akses = $data_akses;
                }

                // urutkan list berdasarkan nama user (ASC)
                return $aksesList
                    ->sortBy(function ($item) {
                        $user = $item->user;

                        return $user ? strtolower($user->name) : '';
                    })
                    ->values();
            },

            'pageName' => Inertia::always('Hak Akses'),
            'auth'     => Inertia::always($auth),
            'isEditor' => Inertia::always(
                in_array('Admin', $auth->akses) || in_array('Admin', $auth->roles)
            ),
            // checkbox di dialog Hak Akses ikut urutan PRIORITAS dari ConstHelper
            'optionRoles' => Inertia::always(ConstHelper::getOptionRoles()),
        ]);
    }

    public function postChange(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);

        if (! $isEditor) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah hak akses.');
        }

        $request->validate([
            'userId'   => 'required',
            'hakAkses' => 'required|array',
        ]);

        // Hapus akses lama
        HakAksesModel::where('user_id', $request->userId)->delete();

        // Simpan hak akses baru (disimpan sebagai string "Role1,Role2,...")
        HakAksesModel::create([
            'id'      => ToolsHelper::generateId(),
            'user_id' => $request->userId,
            'akses'   => implode(',', $request->hakAkses),
        ]);

        return back()->with('success', 'Hak akses berhasil diperbarui.');
    }

    public function postDelete(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);

        if (! $isEditor) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah hak akses.');
        }

        $request->validate([
            'userId' => 'required',
        ]);

        // Hapus akses
        HakAksesModel::where('user_id', $request->userId)->delete();

        return back()->with('success', 'Hak akses pengguna berhasil dihapus.');
    }

    public function postDeleteSelected(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);

        if (! $isEditor) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah hak akses.');
        }

        $request->validate([
            'userIds' => 'required|array',
        ]);

        // Hapus akses
        HakAksesModel::whereIn('user_id', $request->userIds)->delete();

        return back()->with('success', 'Hak akses untuk pengguna yang dipilih berhasil dihapus.');
    }

    private function checkIsEditor($auth): bool
    {
        if (ToolsHelper::checkRoles('Admin', $auth->akses)) {
            return true;
        }

        if (ToolsHelper::checkRoles('Admin', $auth->roles)) {
            return true;
        }

        return false;
    }
}
