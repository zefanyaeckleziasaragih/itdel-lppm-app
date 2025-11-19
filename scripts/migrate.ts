import path from "path";
import { promises as fs } from "fs";
import sequelize from "../utils/dbUtil";
import models from "../models/_index";
import { BACKUP_FILE_NAME_FOR_MIGRATION } from "../utils/constUtil";

async function migrateDatabase(): Promise<void> {
  try {
    // Koneksi ke database
    await sequelize.authenticate();
    console.log("Koneksi berhasil.");

    // Path backup file
    const backupFilePath = path.join(
      __dirname,
      `backups/${BACKUP_FILE_NAME_FOR_MIGRATION}`
    );

    // Cek apakah file backup ada
    try {
      await fs.access(backupFilePath);
    } catch {
      throw new Error(`File backup tidak ditemukan: ${backupFilePath}`);
    }

    // Membaca data backup dari file JSON
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const backupData: Record<string, any[]> = JSON.parse(
      await fs.readFile(backupFilePath, "utf-8")
    );

    // Menghapus data lama dan memasukkan data dari backup
    for (const [modelName, records] of Object.entries(backupData)) {
      const model = models.find((m) => m.name === modelName);
      if (model) {
        await model.destroy({ where: {} });
        console.log(`Data lama untuk model ${modelName} telah dihapus.`);

        for (const record of records) {
          await model.create(record);
          console.log(
            `Data untuk model ${modelName} telah ditambahkan: ${record.id}`
          );
        }
      } else {
        console.warn(`Model ${modelName} tidak ditemukan.`);
      }
    }

    console.log("Migrasi selesai.");
  } catch (error) {
    console.error("Gagal melakukan migrasi:", error);
  } finally {
    await sequelize.close();
  }
}

// Jalankan migrasi menggunakan IIFE
(async () => {
  await migrateDatabase();
})();
