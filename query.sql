CREATE DATABASE absensibi;

CREATE TABLE administratif (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
)

CREATE TABLE kelas (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL UNIQUE KEY,
)

CREATE TABLE guru (
    nik VARCHAR(25) NOT NULL PRIMARY KEY,
    username VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    walikelas INT,
    status ENUM('Aktif', 'Non-Aktif') DEFAULT 'Non-Aktif',
    foto MEDIUMBLOB,
    FOREIGN KEY (walikelas) REFERENCES kelas(id)
)

CREATE TABLE siswa(
    nis VARCHAR(25) NOT NULL PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    kelas INT,
    status ENUM('Aktif', 'Non-Aktif') DEFAULT 'Non-Aktif',
    foto MEDIUMBLOB,
    FOREIGN KEY (kelas) REFERENCES kelas(id)
)

CREATE TABLE event(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    tanggalmulai DATE NOT NULL,
    tanggalselesai DATE NOT NULL,
    status ENUM('Masuk', 'Libur') NOT NULL,
    waktu TIME
)

CREATE TABLE jadwal(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    hari ENUM('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu') NOT NULL,
    status ENUM('Masuk', 'Libur') NOT NULL,
    waktu TIME
)

CREATE TABLE absensi(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    siswa VARCHAR(25) NOT NULL,
    kelas INT,
    tanggal DATE NOT NULL,
    waktu TIME NOT NULL,
    foto MEDIUMBLOB,
    status ENUM('Diproses', 'Diterima') DEFAULT 'Diproses', 
    absen ENUM('Tepat Waktu', 'Telat', 'Sakit', 'Izin', 'Alpha') DEFAULT 'Alpha',
    FOREIGN KEY (siswa) REFERENCES siswa(nis),
    FOREIGN KEY (kelas) REFERENCES kelas(id)
)

CREATE TABLE izin(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    siswa VARCHAR(25) NOT NULL,
    kelas INT,
    tanggal DATE NOT NULL,
    status ENUM('Diproses', 'Diterima') DEFAULT 'Diproses', 
    foto MEDIUMBLOB,
    alasan ENUM('Sakit', 'Izin') NOT NULL,
    deskripsi VARCHAR(255) NOT NULL,
    FOREIGN KEY (siswa) REFERENCES siswa(nis),
    FOREIGN KEY (kelas) REFERENCES kelas(id)
)

INSERT INTO administratif (username, password)
VALUES
('admin', '68484215')

INSERT INTO guru (nik, username, password, nama, walikelas, status, foto)
VALUES
('13.07.01.002', '13.07.01.002', '44832313', 'Sinta Dewi, S.Pd., M.Kom', NULL, 'Aktif', NULL),
('12.13.07.028', '12.13.07.028', '05521288', 'Rizam Nuruzzaman, M.Pd.', NULL, 'Aktif', NULL),
('13.23.17.186', '13.23.17.186', '07124277', 'Ahmad Ridhwan Hanafi, Lc.', NULL, 'Aktif', NULL)

INSERT INTO siswa (nis, username, password, nama, kelas, status, foto)
VALUES
('23.17.3.049', '23.17.3.049', '39524965', 'Mohammad Aqil Athvihaz', NULL, 'Aktif', NULL),
('23.17.3.052', '23.17.3.052', '34398760', 'Ngayuga Jenar Rahmadan', NULL, 'Aktif', NULL),
('23.17.3.054', '23.17.3.054', '06894801', 'Nalendra Chandar Mulya', NULL, 'Aktif', NULL),
('23.17.3.062', '23.17.3.062', '10494767', 'Rizq Dzaki Samudera', NULL, 'Non-Aktif', NULL),
('23.17.3.046', '23.17.3.046', '62848430', 'Kaysan Maulana Pasha', NULL, 'Non-Aktif', NULL)

INSERT INTO jadwal (hari, status, waktu)
VALUES
('Senin')

-- CREATE TABLE jadwalharian(
--     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
--     hari ENUM(
--         'Senin', 
--         'Selasa', 
--         'Rabu', 
--         'Kamis', 
--         'Jumat', 
--         'Sabtu', 
--         'Minggu') NOT NULL,
--     libur BOOLEAN DEFAULT FALSE,
--     bataswaktumasuk TIME NOT NULL,
--     kelasterlibat VARCHAR(255),
--     deskripsi VARCHAR(255) NOT NULL
-- )

-- CREATE TABLE jadwalkegiatan(
--     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
--     nama VARCHAR(255) NOT NULL,
--     tanggal_mulai DATE NOT NULL,
--     tanggal_akhir DATE NOT NULL,
--     libur BOOLEAN DEFAULT FALSE,
--     bataswaktumasuk TIME NOT NULL,
--     deskripsi VARCHAR(255) NOT NULL
-- )

-- CREATE TABLE absensi(
--     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
--     waktu TIME NOT NULL,
--     tanggal DATE NOT NULL,
--     siswa VARCHAR(255) NOT NULL,
--     foto MEDIUMBLOB,
--     status ENUM('Diproses', 'Masuk', 'Ditolak'),
--     FOREIGN KEY (siswa) REFERENCES siswa(nis)
-- )

-- CREATE TABLE izin(
--     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
--     waktu TIME NOT NULL,
--     tanggal DATE NOT NULL,
--     siswa VARCHAR(255) NOT NULL,
--     foto MEDIUMBLOB,
--     deskripsi VARCHAR(255) NOT NULL,
--     izinstatus ENUM('Izin', 'Sakit'),
--     status ENUM('Diproses', 'Diterima', 'Ditolak'),
--     FOREIGN KEY (siswa) REFERENCES siswa(nis)
-- )

-- CREATE TABLE kehadiran(
--     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
--     waktu TIME NOT NULL,
--     tanggal DATE NOT NULL,
--     siswa VARCHAR(255) NOT NULL,
--     guru VARCHAR(255) NOT NULL,
--     foto MEDIUMBLOB,
--     status ENUM('Masuk', 'Telat', 'Izin', 'Sakit', 'Alpha') NOT NULL DEFAULT 'Alpha',
--     FOREIGN KEY (siswa) REFERENCES siswa(nis),
--     FOREIGN KEY (guru) REFERENCES guru(nik)
-- )