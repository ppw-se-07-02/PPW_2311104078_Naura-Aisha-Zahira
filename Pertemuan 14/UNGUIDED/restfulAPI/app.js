//app.js 
const express = require("express");
console.log("APP.JS DIJALANKAN");
const dbOperations = require("./crud");

const app = express();
const port = 3000;

app.use(express.json());

// Endpoint untuk menambahkan data 
app.post("/mahasiswaCreate", (req, res) => {
    const { nama, nim, jurusan, email } = req.body;

    dbOperations.createMahasiswa(nama, nim, jurusan, email, (error) => {
        if (error) {
            return res.status(500).send("Error creating");
        }
        res.status(201).send("Mahasiswa created");
    });
});

// Endpoint untuk mendapatkan semua data 
app.get("/mahasiswaGet", (req, res) => {
    dbOperations.getAllMahasiswa((error, users) => {
        if (error) {
            return res.status(500).send("Error fetching users");
        }
        res.json(users);
    });
});

// Endpoint untuk memperbarui data 
app.put("/mahasiswaUpdate/:id", (req, res) => {
    const { id } = req.params;
    const { nama, nim, jurusan, email } = req.body;

    dbOperations.updateMahasiswa(id, nama, nim, jurusan, email, (error) => {
        if (error) {
            return res.status(500).send("Error updating");
        }
        res.send("Mahasiswa updated");
    });
});

// Endpoint untuk menghapus data 
app.delete("/mahasiswaDelete/:id", (req, res) => {
    const { id } = req.params;

    dbOperations.deleteMahasiswa(id, (error) => {
        if (error) {
            return res.status(500).send("Error deleting");
        }
        res.send("Mahasiswa deleted");
    });
});

// Jalankan server 
app.listen(port, () => {
    console.log(`Server running on http://localhost:${port}`);
});
