const connection = require("./db");
// Create 
function createMahasiswa(nama, nim, jurusan, email, callback) {
    const query =
        "INSERT INTO mahasiswa (nama, nim, jurusan, email) VALUES (?, ?, ?, ?)";
    connection.query(query, [nama, nim, jurusan, email], (error,
        results) => {
        if (error) {
            return callback(error, null);
        }
        callback(null, results);
    });
}

// Read 
function getAllMahasiswa(callback) {
    const query = "SELECT * FROM mahasiswa";
    connection.query(query, (error, results) => {
        if (error) {
            return callback(error, null);
        }
        callback(null, results);
    });
}

// Update 
function updateMahasiswa(id, nama, nim, jurusan, email, callback) {
    const query =
        "UPDATE mahasiswa SET nama = ?, nim = ?, jurusan = ?, email = ? WHERE id = ? ";
    connection.query(query, [nama, nim, jurusan, email, id], (error,
        results) => {
        if (error) {
            return callback(error, null);
        }
        if (results.affectedRows === 0) {
            return callback(new Error("No rows updated, ID may not exist"),
                null);
        }
        callback(null, results);
    });
}

// Delete 
function deleteMahasiswa(id, callback) {
    const query = "DELETE FROM mahasiswa WHERE id = ?";
    connection.query(query, [id], (error, results) => {
        if (error) {
            return callback(error, null);
        }
        callback(null, results);
    });
}

module.exports = {
    getAllMahasiswa,
    createMahasiswa,
    updateMahasiswa,
    deleteMahasiswa,
};