import db from "../utils/dbUtil";
import "../models/_index";

async function syncDB(): Promise<void> {
  try {
    console.log("Sedang melakukan sinkronisasi data...");
    // Sinkronisasi database
    await db.sync({ alter: true });
    console.log("Berhasil melakukan sinkronisasi database.");
  } catch (error) {
    console.error("Gagal melakukan sinkronisasi database: ", error);
  } finally {
    // Tutup koneksi agar Node.js bisa exit
    await db.close();
  }
}

(async () => {
  await syncDB();
})();
