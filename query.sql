CREATE DATABASE absensibi;

CREATE TABLE administratif (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL
)

CREATE TABLE guru (
    nik VARCHAR(25) NOT NULL PRIMARY KEY,
    username VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    walikelas ENUM(
        'X RPL',
        'X TKJ',
        'X DKV',
        'X ANM',
        'X BC',
        'X GD',
        'XI RPL',
        'XI TKJ',
        'XI DKV',
        'XI ANM',
        'XI BC',
        'XI GD',
        'XII RPL',
        'XII TKJ',
        'XII DKV',
        'XII ANM',
        'XII BC',
        'XII GD') DEFAULT NULL,
    status ENUM('Aktif', 'Non-Aktif') DEFAULT 'Non-Aktif',
    foto MEDIUMBLOB
)

CREATE TABLE siswa(
    nis VARCHAR(25) NOT NULL PRIMARY KEY,
    username VARCHAR(255),
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(255) NOT NULL,
    kelas ENUM(
        'X RPL',
        'X TKJ',
        'X DKV',
        'X ANM',
        'X BC',
        'X GD',
        'XI RPL',
        'XI TKJ',
        'XI DKV',
        'XI ANM',
        'XI BC',
        'XI GD',
        'XII RPL',
        'XII TKJ',
        'XII DKV',
        'XII ANM',
        'XII BC',
        'XII GD') NOT NULL,
    status ENUM('Aktif', 'Non-Aktif') DEFAULT 'Non-Aktif',
    foto MEDIUMBLOB
)

CREATE TABLE absensi(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    waktu TIME NOT NULL,
    tanggal DATE NOT NULL,
    siswa VARCHAR(255) NOT NULL,
    foto MEDIUMBLOB,
    status ENUM('Diproses', 'Masuk', 'Ditolak'),
    FOREIGN KEY (siswa) REFERENCES siswa(nis)
)

CREATE TABLE izin(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    waktu TIME NOT NULL,
    tanggal DATE NOT NULL,
    siswa VARCHAR(255) NOT NULL,
    foto MEDIUMBLOB,
    deskripsi VARCHAR(255) NOT NULL,
    izinstatus ENUM('Izin', 'Sakit'),
    status ENUM('Diproses', 'Diterima', 'Ditolak'),
    FOREIGN KEY (siswa) REFERENCES siswa(nis)
)

CREATE TABLE kehadiran(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    waktu TIME NOT NULL,
    tanggal DATE NOT NULL,
    siswa VARCHAR(255) NOT NULL,
    guru VARCHAR(255) NOT NULL,
    foto MEDIUMBLOB,
    status ENUM('Masuk', 'Telat', 'Izin', 'Sakit', 'Alpha') NOT NULL DEFAULT 'Alpha',
    FOREIGN KEY (siswa) REFERENCES siswa(nis),
    FOREIGN KEY (guru) REFERENCES guru(nik)
)

CREATE TABLE jadwalharian(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    hari ENUM(
        'Senin', 
        'Selasa', 
        'Rabu', 
        'Kamis', 
        'Jumat', 
        'Sabtu', 
        'Minggu') NOT NULL,
    libur BOOLEAN DEFAULT FALSE,
    bataswaktumasuk TIME NOT NULL,
    kelasterlibat ENUM(
        'Semua Kelas',
        'X RPL',
        'X TKJ',
        'X DKV',
        'X ANM',
        'X BC',
        'X GD',
        'XI RPL',
        'XI TKJ',
        'XI DKV',
        'XI ANM',
        'XI BC',
        'XI GD',
        'XII RPL',
        'XII TKJ',
        'XII DKV',
        'XII ANM',
        'XII BC',
        'XII GD') NOT NULL,
    deskripsi VARCHAR(255) NOT NULL
)

CREATE TABLE jadwalkegiatan(
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(255) NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_akhir DATE NOT NULL,
    libur BOOLEAN DEFAULT FALSE,
    bataswaktumasuk TIME NOT NULL,
    deskripsi VARCHAR(255) NOT NULL
)

INSERT INTO administratif (username, password)
VALUES
('admin', '68484215')

INSERT INTO guru (nik, username, password, nama, walikelas, status, foto)
VALUES
('13.07.01.002', NULL, '32409110', 'Sinta Dewi, S.Pd., M.Kom', NULL, 'Aktif', NULL),
('12.13.07.028', NULL, '79582265', 'Rizam Nuruzzaman, M.Pd.', 'XII RPL', 'Aktif', NULL)

INSERT INTO siswa (nis, username, password, nama, kelas, status, foto)
VALUES
('23.17.3.049', NULL, '32409110', 'Mohammad Aqil Athvihaz', 'XII RPL', 'Aktif', NULL),
('23.17.3.052', NULL, '79582265', 'Ngayuga Jenar Rahmadan', 'XII RPL', 'Aktif', NULL),
('23.17.3.054', NULL, '79582265', 'Nalendra Chandar Mulya', 'XII RPL', 'Aktif', NULL),
('23.17.3.062', NULL, '79582265', 'Rizq Dzaki Samudera', 'XII RPL', 'Non-Aktif', NULL),
('23.17.3.046', NULL, '79582265', 'Kaysan Maulana Pasha', 'XII RPL', 'Non-Aktif', NULL)