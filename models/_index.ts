import type { Model, ModelStatic } from "sequelize";

// Import all models here
import HakAksesModel from "./HakAksesModel";
import TodoModel from "./TodoModel";

const models: ModelStatic<Model>[] = [
  // Hak Akses
  HakAksesModel,

  // Todos
  TodoModel,
];

export default models;
