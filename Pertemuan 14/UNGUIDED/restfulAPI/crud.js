const db = require("./db");

function getAllMahasiswa(callback) {
  db.query("SELECT * FROM mahasiswa", callback);
}

function createMahasiswa(data, callback) {
  const sql = "INSERT INTO mahasiswa SET ?";
  db.query(sql, data, callback);
}

module.exports = { getAllMahasiswa, createMahasiswa };
