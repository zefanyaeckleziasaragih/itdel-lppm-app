import { DataTypes } from "sequelize";
import db from "../utils/dbUtil";

const TABLE_NAME = "m_todos";
const DataModel = db.define(
  TABLE_NAME,
  {
    id: {
      type: DataTypes.UUID,
      defaultValue: DataTypes.UUIDV4,
      primaryKey: true,
    },
    user_id: {
      type: DataTypes.UUID,
      defaultValue: DataTypes.UUIDV4,
      primaryKey: true,
    },
    title: {
      type: DataTypes.STRING,
      allowNull: false,
    },
    description: {
      type: DataTypes.TEXT,
      allowNull: true,
    },
    is_done: {
      type: DataTypes.BOOLEAN,
      defaultValue: false,
    },
    cover: {
      type: DataTypes.STRING,
      allowNull: true,
    },
  },
  {
    tableName: TABLE_NAME,
    createdAt: "created_at",
    updatedAt: "updated_at",
    timestamps: true,
  }
);

export default DataModel;
