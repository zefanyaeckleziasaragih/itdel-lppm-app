import fs from "fs";
import path from "path";
import sequelize from "../utils/dbUtil";
import { ensureDirectoryExistence, getTimestamp } from "../utils/toolsUtil";
import models from "../models/_index";
import dotenv from "dotenv";
dotenv.config();

// Fungsi untuk melakukan backup database
async function backupDatabase(): Promise<void> {
  try {
    // Koneksi ke database
    await sequelize.authenticate();
    console.log("Koneksi berhasil.");

    // Ambil data dari setiap tabel
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const backupData: Record<string, any[]> = {};
    for (const model of models) {
      const records = await model.findAll();
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      backupData[model.name] = records.map((record: any) => record.toJSON());
    }

    const dbName = process.env.DB_NAME || "database";
    const timestamp = getTimestamp();
    const fileName = path.resolve(
      __dirname,
      `../backups/${dbName}_backup_${timestamp}.json`
    );

    // Pastikan direktori ada
    await ensureDirectoryExistence(fileName);

    // Simpan data ke file .json dengan nama timestamp
    fs.writeFileSync(fileName, JSON.stringify(backupData, null, 2), "utf-8");
    console.log("Backup selesai.");
    console.log(`File disimpan sebagai ${fileName}`);
  } catch (error) {
    console.error("Gagal melakukan backup:", error);
  } finally {
    await sequelize.close();
  }
}

// Jalankan fungsi backup
backupDatabase();
