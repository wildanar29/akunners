-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: akunurse
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `data_asesor`
--

DROP TABLE IF EXISTS `data_asesor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `data_asesor` (
  `id_asesor` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `no_reg` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_berlaku` date DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_asesor`),
  UNIQUE KEY `no_reg` (`no_reg`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `data_asesor_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `data_asesor`
--

LOCK TABLES `data_asesor` WRITE;
/*!40000 ALTER TABLE `data_asesor` DISABLE KEYS */;
INSERT INTO `data_asesor` VALUES (3,24,'ASK.123456','2025-05-31',1);
/*!40000 ALTER TABLE `data_asesor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_kuk_form3`
--

DROP TABLE IF EXISTS `doc_kuk_form3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doc_kuk_form3` (
  `doc_id` int NOT NULL AUTO_INCREMENT,
  `nama_doc` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`doc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_kuk_form3`
--

LOCK TABLES `doc_kuk_form3` WRITE;
/*!40000 ALTER TABLE `doc_kuk_form3` DISABLE KEYS */;
INSERT INTO `doc_kuk_form3` VALUES (1,'EMR'),(2,'EMR PPA notes'),(3,'EMR fluid balance'),(4,'LOGBOOK'),(5,'EMR integrated notes');
/*!40000 ALTER TABLE `doc_kuk_form3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elemen_form_3`
--

DROP TABLE IF EXISTS `elemen_form_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elemen_form_3` (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_elemen_form_3` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isi_elemen` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elemen` (`no_elemen_form_3`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elemen_form_3`
--

LOCK TABLES `elemen_form_3` WRITE;
/*!40000 ALTER TABLE `elemen_form_3` DISABLE KEYS */;
INSERT INTO `elemen_form_3` VALUES (3,'2','Melakukan komunikasi Interpersonal dalam melaksanakan tindakan keperawatan '),(4,'3','Menerapkan prinsip etika : otonomi, beneficience, justice, non maleficienci, veracity, fidelity, confidentiality, accountability dalam asuhan keperawatan\r\n\r\n'),(5,'4','Menerapkan prinsip-prinsip pencegahan infeksi rumah sakit'),(6,'5','Menciptakan dan memelihara lingkungan keperawatan yang aman melalui jaminan kualitas dan manajemen risiko'),(7,'6','Menggunakan tindakan pencegahan (langkah/tindakan) untuk mencegah cedera tekan pada pasein'),(8,'7','Mengukur tanda vital'),(9,'8','Memfasilitasi pemenuhan kebutuhan oksigen'),(10,'9','Memfasilitasi pemenuhan cairan dan elektrolit'),(11,'10','Perawatan luka  kering '),(12,'11','Pemberian obat secara aman dan tepat'),(13,'12','Mengelola pemberian darah dan produk darah secara aman'),(14,'1','Menganalisis, menginterpretasi data dan dokumentasi secara akurat : Melaksanakan pengkajian keperawatan dan kesehatan yang sistematis pada pasien minimal care');
/*!40000 ALTER TABLE `elemen_form_3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elemen_kompetensi_form_2`
--

DROP TABLE IF EXISTS `elemen_kompetensi_form_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elemen_kompetensi_form_2` (
  `no_elemen` int NOT NULL AUTO_INCREMENT,
  `nama_elemen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`no_elemen`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elemen_kompetensi_form_2`
--

LOCK TABLES `elemen_kompetensi_form_2` WRITE;
/*!40000 ALTER TABLE `elemen_kompetensi_form_2` DISABLE KEYS */;
INSERT INTO `elemen_kompetensi_form_2` VALUES (1,'Elemen Kompetensi 1'),(2,'Elemen Kompetensi 2'),(3,'Elemen Kompetensi 3'),(4,'Elemen Kompetensi 4'),(5,'Elemen Kompetensi 5 '),(6,'Elemen Kompetensi 6'),(7,'Elemen Kompetensi 7'),(8,'Elemen Kompetensi 8'),(9,'Elemen Kompetensi 9'),(10,'Elemen Kompetensi 10 '),(11,'Elemen Kompetensi 11'),(12,'Elemen Kompetensi 12 ');
/*!40000 ALTER TABLE `elemen_kompetensi_form_2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form3_a`
--

DROP TABLE IF EXISTS `form3_a`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form3_a` (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_iuk` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_poin` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `no_iuk` (`no_iuk`),
  KEY `poin_diamati` (`no_poin`),
  KEY `no_poin` (`no_poin`),
  CONSTRAINT `form3_a_ibfk_1` FOREIGN KEY (`no_iuk`) REFERENCES `iuk_form3` (`no_iuk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form3_a_ibfk_2` FOREIGN KEY (`no_poin`) REFERENCES `poin_tabel_form3` (`no_poin`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form3_a`
--

LOCK TABLES `form3_a` WRITE;
/*!40000 ALTER TABLE `form3_a` DISABLE KEYS */;
INSERT INTO `form3_a` VALUES (1,'1.2.1','1A'),(2,'1.2.3','1B'),(3,'1.3.1','1C'),(4,'1.4.2','1D'),(5,'2.2.1','2A'),(6,'2.3.1','2B'),(7,'2.3.2','2C'),(8,'3.2.3','3A'),(9,'4.3.2','4A'),(10,'4.3.3','4B'),(11,'5.1.1','5A'),(12,'6.1.1','6A'),(13,'7.1.1','7A'),(14,'7.2.1','7B'),(15,'7.3.1','7C'),(16,'7.4.1','7D'),(17,'7.5.1','7E'),(18,'7.6.1','7F'),(19,'7.8.1','7G'),(20,'8.1.1','8A'),(21,'8.2.1','8B'),(22,'8.4.1','8C'),(23,'8.6.1','8D'),(24,'10.1.1','10A'),(25,'10.3.1','10B'),(26,'10.5.1','10C');
/*!40000 ALTER TABLE `form3_a` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form3_b`
--

DROP TABLE IF EXISTS `form3_b`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form3_b` (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_iuk` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_soal` int DEFAULT NULL,
  `pertanyaan` text COLLATE utf8mb4_general_ci,
  `indikator_pencapaian` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `no_iuk` (`no_iuk`),
  CONSTRAINT `form3_b_ibfk_1` FOREIGN KEY (`no_iuk`) REFERENCES `iuk_form3` (`no_iuk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=115 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form3_b`
--

LOCK TABLES `form3_b` WRITE;
/*!40000 ALTER TABLE `form3_b` DISABLE KEYS */;
INSERT INTO `form3_b` VALUES (1,'1.4.1',1,'Sebutkan prinsip yang perlu diperhatikan pada pengumpulan data?','Sistematis dan komperhensif'),(2,'1.4.1',2,'Sebutkan aspek pengumpulan data?','Biopsikososial'),(3,'1.4.1',3,'Siapa saja yang boleh melakukan proses pengumpulan data pada pasien minimal care?','Perawat klinik 1 yang sudah memiliki SPK dan RKK, bila yang melakukan pra Perawat Klinik harus dilakukan validasii oleh PK 2 atau PK 3 atau Pj Shift atau Kepala Ruang.'),(4,'1.4.1',4,'Apa yang dilakukan jika pasien yang akan operasi af DJ Stent memakai perhiasan / barang berharga banyak?','-Menyarankan pasien untuk menyimpan perhiasannya di tempat yang aman.\r\n-Menyimpan perhiasan pasien di lemari penitipan barang berharga milik pasien.\r\n-Melakukan serah terima perhiasan.\r\n'),(5,'2.4.1',1,'Sebutkan prinsip komunikasi?','-Fokus utama adalah pasien\r\n-Berperilaku profesional\r\n-Hindari hubungan sosial dengan pasien\r\n-Jaga rahasia pasien\r\n'),(6,'2.4.1',2,'Sebutkan 4 langkah komunikasi terapeutik?','-Pra interaksi\r\n-Perkenalan / orientasi\r\n-Kerja\r\n-Terminasi\r\n'),(7,'3.1.1',1,'Apa saja masalah etik yang terjadi dalam asuhan keperawatan?','-Rasa tidak adil dalam pelayanan keperawatan.\r\n-Kurang nya perhatian dan kepedulian terhadap pasien.\r\n-Mengabaikan hak pasien.\r\n-Kurang nya menghargai pasien.\r\n-Tingginya tuntutan etika profesional pada setiap pelayanan pasien.\r\n'),(8,'3.1.1',2,'Sebutkan prinsip etik keperawatan?','Otonomi, benefisiensi, justice, malefisiensi, veracity, fidelity, confidentiality, accountability.'),(9,'3.1.2',1,'Sebutkan faktor yang mempengaruhi dalam pengambilan keputusan masalah dilema etik?','Agama, sosial, ilmu pengetahuan, teknologi, legislasi, keputusan yuridis, dana, keuangan, pekerjaan, posisi pasien ataupun perawat, kode etik keprawatan dan hak hak pasien.'),(10,'3.1.3',1,'Identifikasi masalah pada pasien yang menolak diberikan terapi karena ingin meninggal dengan damai?','-Topik : persetujuan pasien terhadap tindakan keperawatan atau kedokteran\r\n-Dilema aetis : memenuhi permintaan pasien atau memberikan terapi tanpa persetujuan\r\n'),(11,'3.1.3',2,'Solusi pada pasien tersebut menggunakan prinsip etik benefisiensi?','Tetap memberikan terapi tanpa persetujuan dari pasien, karena apabila tidak dilakukan akan memperparah keadaan atau kondisi pasien'),(12,'3.1.3',3,'Solusi pada pasien tersebut menggunakan prinsip etik autonomi?','Dengan berat hati perawat tidak memberikan terapi kepada pasien tersebut untuk menghormati keputusan pasien setelah dijelaskan kondisi dan konsekuensi kepada pasien.'),(13,'3.2.1',1,'Yang dilakukan pada pasien setelah mengganti infus dan mengatakan akan kembali lagi setelah 2 jam untuk mengecek kelancaran tetesan infus pada pasien adalah','Kembali sesuai kontrak waktu yang dilakukan kepada pasien tersebut untuk melakukan pengecekan kelancaran tetesan infus'),(14,'3.2.1',2,'Hal tersebut dilakukan karena perawat melaksanakan prinsip etik ','Fidelity -> selalu menepati janji'),(15,'3.2.1',3,'Apa yang anda lakukan jika sudah 2x tidak berhasil memasang infus pada pasien anda?','Lapor PJ Shift atau kepala ruangan'),(16,'3.2.2',1,'Sebutkan aspek legal yang harus dimiliki perawat yang akan merawat apsien minimal care di rs','-STR\r\n-SIPP\r\n-Sertifikat kompetensi\r\n-RKK\r\n-SPK\r\n'),(17,'3.2.2',2,'Sanksi apa yang didapat perawat jika menjalankan praktik selain yang tercantum dalam SIP dan bila melakukan perbuatan yang bertentangan dengan standar profesi','Sesuai kebijakan pimpinan RS'),(18,'3.2.2',3,'Sebutkan dilema etik pada pasien minimal care','PAPS, pulang atas permintaan sendiri'),(19,'4.3.1',1,'Sebutkan macam-macam APD yang digunakan di RS sesuai dengan SPO','-Topi\r\n-Kacamata (google)\r\n-Masker\r\n-Sarung tangan\r\n-Bismet\r\n-Sepatu boot\r\n'),(20,'4.4.1',1,'Jelaskan etika batuk yang benar dan apa tujuannya?','-Menutup hidung dan mulut dengan tisu atau lengan baju ketika batuk dan bersin \r\n-Segera buang tisu yang sudah di pakai ke dalam tempat sampah\r\n-Cuci tangan\r\n-Pake masker\r\n-Tujuan mencegah penyebaran suatu penyakit secara luas melalui udara bebas (droplet'),(21,'4.4.2',1,'Ruang isolasi terbagi dalam berapa jenis, sebutkan','-Isolasi dengan tekanan udara negative\r\n-Isolasi dengan tekanan udara positif\r\n'),(22,'5.1.1',1,'Bagaimana melakukan skrining jatuh pada pasien usia lansia?','-Asesmen/ Pengkajian Risiko Jatuh pada pasien Lansia/ Geriatrik menggunakanONTARIO MODIFIED STRATIFY – SYDNEY SCORING'),(23,'5.1.1',2,'Bagaimana melakukan skrining jatuh pada pasien dewasa?','-Asesmen/ Pengkajian Risiko Jatuh pada pasien dewasa menggunakan MORSE FALL SCALL (Skala Jatuh Morse'),(24,'6.2.1',1,'Alat apa kah yang dapat di gunakan sebagai suport permukaan alas pasien?','-Matras decubitus\r\n-Bantal/busa di bawah kaki untuk tekanan di area tumit\r\n'),(25,'6.3.1',1,'Tindakan apa yang  bisa di lakukan untuk mencegah terjadinya cedera tekan pada pasien?','-Posisioning dan reposisi\r\n-Menghilangkan gesekan dan geseran\r\n-Mengurangi tekanan di tumit\r\n-Aktivitas dan mobilisasi\r\n-Support permukaan alas\r\n'),(26,'7.3.1',1,'Jika pada saat melakukan pengukuran 5 tanda vital ada hasil yang tidak normal, apakah yang saudara lakukan sesuai SPO?','-Melapor kepada pj shift atau dr yang merawat\r\n-Pasien disarankan untuk istirahat/relaksasi dengan posisi yang nyaman (sesuaikan dengan kondisi ) \r\n'),(27,'8.5.1',1,'Sebutkan tehnik pemberian oksigen menggunakan:\r\n1. Binasal kanul\r\n2. masker non rebreathing\r\n','-Kanul binasal : 1-6 lpm\r\n-Masker non rebreathing : 6-10 lpm'),(28,'10.1.1',1,'Berdasarkan apa anda melakukan perawatan luka?','-Order dokter\r\n-Balutan kotor\r\n-Sesuai jadwal penggantian balutan \r\n'),(29,'11.4.1',1,'Respon apa saja yang harus diperhatikan setelah pemberian obat? ','-Keluhan : Pusing, mual , nyeri, sesak napas\r\n-Adanya alergi : gatal gatal, kulit kemrahan, bengkak di bagian tubuh\r\n-Perubahan tanda tanda vital : Tekanan darah, suhu tubuh, Nadi, pernapasan \r\n'),(30,'12.2.1',1,'Apa saja yang harus dikaji dalam menilai reaksi saat pemberian transfuse darah? ','Tanda tanda alergi : , menggigil sakit kepala, gatal gatal, dll.'),(31,'12.4.1',1,'Bagaimana penanganan reksi transfuse darah sesuai dengan SPO?','-Hentikan transfuse darah \r\n-Ganti infuse set\r\n-Spooling dengan Nacl 0,9 \r\n-Lapor PJ shift dan DPJP\r\n-Laporkan kejadian ke Bank Darah dan bawa sisa darah ke Bank darah\r\n');
/*!40000 ALTER TABLE `form3_b` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form3_c`
--

DROP TABLE IF EXISTS `form3_c`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form3_c` (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_iuk` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_soal` int DEFAULT NULL,
  `pertanyaan` text COLLATE utf8mb4_general_ci,
  `standar_jawaban` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `no_iuk` (`no_iuk`),
  CONSTRAINT `form3_c_ibfk_1` FOREIGN KEY (`no_iuk`) REFERENCES `iuk_form3` (`no_iuk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form3_c`
--

LOCK TABLES `form3_c` WRITE;
/*!40000 ALTER TABLE `form3_c` DISABLE KEYS */;
INSERT INTO `form3_c` VALUES (32,'1.1.1',1,'Bagaimana status biopsikososial untuk pasien minimal care?\r\na. tidak stabil\r\nb. stabil\r\nc. kadang stabil kadang tidak','b. stabil'),(33,'1.1.1',2,'Bagaimana kriteria / manifestasi klinis pasien minimal care yang harus diperhatikan?\r\na. pasien bisa mandiri\r\nb. pasien hampir tidak memerlukan bantuan\r\nc. a dan b benar\r\n','c. a dan b benar'),(34,'1.1.1',3,'Sebutkan contoh pasien minimal care :\r\na. Pasien yang dirawat untuk prosedur \r\n   diagnostik\r\nb. Pasien dengan operasi ringan\r\nc. a dan b benar\r\n','c. a dan b benar'),(35,'1.2.1',1,'Sebutkan alat atau skala yang digunakan Untuk melkaukan pengkajian pada pasien minimal care?\r\na. Format pengkajian\r\nb. Alat mengukur TTV + saturasi oksigen\r\nc. ATK\r\nd. a, b dan c benar\r\n','d. a, b dan c benar'),(36,'1.2.1',2,'Sebutkan skala nyeri ringan dewasa menurut Numeric Rating Scale!\r\na. 1-3\r\nb. 4-6\r\nc. 7-10\r\n','a. 1-3'),(37,'1.2.1',3,'Sebutkan nilai kesadaran composmentis pada pasien dewasa dengan menggunakan GCS!\r\na. E4V5E6\r\nb. E3V3M4\r\nc. E2V2M2\r\n','a. E4V5M6'),(38,'1.3.1',1,'Yang termasuk data subjektif pada pasien minimal care adalah ...\r\na. Pasien mengatakan saya bisa berjalan \r\n   sendiri\r\nb. Pasien mengeluh cemas karena akan \r\n   dilakukan tindakan operasi besok\r\nc. a dan b benar\r\n','c. a dan b benar'),(39,'1.3.1',2,'Yang termasuk data objektif pada pasien minimal care adalah...\r\na. Pasien melakukan ambulasi dan berjalan sendiri\r\nb. Status psikologis stabil\r\nc. a dan b benar\r\n','c. a dan b benar'),(40,'1.3.1',3,'Yang termasuk data objektif pada apsien minimal care :\r\na. TTV\r\nb. hasil pemeriksaan penunjang\r\nc. a dan b benar\r\n','c. adan b benar'),(41,'1.3.2',1,'Sebutkan sumber data pada pasien yang anda kelola:\r\na. pasien\r\nb. keluarga / orang tua\r\nb. pengantar\r\nd. emr\r\n','Sesuai pasien kelolaan'),(42,'1.3.2',2,'Sebutkan sumber data primer pada pasien yang anda kelola:\r\na. pasien\r\nb. orangtua\r\nc. keluarga\r\nd. emr\r\n','Sesuai pasien kelolaan'),(43,'1.3.2',3,'Sebutkan metode pengumpulan data:\r\na. wawancara\r\nb. observasi\r\nc. konsultasi\r\nd. pemeriksaaan \r\ne. a, d, c dan d benar\r\n','e. a, b, c, dan d benar'),(45,'1.4.1',1,'Kelompokkan data pada kasus diatas ','DS: pasien merasa cemas karena ada benjolan di payudara kiri, tidak terasa nyeri\r\nDO: \r\n- Benjolan di payudara kiri dekat ketiak diameter \r\n  1 cm, kenyal dapat digerakkan\r\n- TTV dalam batas noormal\r\n- Rencana biopsi\r\n'),(46,'1.4.2',2,'Identifikasi masalah keperawatan pada pasien tsb.','Benjolan di payudara kiri -> pemeriksaan diagnostik -> rencana tindakan invasif (biopsi) -> ansietas'),(47,'1.4.3',3,'Rumuskan diagnosa keperawatan pada pasien tersebut','Ansietas'),(48,'2.1.1',1,'Bagaimana kondisi pasien minimal care yang dapat dilakukan komunikasi interpersonal?\r\n','-kesadaran kompos menthis\r\n-bahasa yang digunakan dipahami oleh kedua belah \r\n pihak\r\n-tidak ada hambatana atau gangguan komunikasi\r\n-status psikologis stabil\r\n'),(49,'2.1.1',2,'Jika pasien yang anda kelola memiliki hambatan komunikasi, apa yang anda lakukan?','Lapor PJ Shift atau kepala ruangan'),(50,'2.1.1',3,'Yang termasuk hambatan komunikasi','-pasien berkebutuhan khusus seperti pasien tunarungu, tuna daksa, tuna netra, tuna grahita, dll\r\n-pasien warga negara asing yang tidak bisa berbahasa indonesia\r\n'),(51,'2.2.1',1,'Sebutkan tehnik komunikasi yang dapat dilakukan pada pasien dengan minimal care?','-tehnik komunikasi terapeutik\r\n-tehnik komunikasi interpersonal\r\n'),(52,'2.2.1',2,'Sebutkan tehnik komunikasi yang dapat digunakan untuk melakukan komunikasi interpersonal pada pasien minimal care?','-Dengarkan dengan penuh perhatian\r\n-Gunakan bahasa sederhana yang mudah dipahami\r\n-Menerima dan mendengarkan pasien\r\n'),(53,'2.2.1',3,'Apa yang dimaksud dengan dengarkan pasien penuh perhatian?','Mendengarkan pasien secara aktif, meliputi gestur  verbal dan non verbal. Misalnya : menganggukkan kepala, menunjukkan ketertarikan pada pembicaraan pasien, memhami dan aktif mendengarkan pasien'),(54,'2.2.1',4,'Apa yang dilakukan perawat saat melakukan tehnik komunikasi menerima dan mendengarkan pasien?','Perawat wajib memberikan reaksi dia mengerti ucapan pasien, meski tak harus menyetujui nya. Reaksi ini misalnya dengan kontak mata atau dengan ucapan : “Ya saya mengerti”'),(55,'2.3.1',1,'Sebutkan faktor yang dapat mempengaruhi  komunikasi pada pasien dengan minimal care?','Persepsi, nilai, emosi, latar belakang sosial budaya, pengetahuan, peran hubungan dan kondisi lingkungan'),(56,'2.3.1',2,'Hal hal apa saja yang harus diperhatikan dalam berkomunikasi dengan pasien minimal care?','Gunakan bahasa yang mudah dipahami dan dimengerti oleh pasien'),(57,'2.3.1',3,'Bagaimana komunikasi perawat agar dapat meningkatkan patisent wellbeing terutama pada pasien minimal care?','Gunakan bahasa yang lembut, intonasi suara yang rendah, dan ekspresi wajah yang senang saat mendatangi pasien'),(58,'4.1.1',1,'Sebutkan pasien2 yang beresiko terjadi infeksi HAIs?','1. neonatus dan orang lanjut usia lebih rentan.\r\n2. status imun yang rendah/terganggu (immuno- \r\n   compromised)\r\n3. gangguan/Interupsi barier anatomis (post op, luka \r\n   bakar,dll)\r\n4. implantasi benda asing\r\n'),(59,'4.2.1',1,'Dari  enam komponen rantai penularan infeksi, terdapat agen infeksi, apakah yang dimaksud?','Agen infeksi adalah mikroorganisme penyebab infeksi. Pada manusia, agen infeksi dapat berupa bakteri, virus, jamur dan parasit'),(60,'4.3.1',1,'Sebutkan 5 moment hand hygiene?','1. Sebelum kontak pasien\r\n2. Sebelum tindakan aseptik\r\n3. Setelah kontak darah dan cairan tubuh\r\n4. Setelah kontak pasien\r\n5. Setelah kontak dengan lingkungan sekitar pasien\r\n'),(61,'5.1.1',1,'Jelaskan cara memberikan tanda/gelang pada pasien yang beresiko jatuh?','1. Jelaskan maksud dan tujuan pemasangan gelang \r\nidentifikasi resiko jatuh\r\n2. Pasangkan gelang identifikasi resiko jatuh pada \r\npergelangan tangan pasien\r\n3. Infromasikan gelang tidak boleh di lepas selama \r\nmasa perawatan\r\n'),(62,'7.1.1',1,'Sebutkan 5 tanda-tanda vital klien ?','1. Tekanan darah\r\n2. Nadi\r\n3. Respirasi\r\n4. Suhu\r\n5. Nyeri\r\n'),(63,'7.4.1',1,'Bila hasil jumlah skoring EWS 4, tentukan level resiko klinisnya, warna dan frekuensi monitoring?','Resiko klinis: rendah, warna : hijau\r\nMinimal tiap 4-6 jam  atau 2x/shift\r\n'),(64,'9.1.1',1,'Apa yang dimaksud dengan parenteral nutrisi?','1. parenteral nutrisi adalah mempersiapkan dan \r\n   memberikan nutrisi melalui pembuluh darah vena \r\n   baik sentral (unutk nutrisi parenteral total) \r\n   atau vena perifer (unutk nutrisi parenteral \r\n   parsial) pada pasien yang tidak dapat memenuhi \r\n  '),(65,'9.1.1',2,'Sebutkan jenis elektrolit pekat?','2. elektrolit pekat\r\n-KCl\r\n-MgSO4\r\n-Natrium Bicarbonat\r\n-NaCl 3%\r\n'),(66,'10.1.1',1,'Apa tujuan perawatan luka?','-Mencegah masuknya microorganism ke dalam luka\r\n-Memberi rasa aman dan nyaman kepada pasien\r\n-Mengevalusi tingkat penyembuhan luka\r\n'),(67,'10.4.1',1,'Sebutkan proses penyembuhan luka?','-Fase inflamasi\r\n-Fase proliferasi\r\n-Fase maturasi/remodeling \r\n'),(68,'11.2.1',1,'Seorang pasien membutuhkan obat paracetamol 250 mg, sedian obat 500 mg,berapa jumlah dosis tablet yang diberikan?','Dosis yg harus diberikan 250/500 mg x 1 tablet\r\nDosis yang harus diberikan 0,5 tablet \r\n');
/*!40000 ALTER TABLE `form3_c` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form3_d`
--

DROP TABLE IF EXISTS `form3_d`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form3_d` (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_kuk` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `doc_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `no_kuk` (`no_kuk`,`doc_id`),
  KEY `doc_id` (`doc_id`),
  CONSTRAINT `form3_d_ibfk_1` FOREIGN KEY (`no_kuk`) REFERENCES `kuk_form3` (`no_kuk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form3_d_ibfk_2` FOREIGN KEY (`doc_id`) REFERENCES `doc_kuk_form3` (`doc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form3_d`
--

LOCK TABLES `form3_d` WRITE;
/*!40000 ALTER TABLE `form3_d` DISABLE KEYS */;
INSERT INTO `form3_d` VALUES (1,'1.4',1),(2,'1.5',1),(8,'10.5',4),(9,'11.4',5),(10,'12.1',1),(3,'2.3',1),(4,'3.2',1),(5,'7.4',2),(6,'8.6',1),(7,'9.4',3);
/*!40000 ALTER TABLE `form3_d` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_1`
--

DROP TABLE IF EXISTS `form_1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_1` (
  `form_1_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `asesi_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asesi_date` date DEFAULT NULL,
  `asesor_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asesor_date` date DEFAULT NULL,
  `no_reg` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Waiting','Approved','Cancel','') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ijazah_id` int DEFAULT NULL,
  `spk_id` int DEFAULT NULL,
  `sip_id` int DEFAULT NULL,
  `str_id` int DEFAULT NULL,
  `ujikom_id` int DEFAULT NULL,
  `sertifikat_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`form_1_id`),
  UNIQUE KEY `no_reg` (`no_reg`),
  UNIQUE KEY `ijazah_id_2` (`ijazah_id`,`spk_id`,`sip_id`,`str_id`,`ujikom_id`,`sertifikat_id`),
  KEY `ijazah_id` (`ijazah_id`,`spk_id`,`sip_id`,`str_id`,`ujikom_id`),
  KEY `transkrip_id` (`spk_id`),
  KEY `str_id` (`str_id`),
  KEY `ujikom_id` (`ujikom_id`),
  KEY `sip_id` (`sip_id`),
  KEY `sertifikat_id` (`sertifikat_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `form_1_ibfk_1` FOREIGN KEY (`ijazah_id`) REFERENCES `users_ijazah_file` (`ijazah_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_1_ibfk_2` FOREIGN KEY (`spk_id`) REFERENCES `users_spk_file` (`spk_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_1_ibfk_3` FOREIGN KEY (`str_id`) REFERENCES `users_str_file` (`str_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_1_ibfk_4` FOREIGN KEY (`ujikom_id`) REFERENCES `users_ujikom_file` (`ujikom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_1_ibfk_5` FOREIGN KEY (`sip_id`) REFERENCES `users_sip_file` (`sip_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_1_ibfk_6` FOREIGN KEY (`sertifikat_id`) REFERENCES `users_sertifikat_pendukung` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_1_ibfk_7` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_1_ibfk_8` FOREIGN KEY (`no_reg`) REFERENCES `data_asesor` (`no_reg`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_1`
--

LOCK TABLES `form_1` WRITE;
/*!40000 ALTER TABLE `form_1` DISABLE KEYS */;
INSERT INTO `form_1` VALUES (51,23,'Testing','2025-04-29','asesor_testing','2025-04-29','ASK.123456','Waiting',38,NULL,16,12,14,23,'2025-04-29 04:30:03','2025-04-29 06:53:52');
/*!40000 ALTER TABLE `form_1` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_2`
--

DROP TABLE IF EXISTS `form_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_2` (
  `form_2_id` int NOT NULL AUTO_INCREMENT,
  `user_jawab_form_2_id` int DEFAULT NULL,
  `penilaian_asesi` decimal(10,2) DEFAULT NULL,
  `asesi_date` date DEFAULT NULL,
  `asesor_date` date DEFAULT NULL,
  `no_reg` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asesi_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asesor_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Approved','Cancel') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`form_2_id`),
  UNIQUE KEY `jawab_form_2_id` (`user_jawab_form_2_id`),
  UNIQUE KEY `no_reg_2` (`no_reg`),
  UNIQUE KEY `user_jawab_form_2_id` (`user_jawab_form_2_id`),
  KEY `no_reg` (`no_reg`),
  KEY `jawab_form_2_id_2` (`user_jawab_form_2_id`),
  CONSTRAINT `form_2_ibfk_2` FOREIGN KEY (`no_reg`) REFERENCES `form_1` (`no_reg`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_2_ibfk_3` FOREIGN KEY (`user_jawab_form_2_id`) REFERENCES `jawaban_form_2` (`user_jawab_form_2_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_2`
--

LOCK TABLES `form_2` WRITE;
/*!40000 ALTER TABLE `form_2` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `form_3`
--

DROP TABLE IF EXISTS `form_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `form_3` (
  `form_3_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `asesi_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asesi_date` date NOT NULL,
  `asesor_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asesor_date` date NOT NULL,
  `no_reg` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Approved') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`form_3_id`),
  KEY `no_reg` (`no_reg`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `form_3_ibfk_1` FOREIGN KEY (`no_reg`) REFERENCES `form_2` (`no_reg`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `form_3_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `form_2` (`user_jawab_form_2_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `form_3`
--

LOCK TABLES `form_3` WRITE;
/*!40000 ALTER TABLE `form_3` DISABLE KEYS */;
/*!40000 ALTER TABLE `form_3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history_jabatan_user`
--

DROP TABLE IF EXISTS `history_jabatan_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `history_jabatan_user` (
  `user_jabatan_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `jabatan_id` int DEFAULT NULL,
  `working_unit_id` int DEFAULT NULL,
  `dari` date DEFAULT NULL,
  `sampai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_jabatan_id`),
  KEY `user_id` (`user_id`,`jabatan_id`),
  KEY `jabatan_id` (`jabatan_id`),
  KEY `working_unit_id` (`working_unit_id`),
  CONSTRAINT `history_jabatan_user_ibfk_1` FOREIGN KEY (`jabatan_id`) REFERENCES `jabatan` (`jabatan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `history_jabatan_user_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `history_jabatan_user_ibfk_3` FOREIGN KEY (`working_unit_id`) REFERENCES `working_unit` (`working_unit_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history_jabatan_user`
--

LOCK TABLES `history_jabatan_user` WRITE;
/*!40000 ALTER TABLE `history_jabatan_user` DISABLE KEYS */;
INSERT INTO `history_jabatan_user` VALUES (9,23,5,2,'2025-02-12','2023-02-12','2025-03-26 12:15:46','2025-03-26 12:17:17'),(10,26,2,4,NULL,NULL,'2025-03-26 15:24:24','2025-03-26 15:25:14'),(11,25,2,4,NULL,NULL,'2025-03-29 08:43:20','2025-03-29 08:43:20'),(12,23,2,1,'2025-04-30','2025-04-30','2025-04-29 02:01:54','2025-04-29 02:01:54');
/*!40000 ALTER TABLE `history_jabatan_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `iuk_form3`
--

DROP TABLE IF EXISTS `iuk_form3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `iuk_form3` (
  `iuk_form3_id` int NOT NULL AUTO_INCREMENT,
  `no_kuk` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_iuk` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `iuk_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`iuk_form3_id`),
  UNIQUE KEY `no_iuk` (`no_iuk`),
  KEY `no_kuk` (`no_kuk`),
  CONSTRAINT `iuk_form3_ibfk_1` FOREIGN KEY (`no_kuk`) REFERENCES `kuk_form3` (`no_kuk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `iuk_form3`
--

LOCK TABLES `iuk_form3` WRITE;
/*!40000 ALTER TABLE `iuk_form3` DISABLE KEYS */;
INSERT INTO `iuk_form3` VALUES (1,'1.1','1.1.1','Menjelaskan respon biopsikososial / manifestasi klinik yang diperhatikan oleh pasien dengan minimal care (P)'),(2,'1.1','1.1.2','Menjelaskan jenis pengkajian pada pasien minimal care'),(3,'1.2','1.2.1','Menyiapkan formulir pengkajian sesuai standar / dapat melalui sistem EMR  (K,S)'),(4,'1.2','1.2.2','Menjelaskan tentang alat / skala sesuai dengan metode pengkajian (P)'),(5,'1.2','1.2.3','Memastikan alat pengukuran sudah terkalibrasi (K)'),(6,'1.3','1.3.1','Melakukan pengkajian secara komprehensif dan sistematis pada pasien dengan minimal care (K-S)'),(7,'1.3','1.3.2','Menjelaskan jenis data objektif dan subjektif pada pasien dengan minimal care (P)'),(8,'1.3','1.3.3','Menjelaskan sumber data pengkajian pada pasien dengan minimal care (P)'),(9,'1.4','1.4.1','Menjelaskan prinsip yang perlu diperhatikan dan di justifikasinya pada pasien minimal care (P)'),(10,'1.4','1.4.2','Mengobservasi kondisi dan respon pasien selama pengumpulan data pada pasien dengan minimal care (K,S)'),(11,'1.5','1.5.1','Mengelompok kan data dan identifikasi data fokus pada pasien dengan minimal care (P)'),(12,'1.5','1.5.2','Mengidentifikasi masalah keperawatan hasil analisis pada pasien dengan minimal care (P)'),(13,'1.5','1.5.3','Merumuskan diagnosis keperawatan sesuai dengan kewenangan pada pasien dengan minimal care (P)'),(14,'2.1','2.1.1','Menjelaskan tentang kondisi pasien yang dapat dilakukan komunikasi pada pasien dengan minimal care (P)'),(15,'2.2','2.2.1','Melakukan pengkajian terkait kebutuhan komunikasi pada pasien dengan minimal care (K)'),(16,'2.2','2.2.2','Menjelaskan berbagai teknik komunikasi sesuai kondisi pasien (P)'),(17,'2.3','2.3.1','Melakukan teknik komunikasi interpersonal dengan tahapan pra interaksi, interaksi dan terminasi dalam melakukan tindakan keperawatan (K)'),(18,'2.3','2.3.2','Mengidentifikasi kondisi dan respon pasien selama berinteraksi dengan pasien dengan minimal care (K,S)'),(19,'2.3','2.3.3','Menjelaskan faktor – faktor yang mempengaruhi proses komunikasi pada pasien dengan minimal care (P)'),(20,'2.4','2.4.1','Menjelaskan prinsip komunikasi dilakukan sesuai dengan kondisi pasien dengan minimal care (S)'),(21,'3.1','3.1.1','Menjelaskan masalah – masalah etik dalam kontek asuhan keperawatan pada pasien dengan minimal care (P)'),(22,'3.1','3.1.2','Menjelaskan faktor yang mempengaruhi resiko masalah dilema etik (P)'),(23,'3.1','3.1.3','Mengidentifikasi kondisi pasien yang beresiko masalah dan dilema etik (P)'),(24,'3.2','3.2.1','Menjelaskan prinsip moral etik selama berhubungan dengan pasien dengan minimal care (P)'),(25,'3.2','3.2.2','Menjelaskan perbedaan aspek etik dengan aspek legal dalam asuhan keperawatan pada pasien dengan minimal care (P)'),(26,'3.2','3.2.3','Memperhatikan peran model perilaku etik selama asuhan keperawatan pada pasien dengan minimal care (K)'),(27,'3.3','3.3.1','Menerapkan sikap empati, sabar, respek, dan dan sopan (S)'),(28,'3.3','3.3.2','Mengidentifikasi prinsip moral etik sesuai kondisi pasien dengan minimal care (K)'),(29,'3.3','3.3.3','Mendemonstrasikan prilaku etis sesuai prinsip moral selama asuhan keperawatan pasien dengan minimal care (K,S)'),(30,'4.1','4.1.1','Mengidentifikasi pasien-pasien yang beresiko terjadi infeksi'),(31,'4.2','4.2.1','Menguraikan rantai penularan infeksi dan menganalisa sumber, penyebab dan cara penyebaran infeksi'),(32,'4.3','4.3.1','Menjelaskan 5 momen hand hygiene'),(33,'4.3','4.3.2','Melakukan 6 langkah cuci tangan dengan benar'),(34,'4.3','4.3.3','Melakukan pengelolaan sampah infeksius dan benda tajam'),(35,'4.3','4.3.4','Menerapkan prinsip pemakaian APD'),(36,'4.4','4.4.1','Menjelaskan tentang transmisi droplet '),(37,'4.4','4.4.2','Menjelaskan tentang kebutuhan ruang isolasi pasien'),(38,'5.1','5.1.1','Menjelaskan tentang skreening pasien beresiko jatuh'),(39,'5.1','5.1.2','Menjelaskan cara memberikan tanda pada pasien yang beresiko jatuh'),(40,'5.2','5.2.1','Menjelaskan kepada keluarga tentang tindakan pencegahan jatuh pasien sesuai hasil pengkajian'),(41,'5.3','5,3,1','Melakukan evaluasi hasil dari pencegahan jatuh'),(42,'6.1','6.1.1','Melakukan pengkajian dan skreeining resiko cedera tekan pada pasien'),(43,'6.2','6.2.1','Persiapkan alat untuk mencegah cedera tekan sesuai identifikasi'),(44,'6.3','6.3.1','Melakukan tindakan pencegahan terjadinya cedera tekan'),(45,'7.1','7.1.1','Mampu menyiapkan set alat pengukuran tanda-tanda vital dan menyebutkan 5 tanda vital \r\n\r\n'),(46,'7.2','7.2.1','Mampu melakukan pengukuran suhu tubuh'),(47,'7.3','7.3.1','Mampu melakukan pengukuran tekanan darah'),(48,'7.4','7.4.1','Mampu melakukan pengukuran nadi'),(49,'7.5','7.5.1','Mampu melakukan pengukuran pernapasan'),(50,'7.6','7.6.1','Mampu melakukan pengukuran skala nyeri'),(51,'7.7','7.7.1','Mampu melaporkan hasil yang menyimpang kepada PJ shift/Kepala Ruangan'),(52,'7.8','7.8.1','Mampu mengevaluasi hasil pengukuran tanda-tanda vital sesuaidengan EWS'),(53,'8.1','8.1.1','Mampu mengidentifikasi alat dan bahan oksigen nasal kanul'),(54,'8.2','8.2.1','Mampu menjelaskan tujuan dan prosedur pemberian oksigen dengan nasal kanul'),(55,'8.3','8.3.1','Mampu melakukan posisi semi fowler'),(56,'8.4','8.4.1','Mampu melakukan suction oropharyngeal sesuai SPO'),(57,'8.5','8.5.1','Mampu memilih tehnik pemberian oksigen sesuai dengan kebutuhan (binasal kanul,  masker non rebreathing)'),(58,'8.6','8.6.1','Mampu mengevaluasi hasil setelah oksigen terpenuhi'),(59,'9.1','9.1.1','Mampu mengidentifikasi jenis cairan elektrolit pekat dan parenteral nutrisi'),(60,'9.2','9.2.1','Mampu melepas IV line'),(61,'9.3','9.3.1','Mampu memberikan makan/minum melalui NGT/OGT'),(62,'9.4','9.4.1','Mampu memonitor dan mendokumentasikan cairan dan elektrolit sesuai SPO'),(63,'10.1','10.1.1','Melakukan pengkajian kebutuhan perawatan luka kering (K),(S)'),(64,'10.2','10.2.1','Menjelaskan tujuan perawatan luka (P)'),(65,'10.3','10.3.1','Melakukan pengkajian kondisi luka (K), (S) '),(66,'10.4','10.4.1','Mengidentifikasi proses penyembuhan luka (P)'),(67,'10.5','10.5.1','Mampu melakukan perawatan luka kering  sesuia dengan SPO (K), (S)'),(68,'11.1','11.1.1','Melakukan verifikasi terhadap order dokter dan obat yang dissiapkan oleh Farmasi(K), (S)'),(69,'11.2','11.2.1','Melakukan perhitungan rasio dan formula obat (P)'),(70,'11.3','11.3.1','Mampu melakukan pemberian obat PO secara aman dan tepat sesuai dengan SPO (K),(S)'),(71,'11.4','11.4.1','Mampu mengevaluasi respon pemberian obat PO (P)'),(72,'12.1','12.1.1','Mampu melakukan permintaan darah ke Bank Darah (K),(S)'),(73,'12.2','12.2.1','Mengidentifikasi reaksi transfusi darah (P)'),(74,'12.3','12.3.1','Mampu menganalisa data yang teridentifikasi (P)'),(75,'12.4','12.4.1','Melakukan penanganan reaksi transfuse darah sesuai SPO (P)'),(76,'1.4','1.4.3','Merumuskan diagnosa keperawatan sesuai dengan kewenangan pada pasien dengan minimal care');
/*!40000 ALTER TABLE `iuk_form3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jabatan`
--

DROP TABLE IF EXISTS `jabatan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jabatan` (
  `jabatan_id` int NOT NULL AUTO_INCREMENT,
  `nama_jabatan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`jabatan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jabatan`
--

LOCK TABLES `jabatan` WRITE;
/*!40000 ALTER TABLE `jabatan` DISABLE KEYS */;
INSERT INTO `jabatan` VALUES (1,'Perawat pelaksana'),(2,'PJ SHIFT'),(3,'Preseptor'),(4,'Kepala ruangan'),(5,'Perawat manager');
/*!40000 ALTER TABLE `jabatan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jawaban_form_2`
--

DROP TABLE IF EXISTS `jawaban_form_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jawaban_form_2` (
  `jawab_form_2_id` int NOT NULL AUTO_INCREMENT,
  `user_jawab_form_2_id` int DEFAULT NULL,
  `no_id` int DEFAULT NULL,
  `k` tinyint(1) DEFAULT NULL,
  `bk` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`jawab_form_2_id`),
  KEY `user_jawab_form_2_id` (`user_jawab_form_2_id`),
  KEY `no_id` (`no_id`),
  CONSTRAINT `jawaban_form_2_ibfk_1` FOREIGN KEY (`no_id`) REFERENCES `soal_form_2` (`no_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `jawaban_form_2_ibfk_2` FOREIGN KEY (`user_jawab_form_2_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=674 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jawaban_form_2`
--

LOCK TABLES `jawaban_form_2` WRITE;
/*!40000 ALTER TABLE `jawaban_form_2` DISABLE KEYS */;
/*!40000 ALTER TABLE `jawaban_form_2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `komponen_form_2`
--

DROP TABLE IF EXISTS `komponen_form_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `komponen_form_2` (
  `id` int NOT NULL AUTO_INCREMENT,
  `komponen_id` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama_komponen` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_elemen` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `komponen_id` (`komponen_id`),
  KEY `no_elemen` (`no_elemen`),
  CONSTRAINT `komponen_form_2_ibfk_1` FOREIGN KEY (`no_elemen`) REFERENCES `elemen_kompetensi_form_2` (`no_elemen`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `komponen_form_2`
--

LOCK TABLES `komponen_form_2` WRITE;
/*!40000 ALTER TABLE `komponen_form_2` DISABLE KEYS */;
INSERT INTO `komponen_form_2` VALUES (1,'1.1','Pasien yang dilakukan pengkajian keperawatan diidentifikasi',1),(2,'1.2','Alat dan formula pengkajian diidentifikasi',1),(3,'1.3','Pengumpulan data secara komprehensif dilaksanakan\r\n',1),(4,'1.4','Prinsip pengumpulan data dilakukan\r\n\r\n',1),(5,'2.1','Pasien diidentifikasi interpersonal dalam melaksanakan tindakan keparawatan ',2),(6,'2.2','Kebutuhan komunikasi di identifikasi',2),(7,'2.3','Tahapan komunikasi dilakukan',2),(8,'2.4','Prinsip komunikasi dilakukan',2),(9,'3.1','Pasien dengan risiko masalah etik diidentifikasi',3),(10,'3.2','Prinsip etika dilakukan',3),(11,'4.1','Pasien diidentifikasi',4),(12,'4.2','Sumber penyebab dan cara penularan infeksi di identifikasi',4),(13,'4.3','Pencegahan infeksi berdasarkan kewaspadaan standar di lakukan',4),(14,'4.4','Pencegahan infeksi berdasarkan kewaspadaan transmisi di lakukan',4),(15,'5.1','Kondisi lingkungan pasien yang berisiko keselamatan pasien didentifikasi',5),(16,'5.2','Menciptakan dan memeliharan lingkungan yang aman untuk pasien dilakukan',5),(17,'5.3','Hasil tindakan pemeliharaan lingkungan yang aman dievaluasi',5),(18,'6.1','Kondisi pasien Beresiko cedera tekan di identifikasi  ',6),(19,'6.2','Alat dan bahan pencegahan cedera tekan di identifikasi',6),(20,'6.3','Tindakan pencegahan cedera tekan di lakukan',6),(21,'6.4','Hasil pencegahan cedera di evaluasi',6),(22,'7.1','Alat dan bahan pengukuran tanda -  tanda vital disiapkan, menggunakan skala EWS',7),(23,'7.2','Pengukuran suhu tubuh dilakukan',7),(24,'7.3','Pengukuran tekanan darah dilakukan',7),(25,'7.4','Pengukuran nadi dilakukan',7),(26,'7.5','Pengukuran pernapasan dilakukan',7),(27,'7.6','Pengukuran skala nyeri dilakukan',7),(28,'7.7','Jika terdapat hasil yang menyimpang dilaporkan kepada PJ Shift/Kepala Ruangan',7),(29,'7.8','Hasil pengukuran tanda-tanda vital di evaluasi sesuai hasil dari EWS',7),(30,'8.1','Kebutuhan oksigen nasal kanul diidentifikasi',8),(31,'8.2','Tujuan dan prosedur pemberian oksigen nasal kanul dijelaskan',8),(32,'8.3','Posisi semif owler dilakukan',8),(33,'8.4','Suction oropharyngeal dilakukan sesuai dengan SPO',8),(34,'8.5','Tehnik pemberian oksigen dipilih sesuai dengan Tingkat kebutuhan oksigen',8),(35,'8.6','Hasil pemenuhan oksigen dievaluasi',8),(36,'9.1','Jenis cairan elektrolit pekat dan parenteral nutrisi diidentifikasi',9),(37,'9.2','IV line dilepas',9),(38,'9.3','Pemberiaan makan/minum melalui NGT/OGT',9),(39,'9.4','Kondisi, keluhan dan respon pasien diobservasi',9),(40,'10.1','Kebutuhan perawatan luka bersih di identifikasi',10),(41,'10.2','Tujuan perawatan luka dijelaskan',10),(42,'10.3','Kondisi luka diidentifikasi',10),(43,'10.4','Proses penyembuhan luka diidentifikasi',10),(44,'10.5','Perawatan luka dilakukan sesuai SPO',10),(45,'11.1','Order dokter dan obat yang disiapkan oleh petugas Farmasi di verifikasi ',11),(46,'11.2','Rasio perhitungan obat dilakukan ',11),(47,'11.3','Memberikan obat PO secara aman dan sesuai dengan SPO',11),(48,'11.4','Respon pemberian obat di evaluasi ',11),(49,'12.1','Permintaan darah ke Bank Darah dilakukan',12),(50,'12.2','Reaksi tranfusi darah di identifikasi',12),(51,'12.3','Data reaksi tranfusi darah yang teridentifikasi dianalis',12),(52,'12.4','Melakukan penanganan reaksi transfuse darah sesuai SPO',12),(53,'1.5','Hasil pengkajian dianalisis',1),(54,'3.3','Tahapan penerapan etik dilakukan',3);
/*!40000 ALTER TABLE `komponen_form_2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kuk_form3`
--

DROP TABLE IF EXISTS `kuk_form3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kuk_form3` (
  `kuk_form3_id` int NOT NULL AUTO_INCREMENT,
  `no_elemen_form_3` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `no_kuk` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kuk_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`kuk_form3_id`),
  UNIQUE KEY `no_kuk` (`no_kuk`),
  KEY `no_elemen_form_3` (`no_elemen_form_3`),
  CONSTRAINT `kuk_form3_ibfk_1` FOREIGN KEY (`no_elemen_form_3`) REFERENCES `elemen_form_3` (`no_elemen_form_3`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kuk_form3`
--

LOCK TABLES `kuk_form3` WRITE;
/*!40000 ALTER TABLE `kuk_form3` DISABLE KEYS */;
INSERT INTO `kuk_form3` VALUES (1,'1','1.1','Pasien yang dilakukan pengkajian keperawatan di identifikasi'),(2,'1','1.2','Alat dan formulir pengkajian diidentifikasi'),(3,'1','1.3','Pengumpulan data secara komprehensif dilaksanakan'),(4,'1','1.4','Prinsip pengumpulan data dilakukan'),(5,'1','1.5','Hasil pengkajian dianalisis'),(6,'2','2.1','Pasien diidentifikasi interpersonal dalam melaksanakan '),(7,'2','2.2','Kebutuhan komunikasi diidentifikasi'),(8,'2','2.3','Tahapan komunikasi dilakukan'),(9,'2','2.4','Prinsip komunikasi dilakukan'),(10,'3','3.1','Pasien dengan risiko masalah etik diidentifikasi'),(11,'3','3.2','Prinsip etika – etika dilakukan'),(12,'3','3.3','Tahapan penerapan etik dilakukan '),(13,'4','4.1','Pasien diidentifikasi'),(14,'4','4.2','Sumber penyebab dan cara penularan infeksi di identifikasi'),(15,'4','4.3','Pencegahan infeksi berdasarkan kewaspadaan standar di lakukan'),(16,'4','4.4','Pencegahan infeksi berdasarkan kewaspadaan transmisi di lakukan'),(17,'5','5.1','Kondisi lingkungan pasien yang berisiko keselamatan pasien diidentifikasi'),(18,'5','5.2','Menciptakan dan memeliharan lingkungan yang aman untuk pasien dilakukan'),(19,'5','5.3','Hasil tindakan pemeliharaan lingkungan yang aman dievaluasi'),(20,'6','6.1','Kondisi pasien Beresiko cedera di identifikasi  '),(21,'6','6.2','Alat dan bahan pencegahan cedera di identifikasi'),(22,'6','6.3','Tindakan pencegahan cedera di lakukan'),(23,'7','7.1','Alat dan bahan pengukuran tanda -  tanda vital disiapkan, menggunakan skala EWS'),(24,'7','7.2','Pengukuran suhu tubuh dilakukan'),(25,'7','7.3','Pengukuran tekanan darah dilakukan'),(26,'7','7.4','Pengukuran nadi dilakukan'),(27,'7','7.5','Pengukuran pernapasan dilakukan'),(28,'7','7.7','Jika terdapat hasil yang menyimpang dilaporkan kepada PJ Shift/Kepala Ruangan'),(29,'7','7.8','Hasil pengukuran tanda-tanda vital di evaluasi sesuai hasil dari EWS'),(30,'8','8.1','Alat dan bahan oksigen nasal kanul diidentifikasi'),(31,'8','8.2','Tujuan dan prosedur pemberian oksigen nasal kanul dijelaskan'),(32,'8','8.3','Posisi semi fowler dilakukan'),(33,'8','8.4','Suction oropharyngeal  dilakukan sesuai dengan SPO'),(34,'8','8.5','Tehnik pemberian oksigen dipilih sesuai dengan Tingkat kebutuhan oksigen'),(35,'8','8.6','Hasil pemenuhan oksigen dievaluasi'),(36,'9','9.1','Jeniscairan elektrolit pekat dan parenteral nutrisi diidentifikasi'),(37,'9','9.2','IV line dilepas'),(38,'9','9.3','Pemberikan makan/minum melalui NGT/OGT'),(39,'9','9.4','Monitor dan dokumentasi cairan dan elektrolit sesuai SPO'),(40,'10','10.1','Kebutuhan perawatan luka kering diidentifikasi dengan tepat'),(41,'10','10.2','Tujuan perawatan luka dijelaskan'),(42,'10','10.3','Kondisi luka diidentifikasi'),(43,'10','10.4','Proses penyembuhan luka diidentifikasi'),(44,'10','10.5','Perawatan luka dilakukan sesuai SPO'),(45,'11','11.1','Order dokter dan obat yang disiapkan oleh Farmasi diverifikasi  pemberian obat PO secara aman dan tepat diidentifikasi'),(46,'11','11.2','Rasio dan formula perhitungan obat dilakukan '),(47,'11','11.3','Pemberian obat PO secara aman dan tepat dilakukan  sesuai dengan SPO'),(48,'11','11.4','Respon pemberian obat di evaluasi'),(49,'12','12.1','Permintaan darah ke Bank Darah dilakukan'),(50,'12','12.2','Reaksi tranfusi darah diidentifikasi'),(51,'12','12.3','Data reaksi tranfusi darah yang teridentifikasi dianalis'),(52,'12','12.4','Penanganan reaksi transfuse darah sesuai SPO'),(53,'7','7.6','Pengukuran skala nyeri dilakukan');
/*!40000 ALTER TABLE `kuk_form3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_telp` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `otp` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `validate_otp_password` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_telp` (`no_telp`),
  KEY `no_telp_2` (`no_telp`),
  CONSTRAINT `password_reset_ibfk_1` FOREIGN KEY (`no_telp`) REFERENCES `users_otps` (`no_telp`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset`
--

LOCK TABLES `password_reset` WRITE;
/*!40000 ALTER TABLE `password_reset` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pk_progress`
--

DROP TABLE IF EXISTS `pk_progress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pk_progress` (
  `progress_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `form_1_id` int DEFAULT NULL,
  `form_2_id` int DEFAULT NULL,
  `form_3_id` int DEFAULT NULL,
  `form_5_id` int DEFAULT NULL,
  `form_6_id` int DEFAULT NULL,
  `form_7_id` int DEFAULT NULL,
  `form_8_id` int DEFAULT NULL,
  `form_9_id` int DEFAULT NULL,
  `form_10_id` int DEFAULT NULL,
  `form_11_id` int DEFAULT NULL,
  `form_12_id` int DEFAULT NULL,
  PRIMARY KEY (`progress_id`),
  KEY `form_1_id` (`form_1_id`),
  KEY `form_2_id` (`form_2_id`),
  KEY `user_id` (`user_id`),
  KEY `form_3_id` (`form_3_id`),
  CONSTRAINT `pk_progress_ibfk_1` FOREIGN KEY (`form_1_id`) REFERENCES `form_1` (`form_1_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pk_progress_ibfk_2` FOREIGN KEY (`form_2_id`) REFERENCES `form_2` (`form_2_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pk_progress_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pk_progress_ibfk_4` FOREIGN KEY (`form_3_id`) REFERENCES `form_3` (`form_3_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pk_progress`
--

LOCK TABLES `pk_progress` WRITE;
/*!40000 ALTER TABLE `pk_progress` DISABLE KEYS */;
INSERT INTO `pk_progress` VALUES (6,23,51,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `pk_progress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pk_status`
--

DROP TABLE IF EXISTS `pk_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pk_status` (
  `status_id` int NOT NULL AUTO_INCREMENT,
  `progress_id` int DEFAULT NULL,
  `form_1_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_2_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_3_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_4_status` enum('Open','Complted') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_5_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_6_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_7_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_8_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_9_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_10_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_11_status` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `form_12_staus` enum('Open','Completed') COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`status_id`),
  KEY `progress_id` (`progress_id`),
  CONSTRAINT `pk_status_ibfk_1` FOREIGN KEY (`progress_id`) REFERENCES `pk_progress` (`progress_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pk_status`
--

LOCK TABLES `pk_status` WRITE;
/*!40000 ALTER TABLE `pk_status` DISABLE KEYS */;
INSERT INTO `pk_status` VALUES (6,6,'Open',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `pk_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poin_tabel_form3`
--

DROP TABLE IF EXISTS `poin_tabel_form3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `poin_tabel_form3` (
  `poin_id` int NOT NULL AUTO_INCREMENT,
  `no_poin` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `poin_diamati` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`poin_id`),
  KEY `no_poin` (`no_poin`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poin_tabel_form3`
--

LOCK TABLES `poin_tabel_form3` WRITE;
/*!40000 ALTER TABLE `poin_tabel_form3` DISABLE KEYS */;
INSERT INTO `poin_tabel_form3` VALUES (1,'1A','Kelengkapan dokumen / formulir sesuai kebutuhan'),(2,'1A','Akses EMR'),(3,'1A','Kelengkapan alat dan bahan sesuai kebutuhan'),(4,'1A','Kebersihan, kerapihan'),(5,'1B','Pengecekan terhadap kesiapan alat dan bahan sesuai kebutuhan dilakukan  melalui formulir kalibrasi'),(6,'1C','Metode pengkajian, Head to toe atau persistem dilakukan sistematis'),(7,'1C','Memperkenalkan diri'),(8,'1C','Pengumpulan data primer dan sekunder dilakukan'),(9,'1C','Menggunakan bahasa yang mudah dimengerti dan dipahami'),(10,'1C','Alat dan bahan yang disiapkan digunakan'),(11,'1C','Kebersihan dan kerapihan'),(12,'1D','Menanyakan kondisi pasien saat melakukan pengkajian '),(13,'1D','Menanyakan respon pasien saat melakukan pengkajian'),(14,'1D','Bahasa yang digunakan mudah dimengerti dan dipahami oleh pasien dan keluarga'),(15,'2A','Memperkenalkan diri'),(16,'2A','Melakukan pengkajian terhadap kemungkinan hambatan komunikasi'),(17,'2A','Bahasa yang digunakan mudah dimengerti dan dipahami oleh pasien dan keluarga'),(18,'2B','Mengeksplorasi perasaan, harapan dan kecemasan diri sendiri'),(19,'2B','Menganalisis kekuatan dan kelemahan diri perawat sendiri'),(20,'2B','Mengumpulkan data tentang klien'),(21,'2B','Merencanakan pertemuan pertama dengan klien'),(22,'2B','Memperkenalkan diri '),(23,'2B','Membina hubungan saling percaya '),(25,'2B','Merumuskan kontrak bersama dengan pasien'),(26,'2B','Menggali pikiran perasaan dan mengidentifikasi masalah'),(27,'2B','Merumuskan tujuan dengan pasien'),(28,'2B','Perawat dan pasien bekerja sama untuk mengatasi masalah yang dihadapi pasien'),(29,'2B','Melakukan evaluasi pencapaian tujuan dari interaksi yang telah dilaksanakan'),(30,'2B','Melakukan evaluasi subjektif'),(31,'2B','Menyepakati tindak lanjut dari interaksi yang telah dilakukan'),(32,'2B','Membuat kontrak urtuk pertemuan berikutnya'),(33,'2C','Bahasa yang digunakan mudah dipahami dan dimengerti oleh pasien'),(34,'2C','Bahasa dan gestur tubuh posituf'),(35,'2C','Senyum sapa salam sopan dan santun'),(36,'2C','Menghargai privasi pasien'),(37,'3A','Menghargai privasi pasien'),(38,'3A','Sopan, santun'),(39,'3A','Memberi kesempatan kepada pasien untuk bertanya'),(40,'3A','Menepati janji sesuai kontrak waktu'),(41,'4A','6 langkah cuci tangan sesuai SPO  dengan sabun dan air mengalir atau SPO cuci tangan dengan alkohol'),(42,'4B','Pembuangan sampah medis dan non medis'),(43,'4B','Pembuangan sampah benda tajam'),(44,'5A','Cara memberikan tanda pada pasien yang beresiko jatuh sesuai dengan SPO'),(45,'6A','Assesmen pasien resiko jatuh sesuai dengan kebutuhan dan SPO'),(46,'7A','Alat alat untuk mengukur 5 tanda vital sesuai SPO'),(47,'7B','Melakukan pengukuran suhu tubuh sesuai SPO'),(48,'7C','Melakukan pengukuran tekanan darah tubuh sesuai SPO'),(49,'7D','Melakukan pengukuran nadi sesuai SPO'),(50,'7E','Melakukan pengukuran pernapasan sesuai SPO'),(51,'7F','Melakukan pengukuran skala nyeri sesuai SPO'),(52,'7G','Melihat hasil pengukuran 5 tanda vital di avicena'),(53,'8A','Alat-alat untuk pemasangan oksigen nasal kanul'),(54,'8B','Melakukan pemasangan nasal kanul sesuai SPO'),(55,'8C','Melakukan suction sesuai SPO'),(56,'8D','Melakukan komunikasi dengan klien evaluasi respon'),(57,'8D','Melakukan evaluasi hasil setelah oksigen terpenuhi tercatat di avicena'),(58,'10A','Memverifikasi order dokter '),(59,'10A','Memeriksa kondisi fisik : keadaan umum pasien'),(60,'10A','Mengkaji kondisi balutan '),(61,'10B','Mengkaji kondisi luka : luas atau diameter luka, keadaan kulit, dan perkembangan luka '),(62,'10B','Mengobservasi tanda tanda infeksi '),(63,'10C','Melakukan perawatan luka kering sesuai dengan SPO ');
/*!40000 ALTER TABLE `poin_tabel_form3` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schedule_interview`
--

DROP TABLE IF EXISTS `schedule_interview`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `schedule_interview` (
  `interview_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `asesi_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `asesor_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `place` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`interview_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schedule_interview`
--

LOCK TABLES `schedule_interview` WRITE;
/*!40000 ALTER TABLE `schedule_interview` DISABLE KEYS */;
/*!40000 ALTER TABLE `schedule_interview` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `soal_form_2`
--

DROP TABLE IF EXISTS `soal_form_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `soal_form_2` (
  `no_id` int NOT NULL AUTO_INCREMENT,
  `komponen_id` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sub_komponen_id` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `daftar_pertanyaan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`no_id`),
  UNIQUE KEY `sub_komponen_id` (`sub_komponen_id`),
  UNIQUE KEY `sub_komponen_id_2` (`sub_komponen_id`),
  KEY `komponen_id` (`komponen_id`),
  CONSTRAINT `soal_form_2_ibfk_1` FOREIGN KEY (`komponen_id`) REFERENCES `komponen_form_2` (`komponen_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `soal_form_2`
--

LOCK TABLES `soal_form_2` WRITE;
/*!40000 ALTER TABLE `soal_form_2` DISABLE KEYS */;
INSERT INTO `soal_form_2` VALUES (1,'1.1',NULL,'Apakah anda mampu menjelaskan respon biopsikososial atau manifestasi klinik yang diperhatikan oleh pasien dengan minimal care?'),(2,'1.2','1.2.1','Apakah anda mampu menyiapkan formulir pengkajian sesuai standar atau dapat melalui sistem EMR?'),(3,'1.2','1.2.2','Apakah anda mampu menjelaskan tentang alat atau skala sesuai dengan metode pengkajian?'),(4,'1.2','1.2.3 ','Apakah anda mampu memastikan alat pengkuran sudah terkalibrasi?'),(5,'1.3','1.3.1','Melakukan pengkajian secara komprehensif dan sistematis pada pasien dengan minimal care (K-S).'),(6,'1.3','1.3.2','Menjelaskan jenis data objektif dan subjektif pada pasien dengan minimal care (P).\r\n'),(7,'1.4','1.4.1','Apakah anda  mampu menjelaskan prinsip yang perlu diperhatikan dan dijustifikasinya pada pasien minimal care?'),(8,'1.4','1.4.2','Apakah anda mampu melakukan obeservasi kondisi dan respon pasien selama pengumpulan data pada pasien dengan minimal care?'),(9,'1.5','1.5.1','Apakah anda mampu mengelompokkan data dan identifikasi data fokus pada pasien dengan minimal care?'),(10,'2.1',NULL,'Apakah anda mampu menjelaskan tentang kondisi pasien yang dapat dilakukan komunikasi pada pasien dengan minimal care?'),(11,'2.2','2.2.1','Apakah anda mampu melakukan pengkajian terkait kebutuhan komunikasi pada pasien dengan minimal care?'),(12,'2.2','2.2.2','Apakah anda mampu menjelaskan berbagai tehnik komunikasi sesuai kondisi pasien?'),(13,'2.3','2.3.1','Apakah anda mampu melakukan tehnik komunikasi interpersonal dengan tahapan pra interaksi, interaksi, kerja dan terminasi dalam melakukan tindakan keperawatan?'),(14,'2.3','2.3.2','Apakah anda mampu mengidentifikasi kondisi dan respon pasien selama berinteraksi dengan pasien minimal care?'),(15,'2.3','2.3.3','Apakah anda mampu menjelaskan faktor faktor yang mempengaruhi proses komunikasi pada pasien dengan minimal care?'),(16,'2.4',NULL,'Apakah anda mampu menjelaskan prisnip komunikasi dilakukan sesuai dengan kondisi pasien minimal care?'),(17,'3.1','3.1.1','Apakah anda mampu menjelaskan masalah masalah etik dalam kontek asuhan keperawatan pada pasien dengan minimal care?'),(18,'3.1','3.1.2','Apakah menjelaskan faktor yang mempengaruhi resiko masalah dilema etik?'),(19,'3.1','3.1.3','Apakah anda mampu  mengidentifikasi kondisi pasien yang beresiko masalah dan dilema etik?'),(20,'3.2','3.2.1','Apakah anda mampu menjelaskan prinsip moral etik selma berhubungan dengan pasien minimal care ?'),(21,'3.2','3.2.2','Anda mampu menjelaskan prinsip moral etik selma berhubungan dengan pasien minimal care?'),(22,'4.1',NULL,'Apakah anda mampu mengidentifikasi pasien-pasien yang beresiko terjadi infeksi?'),(23,'4.2',NULL,'Apakah anda mengetahui penyebab dan cara penularan infeksi?'),(24,'4.3',NULL,'Apakah anda mampu melakukan pencegahan infeksi berdasarkan standar yang ada?'),(25,'4.4',NULL,'Apakah anda mampu mengetahui pencegahan infeksi berdasarkan kewaspadaan transmisi?'),(26,'5.1',NULL,'Apakah anda dapat mengidentifikasi linkungan pasien yang beresiko terhadap keselamatan pasien?'),(27,'5.2',NULL,'Apakah anda dapat menyebutkan intervensi dalam pemeliharaan lingkungan yang aman untuk pasien?'),(28,'5.3',NULL,'Apakah anda dapat melakukan evaluasi dari intervensi yang sudah diberikan dalam pemeliharaan lingkungan yang aman untuk pasien?'),(29,'6.1',NULL,'Apakah anda mampu mengidentifikasi pasien-pasien yang beresiko cidera tekan?'),(30,'6.2',NULL,'Apakah anda dapat mengidentifikasi alat dan bahan yang diperlukan dalam pencegahan cidera tekan?'),(31,'6.3',NULL,'Apakah anda mampu melakukan tindakan pencegahan cidera?'),(32,'6.4',NULL,'Apakah anda mampu mengevaluasi hasil pencegahan cidera tekan?'),(33,'7.1',NULL,'Apakah anda mampu mempersiapkan  alat dan bahan pengukuran tanda -  tanda vital disiapkan, menggunakan skala EWS?'),(34,'7.2',NULL,'Apakah anda mampu melakukan Pengukuran suhu tubuh? '),(35,'7.3',NULL,'Apakah anda mampu melakukan Pengukuran  tekanan darah?'),(36,'7.4',NULL,'Apakah anda mampu melakukan Pengukuran nadi?'),(37,'7.5',NULL,'Apakah anda mampu melakukan Pengukuran  pernapasan?'),(38,'7.6',NULL,'Apakah anda mampu melakukan Pengukuran  skala nyeri?'),(39,'7.7',NULL,'Apakah anda mampu melaporkan Jika terdapat hasil yang menyimpang kepada PJ Shift/Kepala Ruangan?'),(40,'7.8',NULL,'Apakah anda mampu mengevaluasi Hasil pengukuran tanda-tanda vital sesuai hasil dari EWS?'),(41,'8.1',NULL,'Apakah anda mampu mengidentifikasi pemenuhan oksigen nasal kanul?'),(42,'8.2',NULL,'Apakah anda mampu menjelaskan tujuan dan prosedur pemberian oksigen nasal kanul?'),(43,'8.3',NULL,'Apakah anda mampu melakukan posisi semi fowler/fowler?'),(44,'8.4',NULL,'Apakah anda mampu melakukan suction Oropharyngeal sesuai dengan SPO?'),(45,'8.5',NULL,'Apakah anda mampu memberikan oksigen sesuai dengan Tingkat kebutuhan pasien?'),(46,'8.6',NULL,'Apakah anda mampu mengevaluasi pemenuhan oksigen?'),(47,'9.1',NULL,'Apakah anda mampu mengidentifikasi jenis cairan elektrolit pekat dan parenteral nutrisi?'),(48,'9.2',NULL,'Apakah anda mampu melepas IV line?'),(49,'9.3',NULL,'Apakah anda mampu memberikan makan atau minum melalui NGT atau OGT?'),(50,'9.4',NULL,'Apakah anda mampu melakukan observasi terhadap kondisi dan respon pasien selama pemberian cairan dan elektrolit?'),(51,'10.1',NULL,'Apakah anda mampu mengidentifikasi kebutuhan pasien dengan perawatan luka?'),(52,'10.2',NULL,'Apakah anda mampu menjelaskan tujuan perawatan luka?'),(53,'10.3',NULL,'Apakah anda mampu mengkaji kondisi luka?'),(54,'10.4',NULL,'Apakah anda mampu mengidentifikasi proses penyembuhan luka?'),(55,'10.5',NULL,'Apakah anda mampu melakukan perawatan luka sesuai SPO ?'),(56,'11.1',NULL,'Apakah anda mampu memverifikasi order dokter dan obat yang disiapkan petugas farmasi?'),(57,'11.2',NULL,'Apakah anda mampu melakukan perhitungan rasio dan formula obat?'),(58,'11.3',NULL,'Apakah anda mampu memberikan obat secara aman dan tepat sesuai dengan SPO?'),(59,'11.4',NULL,'Apakah anda mampu mengevaluasi respon setelah pemberian obat?'),(60,'12.1',NULL,'Apakah anda mampu melakukan permintaan darah ke Bank Darah?'),(61,'12.2',NULL,'Apakah anda mampu mengidentifikasi rekasi transfusi darah?'),(65,'1.3','1.3.3','Menjelaskan sumber data pengkajian pada pasien dengan minimal care (P).'),(66,'1.5','1.5.2','Apakah anda mampu mengidentifikasi masalah keperawatan hasil analisis pada pasien dengan minimal care?'),(67,'1.5','1.5.3','Apakah anda mampu merumuskan diagnosa keperawatan sesuai dengan kewenangan pada pasien dengan minimal care?'),(68,'3.3','3.3.1','Menerapkan sikap empati, sabar, respek, dan dan sopan (S).\r\n'),(69,'3.3','3.3.2','Mengidentifikasi prinsip moral etik sesuai kondisi pasien dengan minimal care (K).\r\n'),(70,'3.3','3.3.3','Mendemonstrasikan prilaku etis sesuai prinsip moral selama asuhan keperawatan pasien dengan minimal care (K,S).\r\n');
/*!40000 ALTER TABLE `soal_form_2` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role` (
  `role_id` int NOT NULL,
  `role_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (1,'Asesi'),(2,'Asesor'),(3,'Bidang');
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `nik` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tempat_lahir` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `kewarganegaraan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pendidikan` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_lulus` year DEFAULT NULL,
  `provinsi` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kota` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kode_pos` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `no_telp` varchar(13) COLLATE utf8mb4_general_ci NOT NULL,
  `role_id` int DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `token` text COLLATE utf8mb4_general_ci,
  `device_token` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `nik` (`nik`,`email`,`no_telp`),
  UNIQUE KEY `no_telp` (`no_telp`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `user_role` (`role_id`) ON UPDATE CASCADE,
  CONSTRAINT `users_ibfk_3` FOREIGN KEY (`no_telp`) REFERENCES `users_otps` (`no_telp`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (23,'12345678','$2y$10$y4l/erJhBpdFFE1IH0K96ujoFd6nU2BhB3R6k.faR92V7P/3hmGei','Testing','Bandung','2025-02-28','Indonesiaa','L','S2',2020,'Jawa Barat','Bandung','Jl.Kopo','40232','testing@mailnesia.com','089898154551',1,'foto_nurse/tA0t9Ua8Ggy4RfB3KxEX6edE15UgJnq6Lb9nQ5NI.jpg','2025-02-13 17:43:26','2025-04-29 06:02:25','eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FwcC5yc2ltbWFudWVsLm5ldDo5MDkxL2xvZ2luLWFrdW4iLCJpYXQiOjE3NDU5MDY1NDUsImV4cCI6MTc0NTkxMDE0NSwibmJmIjoxNzQ1OTA2NTQ1LCJqdGkiOiJXQ2lMMktpalJqYUlpQnd2Iiwic3ViIjoiMjMiLCJwcnYiOiIzODliYmRmNjI3ZjYwNDc5ODdhNDUzMmExNzdmYTY1MWRhMDQ1YTMxIn0.-9xCzpeDllA_Eq-r2ZWXz8zXINkbMInAYe5fX0zDNqo',NULL),(24,'102','$2y$10$Y4H8m1GoM5C5eBF1gRJYc.pQij0mmhMlYhWQFzLfALyvnsRvF8XWm','asesor_testing',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'123@gmail.com','08966579000',2,'foto_nurse/jzNIuob0TvfyT6c6O2EUHdTUjNB9lQKn0xH0AqyR.png','2025-02-23 23:15:14','2025-04-23 03:33:10','eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FwcC5yc2ltbWFudWVsLm5ldDo5MDkxL2xvZ2luLWFrdW4iLCJpYXQiOjE3NDUzNzkxOTAsImV4cCI6MTc0NTM4Mjc5MCwibmJmIjoxNzQ1Mzc5MTkwLCJqdGkiOiJCNmVJM3cxVDFvdnp5RlhCIiwic3ViIjoiMjQiLCJwcnYiOiIzODliYmRmNjI3ZjYwNDc5ODdhNDUzMmExNzdmYTY1MWRhMDQ1YTMxIn0.Hr_jFueHa6Moo1AR_tRpDilB1y-CBlKx_EaqVe8cBxs',NULL),(25,'103','$2y$10$TT2P0w0uZCgn8YRw/bm9OO7OSROIEH6dY0iG8XaGCPhLgviC.j9p.','bidang_testing','Bandung',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'321@gmail.com','099999999',3,'foto_nurse/574paLjdP8JKSH2T3IkFkuvW1XGvbUanwLSZDJAU.png','2025-02-23 23:17:03','2025-04-29 06:02:42','eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2FwcC5yc2ltbWFudWVsLm5ldDo5MDkxL2xvZ2luLWFrdW4iLCJpYXQiOjE3NDU5MDY1NjIsImV4cCI6MTc0NTkxMDE2MiwibmJmIjoxNzQ1OTA2NTYyLCJqdGkiOiJyMzhHWndjOGxnOVd3WGViIiwic3ViIjoiMjUiLCJwcnYiOiIzODliYmRmNjI3ZjYwNDc5ODdhNDUzMmExNzdmYTY1MWRhMDQ1YTMxIn0.uPVdswQ_iEsu9V4LwVqbk7IjHJEbG3OLGCYDbzygZ1U',NULL),(26,'240070','$2y$10$I7I1NlmEsqby3uNOTWkDseFntqOw2i/Wyd3eDUvefoo1OtgOc73gW','INI BUDI','BANDUNG','1999-03-25','INDONESIA','L','S1 Teknik Informatika',2024,'JAWA BARAT','BANDUNG','jalan jajan no 1','40133','testing29@gmail.com','1234567',1,'foto_nurse/qHYH83L8K2QFRm19WDdV9jpVPP4HG19bqhJZXb03.jpg','2025-03-25 06:37:56','2025-04-02 09:16:16','eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgzL2xvZ2luLWFrdW4iLCJpYXQiOjE3NDM1ODUzNzYsImV4cCI6MTc0MzU4ODk3NiwibmJmIjoxNzQzNTg1Mzc2LCJqdGkiOiJzVlliODV1cFV4WnoyOTlhIiwic3ViIjoiMjYiLCJwcnYiOiIzODliYmRmNjI3ZjYwNDc5ODdhNDUzMmExNzdmYTY1MWRhMDQ1YTMxIn0.DvoqNBFOVYykRC9X9L5g29A6IO4a3ZW-QYTda8s9-Yc',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_ijazah_file`
--

DROP TABLE IF EXISTS `users_ijazah_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_ijazah_file` (
  `ijazah_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `path_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `authentic` tinyint(1) DEFAULT NULL,
  `current` tinyint(1) DEFAULT NULL,
  `sufficient` tinyint(1) DEFAULT NULL,
  `ket` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ijazah_id`),
  UNIQUE KEY `user_id_2` (`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_ijazah_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_ijazah_file`
--

LOCK TABLES `users_ijazah_file` WRITE;
/*!40000 ALTER TABLE `users_ijazah_file` DISABLE KEYS */;
INSERT INTO `users_ijazah_file` VALUES (38,23,'ijazah/1745387326_dummy.pdf',NULL,NULL,NULL,NULL,NULL,'2025-02-27 00:37:08','2025-04-23 05:48:46'),(39,24,'ijazah/1740639178_dummy.pdf',NULL,NULL,NULL,NULL,NULL,'2025-02-27 06:52:58','2025-02-27 06:52:58'),(40,26,'ijazah/1742884962_Laporan Pembelajaran Avicenna_ 24 Maret 2025.pdf',1,1,1,1,NULL,'2025-03-25 06:42:42','2025-03-25 06:42:42');
/*!40000 ALTER TABLE `users_ijazah_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_otps`
--

DROP TABLE IF EXISTS `users_otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_otps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_telp` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `otp` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `validate_otp` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_telp` (`no_telp`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_otps`
--

LOCK TABLES `users_otps` WRITE;
/*!40000 ALTER TABLE `users_otps` DISABLE KEYS */;
INSERT INTO `users_otps` VALUES (66,'089898154551',NULL,'2025-02-24 05:33:26','2025-04-23 06:04:13','2025-02-24 05:33:26',1),(67,'08966579000',NULL,'2025-02-25 04:33:48','2025-02-25 04:33:48','2025-02-25 04:33:48',1),(68,'099999999',NULL,'2025-02-25 04:34:08','2025-03-29 08:43:20','2025-02-25 04:34:08',1),(69,'08989812345','176590','2025-02-27 06:59:56','2025-02-27 06:59:56','2025-02-27 07:04:56',0),(70,'1234567','385844','2025-03-25 06:35:47','2025-03-29 08:51:08','2025-03-25 06:42:25',1),(71,'12345678','544735','2025-03-29 14:28:22','2025-03-29 14:28:22','2025-03-29 14:33:22',0),(74,'123456789','123456','2025-03-19 14:29:35','2025-03-21 14:29:35','2025-03-31 14:29:35',1),(75,'00000','123456','2025-03-29 15:14:12','2025-03-29 15:14:32','2025-03-29 15:14:12',1),(77,'08989815453','778077','2025-04-29 02:36:55','2025-04-29 02:36:55','2025-04-29 02:41:55',0),(78,'0898981545','652794','2025-04-29 03:11:43','2025-04-29 03:12:08','2025-04-29 03:17:08',0);
/*!40000 ALTER TABLE `users_otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_sertifikat_pendukung`
--

DROP TABLE IF EXISTS `users_sertifikat_pendukung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_sertifikat_pendukung` (
  `sertifikat_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `path_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `authentic` tinyint(1) DEFAULT NULL,
  `current` tinyint(1) DEFAULT NULL,
  `sufficient` tinyint(1) DEFAULT NULL,
  `ket` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor_sertifikat` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `masa_berlaku_sertifikat` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sertifikat_id`),
  KEY `user_id_2` (`user_id`),
  CONSTRAINT `users_sertifikat_pendukung_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_sertifikat_pendukung`
--

LOCK TABLES `users_sertifikat_pendukung` WRITE;
/*!40000 ALTER TABLE `users_sertifikat_pendukung` DISABLE KEYS */;
INSERT INTO `users_sertifikat_pendukung` VALUES (18,23,'Sertifikat/1742910282_Dokumentasi API ImmaForDoctor.pdf',NULL,NULL,NULL,NULL,NULL,'SER000000000','2000-01-02','2025-03-25 13:44:42','2025-03-25 13:44:42');
/*!40000 ALTER TABLE `users_sertifikat_pendukung` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_sip_file`
--

DROP TABLE IF EXISTS `users_sip_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_sip_file` (
  `sip_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `path_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `authentic` tinyint(1) DEFAULT NULL,
  `current` tinyint(1) DEFAULT NULL,
  `sufficient` tinyint(1) DEFAULT NULL,
  `ket` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor_sip` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `masa_berlaku_sip` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sip_id`),
  UNIQUE KEY `user_id_2` (`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_sip_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_sip_file`
--

LOCK TABLES `users_sip_file` WRITE;
/*!40000 ALTER TABLE `users_sip_file` DISABLE KEYS */;
INSERT INTO `users_sip_file` VALUES (15,26,'Sip/1742996179_Capture.PNG',1,1,1,1,NULL,'SIP-001242','2025-01-06','2025-03-25 06:42:48','2025-03-26 13:36:19'),(16,23,'Sip/1745885415_dummy.pdf',NULL,NULL,NULL,NULL,NULL,'12345678','2025-04-30','2025-04-29 00:09:28','2025-04-29 00:10:15');
/*!40000 ALTER TABLE `users_sip_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_spk_file`
--

DROP TABLE IF EXISTS `users_spk_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_spk_file` (
  `spk_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `path_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `authentic` tinyint(1) DEFAULT NULL,
  `current` tinyint(1) DEFAULT NULL,
  `sufficient` tinyint(1) DEFAULT NULL,
  `ket` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor_spk` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `masa_berlaku_spk` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`spk_id`),
  UNIQUE KEY `user_id_2` (`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_spk_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_spk_file`
--

LOCK TABLES `users_spk_file` WRITE;
/*!40000 ALTER TABLE `users_spk_file` DISABLE KEYS */;
INSERT INTO `users_spk_file` VALUES (12,23,'Spk/1745885497_dummy.pdf',NULL,NULL,NULL,NULL,NULL,'12345678','2025-04-27','2025-03-25 12:32:47','2025-04-29 00:11:37');
/*!40000 ALTER TABLE `users_spk_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_str_file`
--

DROP TABLE IF EXISTS `users_str_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_str_file` (
  `str_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `path_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `authentic` tinyint(1) DEFAULT NULL,
  `current` tinyint(1) DEFAULT NULL,
  `sufficient` tinyint(1) DEFAULT NULL,
  `ket` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor_str` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `masa_berlaku_str` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`str_id`),
  UNIQUE KEY `user_id_2` (`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_str_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_str_file`
--

LOCK TABLES `users_str_file` WRITE;
/*!40000 ALTER TABLE `users_str_file` DISABLE KEYS */;
INSERT INTO `users_str_file` VALUES (12,23,'Str/1745828445_dummy.pdf',NULL,NULL,NULL,NULL,NULL,'12345678','2025-04-30','2025-02-27 02:19:45','2025-04-28 08:20:45'),(13,26,'Str/1742884966_Laporan Pembelajaran Avicenna_ 24 Maret 2025.pdf',1,1,1,1,NULL,NULL,NULL,'2025-03-25 06:42:46','2025-03-25 06:42:46');
/*!40000 ALTER TABLE `users_str_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_ujikom_file`
--

DROP TABLE IF EXISTS `users_ujikom_file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_ujikom_file` (
  `ujikom_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `path_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid` tinyint(1) DEFAULT NULL,
  `authentic` tinyint(1) DEFAULT NULL,
  `current` tinyint(1) DEFAULT NULL,
  `sufficient` tinyint(1) DEFAULT NULL,
  `ket` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nomor_kompetensi` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `masa_berlaku_kompetensi` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ujikom_id`),
  UNIQUE KEY `user_id_2` (`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `users_ujikom_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_ujikom_file`
--

LOCK TABLES `users_ujikom_file` WRITE;
/*!40000 ALTER TABLE `users_ujikom_file` DISABLE KEYS */;
INSERT INTO `users_ujikom_file` VALUES (14,23,'Ujikom/1742909477_[op[op[p[po[op.PNG',1,1,1,1,NULL,'KOM-212','2014-11-15','2025-02-27 07:01:02','2025-03-25 13:31:17'),(15,26,'Ujikom/1742885041_Laporan Pembelajaran Avicenna_ 24 Maret 2025.pdf',1,1,1,1,NULL,NULL,NULL,'2025-03-25 06:42:50','2025-03-25 06:44:01');
/*!40000 ALTER TABLE `users_ujikom_file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `working_area`
--

DROP TABLE IF EXISTS `working_area`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `working_area` (
  `working_area_id` int NOT NULL AUTO_INCREMENT,
  `working_area_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`working_area_id`),
  CONSTRAINT `working_area_ibfk_1` FOREIGN KEY (`working_area_id`) REFERENCES `working_unit` (`working_area_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `working_area`
--

LOCK TABLES `working_area` WRITE;
/*!40000 ALTER TABLE `working_area` DISABLE KEYS */;
INSERT INTO `working_area` VALUES (1,'Area Keperawatan Medikal Bedah '),(2,'Area keperawatan Anak'),(3,'Area Keperawatan Intensif '),(4,'Area keperawatan Gawat Darurat'),(5,'Area Keperawatan Dialisis'),(6,'Area Keperawatan Kamar Bedah '),(7,'Area Keperawatan Maternitas '),(8,'Area keperawatan Rawat Jalan'),(9,'Area Keperawatan Extramural & PKRS'),(10,'Komite Keperawatan'),(11,'Bidang Keperawatan'),(12,'PPI RS'),(13,'Komite Mutu RS'),(14,'SPI'),(15,'Endoscopy'),(16,'USG'),(17,'CSSD'),(18,'Independent'),(19,'Tidak Ada');
/*!40000 ALTER TABLE `working_area` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `working_unit`
--

DROP TABLE IF EXISTS `working_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `working_unit` (
  `working_unit_id` int NOT NULL,
  `working_unit_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `working_area_id` int DEFAULT NULL,
  PRIMARY KEY (`working_unit_id`),
  KEY `working_area_id` (`working_area_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `working_unit`
--

LOCK TABLES `working_unit` WRITE;
/*!40000 ALTER TABLE `working_unit` DISABLE KEYS */;
INSERT INTO `working_unit` VALUES (1,'Kamar Bedah / Anesthesi',6),(2,'Komite Keperawatan',10),(3,'Komite Mutu RS',13),(4,'PK RS',18),(5,'Ponek',19),(6,'PPI RS',12),(7,'R. Beria',1),(8,'R. Clement',2),(9,'Bidang Penunjang Medis',18),(10,'CSSD',17),(11,'Endoscopy',15),(12,'Extramural',9),(13,'IGD & Ambulans',4),(14,'Instalasi Dialisis',5),(15,'R. Debora',7),(16,'R. Elizabeth',1),(17,'R. Filipus',1),(18,'R. Gideon',1),(19,'R. Hana & Yokhebeth',1),(20,'R. HCU',3),(21,'R. HCU & Cathlab',3),(22,'R. ICU',3),(23,'R. LC A',2),(24,'R. LC B',2),(25,'Alkema LT. IV',7),(26,'Alkema LT. V',1),(27,'Alkema LT. VI',1),(28,'Alkema LT. VII',2),(29,'Alkema LT. VIII',1),(30,'Bidang Keperawatan',11),(31,'R. Lukas',1),(32,'R. Lukas & Nazareth',1),(33,'R. Nazareth',1),(34,'R. NICU & PICU',3),(35,'R. Obaja',1),(36,'R. Petra',1),(37,'Rajal Leimena',8),(38,'Rajal PD',8),(39,'SPI',14),(40,'USG',16);
/*!40000 ALTER TABLE `working_unit` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-29 13:55:23
