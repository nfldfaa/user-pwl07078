-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Apr 2025 pada 18.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pwl07078`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri_gambar`
--

CREATE TABLE `galeri_gambar` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `thumbpath` varchar(255) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `uploaded_at` int(11) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `galeri_gambar`
--

INSERT INTO `galeri_gambar` (`id`, `filename`, `filepath`, `thumbpath`, `width`, `height`, `uploaded_at`) VALUES
(5, 'Farmer', 'gambar/uploads/67fe597786454_1744722295.png', 'gambar/thumbs/thumb_67fe597786454_1744722295.png', 512, 512, 2147483647),
(6, 'Sapi', 'gambar/uploads/67fe5b871de86_1744722823.png', 'gambar/thumbs/thumb_67fe5b871de86_1744722823.png', 512, 512, 2147483647),
(7, 'Pupuk', 'gambar/uploads/67fe5bcc145af_1744722892.png', 'gambar/thumbs/thumb_67fe5bcc145af_1744722892.png', 512, 512, 2147483647),
(9, 'Beras', 'gambar/uploads/67fe78e0347e1_1744730336.jpg', 'gambar/thumbs/thumb_67fe78e0347e1_1744730336.jpg', 1054, 1054, 2147483647),
(10, 'Daging', 'gambar/uploads/67fe792d7f629_1744730413.png', 'gambar/thumbs/thumb_67fe792d7f629_1744730413.png', 1024, 1024, 2147483647),
(12, 'Dokter', 'gambar/uploads/67fe7d5f57fcd_1744731487.png', 'gambar/thumbs/thumb_67fe7d5f57fcd_1744731487.png', 512, 512, 2147483647),
(13, 'Farmer Grey', 'gambar/uploads/67fe80c376a53_1744732355.png', 'gambar/thumbs/thumb_67fe80c376a53_1744732355.png', 512, 512, 2147483647);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `iduser` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `status` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`iduser`, `username`, `password`, `status`) VALUES
(2, 'Test3', 'dsvhjs', 'testi'),
(4, 'nfldfaa_', '$2y$10$kbVqUjEdxstd8enXjFiVueZ9u', 'hadir'),
(7, 'nfldfaa_', '$2y$10$p5Fml4QkjZQV6Jv0LSmv5O5s7', 'hadir'),
(8, 'nfldfaa_', '$2y$10$kqThsDhzM9n9g2RU6h8dou08E', 'hadir'),
(11, 'ewfewsvfzv', 'sfvesfesf', 'svsfs'),
(12, 'dadaefcaec', '$2y$10$2mG1GtGhFjAX0tWpO7XwbuXRc', 'aca'),
(14, 'sacasc', '12345', 'cacad'),
(15, 'ascsacsac', '$2y$10$Qq/IhaibdRl0dBJU95J9cOmdL', 'dad'),
(16, 'cc', '1', '1'),
(17, 'Daffa Naufal Athallah', 'DaffaGantenf01', 'hadir'),
(18, 'Naufal Daffa', 'Pipiyo01', 'Izin'),
(21, 'daffaganteng', 'pipiyo04', 'hadir'),
(22, 'athaa_uiux', '070604', 'keren'),
(23, 'Prof. Ir. Daffa', 'Jenius', 'No.1');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `galeri_gambar`
--
ALTER TABLE `galeri_gambar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `galeri_gambar`
--
ALTER TABLE `galeri_gambar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `iduser` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
