<?php

namespace App\Http\Controllers\App\Seminar;

use App\Helper\ToolsHelper;
use App\Http\Api\UserApi;
use App\Http\Controllers\Controller;
use App\Models\SeminarModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SeminarController extends Controller
{
    // Halaman Daftar Seminar (untuk dosen melihat seminar mereka)
    public function index(Request $request)
    {
        $auth = $request->attributes->get('auth');

        return Inertia::render('app/seminar/seminar-page', [
            'seminarList' => function () use ($auth) {
                $seminars = SeminarModel::where('user_id', $auth->id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                return $seminars;
            },
            'pageName' => Inertia::always('Daftar Seminar Saya'),
            'auth' => Inertia::always($auth),
            'isDosen' => Inertia::always(true),
        ]);
    }

    // Halaman Admin untuk melihat semua seminar
    public function adminIndex(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);
        
        if (!$isEditor) {
            return redirect()->route('home');
        }

        return Inertia::render('app/seminar/seminar-admin-page', [
            'seminarList' => function () {
                $seminars = SeminarModel::orderBy('created_at', 'desc')->get();

                $response = UserApi::postReqUsersByIds(
                    ToolsHelper::getAuthToken(),
                    $seminars->pluck('user_id')->unique()->toArray(),
                );

                $usersList = [];
                if ($response && isset($response->data->users)) {
                    $usersList = collect($response->data->users)->map(function ($user) {
                        return (object) $user;
                    })->all();
                }

                foreach ($seminars as $seminar) {
                    $seminar->user = collect($usersList)->firstWhere('id', $seminar->user_id);
                }

                return $seminars;
            },
            'pageName' => Inertia::always('Daftar Seminar (Admin)'),
            'auth' => Inertia::always($auth),
            'isEditor' => Inertia::always(true),
        ]);
    }

    // Tambah Seminar
    public function store(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $request->validate([
            'sinta_id' => 'nullable|string',
            'scopus_id' => 'nullable|string',
            'prosiding' => 'nullable|string',
            'nama_forum' => 'required|string',
            'penulis' => 'nullable|string',
            'institusi_penyelenggara' => 'required|string',
            'waktu_pelaksanaan' => 'required|date',
            'tempat_pelaksanaan' => 'required|string',
            'url' => 'nullable|url',
        ]);

        SeminarModel::create([
            'id' => ToolsHelper::generateId(),
            'user_id' => $auth->id,
            'sinta_id' => $request->sinta_id,
            'scopus_id' => $request->scopus_id,
            'prosiding' => $request->prosiding,
            'nama_forum' => $request->nama_forum,
            'penulis' => $request->penulis,
            'institusi_penyelenggara' => $request->institusi_penyelenggara,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
            'tempat_pelaksanaan' => $request->tempat_pelaksanaan,
            'url' => $request->url,
            'status' => 'Belum Dicairkan',
        ]);

        return back()->with('success', 'Seminar berhasil ditambahkan.');
    }

    // Update Seminar
    public function update(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $request->validate([
            'id' => 'required|exists:m_seminar,id',
            'sinta_id' => 'nullable|string',
            'scopus_id' => 'nullable|string',
            'prosiding' => 'nullable|string',
            'nama_forum' => 'required|string',
            'penulis' => 'nullable|string',
            'institusi_penyelenggara' => 'required|string',
            'waktu_pelaksanaan' => 'required|date',
            'tempat_pelaksanaan' => 'required|string',
            'url' => 'nullable|url',
        ]);

        $seminar = SeminarModel::findOrFail($request->id);

        // Pastikan hanya pemilik yang bisa edit
        if ($seminar->user_id !== $auth->id) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah seminar ini.');
        }

        $seminar->update([
            'sinta_id' => $request->sinta_id,
            'scopus_id' => $request->scopus_id,
            'prosiding' => $request->prosiding,
            'nama_forum' => $request->nama_forum,
            'penulis' => $request->penulis,
            'institusi_penyelenggara' => $request->institusi_penyelenggara,
            'waktu_pelaksanaan' => $request->waktu_pelaksanaan,
            'tempat_pelaksanaan' => $request->tempat_pelaksanaan,
            'url' => $request->url,
        ]);

        return back()->with('success', 'Seminar berhasil diperbarui.');
    }

    // Update Status (Admin only)
    public function updateStatus(Request $request)
    {
        $auth = $request->attributes->get('auth');
        $isEditor = $this->checkIsEditor($auth);

        if (!$isEditor) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah status seminar.');
        }

        $request->validate([
            'id' => 'required|exists:m_seminar,id',
            'status' => 'required|in:Belum Dicairkan,Sudah Dicairkan',
        ]);

        $seminar = SeminarModel::findOrFail($request->id);
        $seminar->update(['status' => $request->status]);

        return back()->with('success', 'Status seminar berhasil diperbarui.');
    }

    // Delete Seminar
    public function destroy(Request $request)
    {
        $auth = $request->attributes->get('auth');

        $request->validate([
            'id' => 'required|exists:m_seminar,id',
        ]);

        $seminar = SeminarModel::findOrFail($request->id);

        // Pastikan hanya pemilik yang bisa hapus
        if ($seminar->user_id !== $auth->id) {
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus seminar ini.');
        }

        $seminar->delete();

        return back()->with('success', 'Seminar berhasil dihapus.');
    }

    private function checkIsEditor($auth)
    {
        if (ToolsHelper::checkRoles('Admin', $auth->akses)) {
            return true;
        } elseif (ToolsHelper::checkRoles('Admin', $auth->roles)) {
            return true;
        }

        return false;
    }
}