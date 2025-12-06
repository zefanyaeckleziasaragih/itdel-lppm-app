namespace App\Http\Controllers\App\Penghargaan;

use App\Http\Controllers\Controller;
use App\Models\Penghargaan;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    // Menampilkan daftar pengajuan penghargaan
    public function index(Request $request)
    {
        $pengajuan = Penghargaan::where('status', 'Menunggu')->get(); // Hanya menampilkan yang statusnya 'Menunggu'
        
        return view('app.penghargaan.daftar-pengajuan', [
            'pengajuan' => $pengajuan
        ]);
    }

    // Menampilkan detail pengajuan penghargaan
    public function show(Request $request, $id)
    {
        $pengajuan = Penghargaan::find($id);
        
        if (!$pengajuan) {
            return redirect()->route('penghargaan.daftar')->with('error', 'Pengajuan tidak ditemukan.');
        }
        
        return view('app.penghargaan.detail-pengajuan', [
            'pengajuan' => $pengajuan
        ]);
    }

    // Menangani konfirmasi pencairan dana
    public function konfirmasi(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'tanggal_pencairan' => 'required|date',
            'dana_disetujui' => 'required|numeric|min:0',
        ]);

        // Temukan pengajuan berdasarkan ID
        $pengajuan = Penghargaan::find($id);
        
        if (!$pengajuan) {
            return redirect()->route('penghargaan.daftar')->with('error', 'Pengajuan tidak ditemukan.');
        }

        // Perbarui pengajuan dengan tanggal pencairan dan dana yang disetujui
        $pengajuan->tanggal_pencairan = $validated['tanggal_pencairan'];
        $pengajuan->dana_disetujui = $validated['dana_disetujui'];
        $pengajuan->status = 'Pencairan Dijadwalkan'; // Update status menjadi 'Pencairan Dijadwalkan'
        $pengajuan->save();

        // Redirect dengan pesan sukses
        return redirect()->route('penghargaan.daftar')->with('success', 'Tanggal pencairan dan dana berhasil diperbarui.');
    }
}
