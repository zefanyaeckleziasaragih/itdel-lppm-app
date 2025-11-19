import { Sequelize } from "sequelize";
import dotenv from "dotenv";
dotenv.config();

const sequelize = new Sequelize(
  process.env.DB_NAME || "",
  process.env.DB_USER || "",
  process.env.DB_PASSWORD || "",
  {
    dialect: "postgres",
    host: process.env.DB_HOST || "",
    port: Number(process.env.DB_PORT) || 5432,
    logging: process.env.DB_LOG === "true" ? console.log : false,
  }
);

export default sequelize;
