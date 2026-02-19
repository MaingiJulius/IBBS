-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: IBBS_PROTOTYPE
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_time` datetime NOT NULL DEFAULT current_timestamp(),
  `seat_number` varchar(10) NOT NULL,
  `booking_status` enum('CONFIRMED','CANCELLED','PAID','CHECKED_IN') DEFAULT 'PAID',
  `qr_token` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `passenger_name` varchar(255) NOT NULL DEFAULT '',
  `passenger_age` int(11) NOT NULL DEFAULT 0,
  `passenger_id_number` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`booking_id`),
  UNIQUE KEY `unique_booking` (`route_id`,`seat_number`),
  KEY `user_id` (`user_id`),
  KEY `bus_id` (`bus_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (1,'2026-06-05 20:00:00','2A','PAID','e6233292db93c159fca80cd558ffbfd9',22,2,2,'',0,''),(2,'2026-10-03 15:00:00','3A','PAID','0db1515fc46edd6e578051bcd29375b6',23,3,3,'',0,''),(3,'2026-02-16 20:00:00','4A','PAID','730e6e438d5f966a527f4f538f132003',24,4,4,'',0,''),(4,'2026-04-26 14:00:00','5A','PAID','66fc8bc02fd8ac40242e137f1830bfb9',25,5,5,'',0,''),(5,'2026-03-27 12:00:00','6A','CANCELLED','622d6ca0678c67e7fdd6a7d368465fa4',26,6,6,'',0,''),(6,'2026-02-04 13:00:00','7A','PAID','26f3029aeed588071f58b38de5757653',27,7,7,'',0,''),(7,'2026-09-05 20:00:00','8A','PAID','dc5486d63381738a637789a92b8085fa',28,8,8,'',0,''),(8,'2026-04-20 16:00:00','9A','PAID','71b059ece856b4830ff8068af95eec29',29,9,9,'',0,''),(9,'2026-06-05 15:00:00','10A','PAID','4477b9b52f1ae4f00c21a4ea55af147a',30,10,10,'',0,''),(10,'2026-12-24 10:00:00','11A','PAID','8a51b1f62ed3c7abd8fd96a67b744c63',21,11,11,'',0,''),(11,'2026-07-03 19:00:00','12A','PAID','eabec05090e648641aca30b6dc9dde54',22,12,12,'',0,''),(12,'2026-09-18 09:00:00','13A','PAID','0688f64043a983e55793317de1d00730',23,13,13,'',0,''),(13,'2026-06-20 14:00:00','14A','PAID','61d1e57c0708a4edc51066ae81067bac',24,14,14,'',0,''),(14,'2026-06-19 17:00:00','15A','PAID','0d5ba3994aa82106ff5e852aa841f395',25,15,15,'',0,''),(15,'2026-04-14 08:00:00','16A','PAID','52a4dd281e934256937909057dad588c',26,16,16,'',0,''),(16,'2026-11-11 12:00:00','17A','PAID','32fe983757e7eb28580a7a519bc5cd7a',27,17,17,'',0,''),(17,'2026-01-11 10:00:00','18A','PAID','373db2a35abc3e3b8b1f6a9eaf88b9bf',28,18,18,'',0,''),(18,'2026-08-20 19:00:00','19A','PAID','31400a08f0666ae9067b670816f3a7b7',29,19,19,'',0,''),(19,'2026-06-01 08:00:00','20A','PAID','747d0627ee743985911357a1bb6c2185',30,20,20,'',0,''),(20,'2026-07-14 16:00:00','21A','PAID','da61c9e0d991ec6df279d64349ac43b8',21,21,21,'',0,''),(21,'2026-06-22 14:00:00','22A','PAID','8d9c0cd02e567801f2a89bcdbd17a720',22,22,22,'',0,''),(22,'2026-08-24 12:00:00','23A','PAID','99ac1e189752a4d8b44304de6cd6bbe9',23,23,23,'',0,''),(23,'2026-07-22 14:00:00','24A','PAID','98cd95edd59baf6a2333c93be1ecd4b8',24,24,24,'',0,''),(24,'2026-11-18 14:00:00','25A','PAID','7c100301a8e2ff2b11e6df1d95730ba5',25,25,25,'',0,''),(25,'2026-01-05 17:00:00','26A','PAID','149cf7b391f41ebd4e0151b3c1619c62',26,26,26,'',0,''),(26,'2026-06-23 16:00:00','27A','PAID','46f3d5ae88f84387aa08bdf6afd424ed',27,27,27,'',0,''),(27,'2026-10-01 11:00:00','28A','PAID','a23cc6ba5f5ab6e6b4d1d044f30a245d',28,28,28,'',0,''),(28,'2026-08-04 08:00:00','29A','PAID','7f89ebecb0dc36b7d2f55fd2480667e1',29,29,29,'',0,''),(29,'2026-04-21 13:00:00','30A','PAID','5095b52c5c74e2c358469a1b4fd6755b',30,30,30,'',0,''),(30,'2026-09-19 14:00:00','31A','PAID','e6b951a4a4eb2d2543f08052046565d1',21,1,1,'',0,''),(31,'2026-01-13 20:28:15','S2','PAID','fb5707b999db456785aa02fb07afc61b',21,14,14,'',0,''),(32,'2026-01-13 21:33:26','S23','PAID','c0311852f231eb7af08c6381bc6a4395',27,30,30,'Roy',43,'343222'),(33,'2026-01-19 12:02:23','S16','PAID','e88e3984ea27a4c2b573ec198d934407',22,1,1,'Bin Clinton',23,'31522846');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `buses`
--

DROP TABLE IF EXISTS `buses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `buses` (
  `bus_id` int(11) NOT NULL AUTO_INCREMENT,
  `reg_no` varchar(20) NOT NULL,
  `bus_name` varchar(50) DEFAULT NULL,
  `max_passengers` int(11) NOT NULL,
  `seat_layout` varchar(20) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`bus_id`),
  UNIQUE KEY `reg_no` (`reg_no`),
  KEY `driver_id` (`driver_id`),
  CONSTRAINT `buses_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `buses`
--

LOCK TABLES `buses` WRITE;
/*!40000 ALTER TABLE `buses` DISABLE KEYS */;
INSERT INTO `buses` VALUES (1,'KWI 435Z','Wema Executive 1',40,'2x2',1),(2,'KIP 893K','Wema Executive 2',40,'2x2',2),(3,'KHN 719W','Wema Executive 3',40,'2x2',3),(4,'KOY 346P','Wema Executive 4',40,'2x2',4),(5,'UPP 520A','Wema Executive 5',40,'2x2',5),(6,'UUH 503W','Wema Executive 6',40,'2x2',6),(7,'T 435 VWM','Wema Executive 7',40,'2x2',7),(8,'T 155 UII','Wema Executive 8',40,'2x2',8),(9,'RLR 718 H','Wema Executive 9',40,'2x2',9),(10,'SIJ 854 GP','Wema Executive 10',40,'2x2',10),(11,'KJI 112Z','Wema Executive 11',40,'2x2',11),(12,'KOT 120F','Wema Executive 12',40,'2x2',12),(13,'KJX 893M','Wema Executive 13',40,'2x2',13),(14,'KAL 785Y','Wema Executive 14',40,'2x2',14),(15,'UAP 465F','Wema Executive 15',40,'2x2',15),(16,'UTK 299E','Wema Executive 16',40,'2x2',16),(17,'T 175 CCH','Wema Executive 17',40,'2x2',17),(18,'T 676 VKM','Wema Executive 18',40,'2x2',18),(19,'RQI 605 S','Wema Executive 19',40,'2x2',19),(20,'ENM 318 GP','Wema Executive 20',40,'2x2',20),(21,'KHJ 537G','Wema Executive 21',40,'2x2',21),(22,'KZQ 699X','Wema Executive 22',40,'2x2',22),(23,'KUI 133D','Wema Executive 23',40,'2x2',23),(24,'KIS 653J','Wema Executive 24',40,'2x2',24),(25,'UIH 545W','Wema Executive 25',40,'2x2',25),(26,'UOF 319L','Wema Executive 26',40,'2x2',26),(27,'T 788 UOC','Wema Executive 27',40,'2x2',27),(28,'T 909 NSY','Wema Executive 28',40,'2x2',28),(29,'RFA 598 Q','Wema Executive 29',40,'2x2',29),(30,'YET 839 GP','Wema Executive 30',40,'2x2',30);
/*!40000 ALTER TABLE `buses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `drivers`
--

DROP TABLE IF EXISTS `drivers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL AUTO_INCREMENT,
  `national_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`driver_id`),
  UNIQUE KEY `national_id` (`national_id`),
  UNIQUE KEY `phone` (`phone`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `drivers`
--

LOCK TABLES `drivers` WRITE;
/*!40000 ALTER TABLE `drivers` DISABLE KEYS */;
INSERT INTO `drivers` VALUES (1,'ID00000001','John Mwangi','0733000000','john.mwangi@gmail.com'),(2,'ID00000002','Samuel Okello','0733000001','samuel.okello@gmail.com'),(3,'ID00000003','David Mengistu','0733000002','david.mengistu@gmail.com'),(4,'ID00000004','Mohammed Hassan','0733000003','mohammed.hassan@gmail.com'),(5,'ID00000005','Peter Kamau','0733000004','peter.kamau@gmail.com'),(6,'ID00000006','James Omondi','0733000005','james.omondi@gmail.com'),(7,'ID00000007','Benson Kiprop','0733000006','benson.kiprop@gmail.com'),(8,'ID00000008','Charles Odhiambo','0733000007','charles.odhiambo@gmail.com'),(9,'ID00000009','Joseph Kariuki','0733000008','joseph.kariuki@gmail.com'),(10,'ID00000010','Thomas Njoroge','0733000009','thomas.njoroge@gmail.com'),(11,'ID00000011','Emmanuel Abebe','0733000010','emmanuel.abebe@gmail.com'),(12,'ID00000012','Isaac Tesfaye','0733000011','isaac.tesfaye@gmail.com'),(13,'ID00000013','Gabriel Selassie','0733000012','gabriel.selassie@gmail.com'),(14,'ID00000014','Michael Afewerki','0733000013','michael.afewerki@gmail.com'),(15,'ID00000015','Daniel Tekle','0733000014','daniel.tekle@gmail.com'),(16,'ID00000016','Richard Mwanza','0733000015','richard.mwanza@gmail.com'),(17,'ID00000017','Patrick Mutua','0733000016','patrick.mutua@gmail.com'),(18,'ID00000018','Stephen Musyoka','0733000017','stephen.musyoka@gmail.com'),(19,'ID00000019','George Otieno','0733000018','george.otieno@gmail.com'),(20,'ID00000020','Edward Wanyama','0733000019','edward.wanyama@gmail.com'),(21,'ID00000021','Brian Kibet','0733000020','brian.kibet@gmail.com'),(22,'ID00000022','Kevin Cheruiyot','0733000021','kevin.cheruiyot@gmail.com'),(23,'ID00000023','Dennis Kemboi','0733000022','dennis.kemboi@gmail.com'),(24,'ID00000024','Alex Rotich','0733000023','alex.rotich@gmail.com'),(25,'ID00000025','Felix Langat','0733000024','felix.langat@gmail.com'),(26,'ID00000026','Victor Ochieng','0733000025','victor.ochieng@gmail.com'),(27,'ID00000027','Collins Okeyo','0733000026','collins.okeyo@gmail.com'),(28,'ID00000028','Fredrick Owino','0733000027','fredrick.owino@gmail.com'),(29,'ID00000029','Walter Aketch','0733000028','walter.aketch@gmail.com'),(30,'ID00000030','Moses Odongo','0733000029','moses.odongo@gmail.com');
/*!40000 ALTER TABLE `drivers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL AUTO_INCREMENT,
  `rating` int(11) DEFAULT 5,
  `comments` text NOT NULL,
  `feedback_date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`feedback_id`),
  KEY `user_id` (`user_id`),
  KEY `bus_id` (`bus_id`),
  KEY `route_id` (`route_id`),
  CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`),
  CONSTRAINT `feedback_ibfk_3` FOREIGN KEY (`route_id`) REFERENCES `routes` (`route_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feedback`
--

LOCK TABLES `feedback` WRITE;
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
INSERT INTO `feedback` VALUES (1,4,'Charging ports were not working, please fix.','2026-12-11',22,2,2,'2026-01-13 16:42:56'),(2,3,'Charging ports were not working, please fix.','2026-08-04',23,3,3,'2026-01-13 16:42:56'),(3,5,'Great service, very comfortable bus.','2026-03-25',24,4,4,'2026-01-13 16:42:56'),(4,4,'Love the new buses, very modern and fresh.','2026-12-25',25,5,5,'2026-01-13 16:42:56'),(5,5,'Bus was a bit late but the ride was okay.','2026-02-08',26,6,6,'2026-01-13 16:42:56'),(6,5,'Excellent staff and safe driving. Will book again.','2026-07-17',27,7,7,'2026-01-13 16:42:56'),(7,5,'The driver drove carefully, I felt safe throughout.','2026-06-02',28,8,8,'2026-01-13 16:42:56'),(8,5,'Best travel experience I have had in a long time.','2026-06-28',29,9,9,'2026-01-13 16:42:56'),(9,5,'Excellent staff and safe driving. Will book again.','2026-09-26',30,10,10,'2026-01-13 16:42:56'),(10,3,'Enjoyed the free Wi-Fi, it was surprisingly fast.','2026-08-09',21,11,11,'2026-01-13 16:42:56'),(11,3,'The snacks provided were a nice touch.','2026-09-17',22,12,12,'2026-01-13 16:42:56'),(12,5,'Great service, very comfortable bus.','2026-07-18',23,13,13,'2026-01-13 16:42:56'),(13,4,'Enjoyed the free Wi-Fi, it was surprisingly fast.','2026-03-17',24,14,14,'2026-01-13 16:42:56'),(14,3,'Too many stops along the way made the trip longer.','2026-03-13',25,15,15,'2026-01-13 16:42:56'),(15,3,'Booking process was easy and the bus was on time.','2026-04-11',26,16,16,'2026-01-13 16:42:56'),(16,3,'Too many stops along the way made the trip longer.','2026-01-15',27,17,17,'2026-01-13 16:42:56'),(17,5,'The bus was noisy, could not sleep well.','2026-09-15',28,18,18,'2026-01-13 16:42:56'),(18,5,'Booking process was easy and the bus was on time.','2026-04-16',29,19,19,'2026-01-13 16:42:56'),(19,5,'Bus was a bit late but the ride was okay.','2026-07-14',30,20,20,'2026-01-13 16:42:56'),(20,3,'Booking process was easy and the bus was on time.','2026-03-27',21,21,21,'2026-01-13 16:42:56'),(21,5,'Excellent staff and safe driving. Will book again.','2026-01-11',22,22,22,'2026-01-13 16:42:56'),(22,4,'The journey was smooth and the driver was professional.','2026-08-25',23,23,23,'2026-01-13 16:42:56'),(23,3,'Enjoyed the free Wi-Fi, it was surprisingly fast.','2026-06-09',24,24,24,'2026-01-13 16:42:56'),(24,4,'Customer service was helpful when I needed to change my seat.','2026-02-18',25,25,25,'2026-01-13 16:42:56'),(25,5,'Great service, very comfortable bus.','2026-04-05',26,26,26,'2026-01-13 16:42:56'),(26,4,'The bus was noisy, could not sleep well.','2026-12-26',27,27,27,'2026-01-13 16:42:56'),(27,4,'Bus was a bit late but the ride was okay.','2026-10-01',28,28,28,'2026-01-13 16:42:56'),(28,5,'The journey was smooth and the driver was professional.','2026-07-06',29,29,29,'2026-01-13 16:42:56'),(29,3,'Arrived at the destination ahead of schedule!','2026-06-20',30,30,30,'2026-01-13 16:42:56'),(30,4,'Love the new buses, very modern and fresh.','2026-07-26',21,1,1,'2026-01-13 16:42:56');
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `routes`
--

DROP TABLE IF EXISTS `routes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `routes` (
  `route_id` int(11) NOT NULL AUTO_INCREMENT,
  `from_location` varchar(100) NOT NULL,
  `to_location` varchar(100) NOT NULL,
  `departure_date` date NOT NULL,
  `departure_time` time NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `status` enum('SCHEDULED','COMPLETED','CANCELLED') DEFAULT 'SCHEDULED',
  PRIMARY KEY (`route_id`),
  KEY `bus_id` (`bus_id`),
  CONSTRAINT `routes_ibfk_1` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`bus_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `routes`
--

LOCK TABLES `routes` WRITE;
/*!40000 ALTER TABLE `routes` DISABLE KEYS */;
INSERT INTO `routes` VALUES (1,'Nairobi, Kenya','Kampala, Uganda','2026-04-07','12:00:00',3500.00,1,'SCHEDULED'),(2,'Kampala, Uganda','Nairobi, Kenya','2026-06-14','15:10:00',3500.00,2,'SCHEDULED'),(3,'Nairobi, Kenya','Arusha, Tanzania','2026-11-25','14:10:00',2500.00,3,'SCHEDULED'),(4,'Arusha, Tanzania','Nairobi, Kenya','2026-09-20','20:40:00',2500.00,4,'SCHEDULED'),(5,'Johannesburg, SA','Asmara, Eritrea','2026-05-22','13:30:00',25000.00,5,'SCHEDULED'),(6,'Mombasa, Kenya','Dar es Salaam, Tanzania','2026-10-05','17:40:00',4000.00,6,'SCHEDULED'),(7,'Kigali, Rwanda','Kampala, Uganda','2026-11-03','13:40:00',2000.00,7,'SCHEDULED'),(8,'Bujumbura, Burundi','Kigali, Rwanda','2026-03-18','14:10:00',1500.00,8,'SCHEDULED'),(9,'Addis Ababa, Ethiopia','Nairobi, Kenya','2026-08-18','20:00:00',6000.00,9,'SCHEDULED'),(10,'Lusaka, Zambia','Dar es Salaam, Tanzania','2026-06-12','12:40:00',8000.00,10,'SCHEDULED'),(11,'Nairobi, Kenya','Kampala, Uganda','2026-11-15','07:10:00',3500.00,11,'SCHEDULED'),(12,'Kampala, Uganda','Nairobi, Kenya','2026-09-01','10:40:00',3500.00,12,'SCHEDULED'),(13,'Nairobi, Kenya','Arusha, Tanzania','2026-10-05','17:10:00',2500.00,13,'SCHEDULED'),(14,'Arusha, Tanzania','Nairobi, Kenya','2026-01-16','20:20:00',2500.00,14,'SCHEDULED'),(15,'Johannesburg, SA','Asmara, Eritrea','2026-05-25','10:40:00',25000.00,15,'SCHEDULED'),(16,'Mombasa, Kenya','Dar es Salaam, Tanzania','2026-01-20','20:10:00',4000.00,16,'SCHEDULED'),(17,'Kigali, Rwanda','Kampala, Uganda','2026-04-07','06:50:00',2000.00,17,'SCHEDULED'),(18,'Bujumbura, Burundi','Kigali, Rwanda','2026-03-26','06:50:00',1500.00,18,'SCHEDULED'),(19,'Addis Ababa, Ethiopia','Nairobi, Kenya','2026-10-26','10:20:00',6000.00,19,'SCHEDULED'),(20,'Lusaka, Zambia','Dar es Salaam, Tanzania','2026-04-12','15:10:00',8000.00,20,'SCHEDULED'),(21,'Nairobi, Kenya','Kampala, Uganda','2026-09-26','17:20:00',3500.00,21,'SCHEDULED'),(22,'Kampala, Uganda','Nairobi, Kenya','2026-10-24','16:10:00',3500.00,22,'SCHEDULED'),(23,'Nairobi, Kenya','Arusha, Tanzania','2026-09-05','09:30:00',2500.00,23,'SCHEDULED'),(24,'Arusha, Tanzania','Nairobi, Kenya','2026-11-04','09:10:00',2500.00,24,'SCHEDULED'),(25,'Johannesburg, SA','Asmara, Eritrea','2026-08-01','18:10:00',25000.00,25,'SCHEDULED'),(26,'Mombasa, Kenya','Dar es Salaam, Tanzania','2026-09-09','08:30:00',4000.00,26,'SCHEDULED'),(27,'Kigali, Rwanda','Kampala, Uganda','2026-09-11','20:40:00',2000.00,27,'SCHEDULED'),(28,'Bujumbura, Burundi','Kigali, Rwanda','2026-12-07','20:30:00',1500.00,28,'SCHEDULED'),(29,'Addis Ababa, Ethiopia','Nairobi, Kenya','2026-08-11','12:20:00',6000.00,29,'SCHEDULED'),(30,'Lusaka, Zambia','Dar es Salaam, Tanzania','2026-06-20','10:50:00',8000.00,30,'SCHEDULED');
/*!40000 ALTER TABLE `routes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('PASSENGER','ADMIN','AGENT') DEFAULT 'PASSENGER',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone_number` (`phone_number`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Alice','Kamau','alice1@gmail.com','0700000000','$2y$10$6AP9mQLbsAlcc2yDxdX3.Oed376Faioz4iG2NG7Z5ZTsEOQU8huQe','ADMIN','2026-01-13 16:42:56'),(2,'Bob','Ochieng','bob2@gmail.com','0700000001','$2y$10$eOws.VlRQIGeg7axR6s9pOcAEHNSQ7ogTbPqG4uGzsnXQJ1jf2GUK','ADMIN','2026-01-13 16:42:56'),(3,'Charlie','Kipkorir','charlie3@gmail.com','0700000002','$2y$10$nnpgJAAK5jr6CwCNQbFJTuRXCZZY017LneYwnjbpX2IhOa1aHKZqW','ADMIN','2026-01-13 16:42:56'),(4,'David','Maina','david4@gmail.com','0700000003','$2y$10$KPSYO2TIhGX2uVCcycR08uyYkqVoQMnq7t1xWLGIP0j8d5Hhz7y32','ADMIN','2026-01-13 16:42:56'),(5,'Eve','Wanjiku','eve5@gmail.com','0700000004','$2y$10$07KWtn7qvTnlWR4AWJprzujaiG4W0QuNot90mOJaD61jwkIHcBOVS','ADMIN','2026-01-13 16:42:56'),(6,'Frank','Otieno','frank6@gmail.com','0700000005','$2y$10$Dw4bGCBQoh6he42VVOKyqegyTzNd81KDtP8J0hg3r79pAOfZWll/e','ADMIN','2026-01-13 16:42:56'),(7,'Grace','Achieng','grace7@gmail.com','0700000006','$2y$10$Kf8pw34A0CbitzLb2W1uhOrJuy3Jpz8YaBkHZnFrCGUEWvToaukuC','ADMIN','2026-01-13 16:42:56'),(8,'Hank','Musyoka','hank8@gmail.com','0700000007','$2y$10$7AfpBfCzt0V0ID.iVXutLOdQjeT6YfQQTz7toR776amoEqHSKpApK','ADMIN','2026-01-13 16:42:56'),(9,'Ivy','Njeri','ivy9@gmail.com','0700000008','$2y$10$l7XO37p6M77kIXQmFT/7au5acAaWJyqXPueFCs6QEDK2rT7iAE10u','ADMIN','2026-01-13 16:42:56'),(10,'Jack','Mutua','jack10@gmail.com','0700000009','$2y$10$faynGAGhm8/w.YVgf42B1eSL7Kta9AdKvGCSz93ZfhT/Oge6Wu2Z2','ADMIN','2026-01-13 16:42:56'),(11,'Karen','Njoroge','karen1@gmail.com','0711000000','$2y$10$vorDX2PZQq6CNyKhnEd6muNccbOp0/kAsmf/VoOvsdL9VixyFckha','AGENT','2026-01-13 16:42:56'),(12,'Leo','Mwangi','leo2@gmail.com','0711000001','$2y$10$n63JIZe9OeTTLHSgl9HrEuSQsam528/xT2XNpUo/jhNka2HVBWrYK','AGENT','2026-01-13 16:42:56'),(13,'Mia','Odhiambo','mia3@gmail.com','0711000002','$2y$10$e2UZmFWu0tvbMmaWdt/vtObHNqAnVEaY3JuZL2q6rKn9gDYQWXcPW','AGENT','2026-01-13 16:42:56'),(14,'Noah','Kimani','noah4@gmail.com','0711000003','$2y$10$OtaT8IBKXLzF7TwwxVjmpe8UyX7iXI2a991g2Bnf7BUbt4tjApzMu','AGENT','2026-01-13 16:42:56'),(15,'Olivia','Chebet','olivia5@gmail.com','0711000004','$2y$10$Vajn3ZpLHhurmfDz/CsvluQznpN7kTYjjk.Dxl9ktS4VJt9SicYmm','AGENT','2026-01-13 16:42:56'),(16,'Paul','Kariuki','paul6@gmail.com','0711000005','$2y$10$QTQGUhYKwJv9krYSEI/sku0vRIsACDQ2eE3FD3Ac07/b8tYf2C9m6','AGENT','2026-01-13 16:42:56'),(17,'Quinn','Awere','quinn7@gmail.com','0711000006','$2y$10$LntrFhaxjyTKyGEShGKFsuY/j7E5McloZPjzKoDRDozZjMmnaRjqS','AGENT','2026-01-13 16:42:56'),(18,'Ryan','Omondi','ryan8@gmail.com','0711000007','$2y$10$/Y6W3rm6Ug1L5sWVHCeQn.QwOFX0brsk7jyUpxNXLus.aayOwA1SO','AGENT','2026-01-13 16:42:56'),(19,'Sarah','Wambui','sarah9@gmail.com','0711000008','$2y$10$ZTnq0FaW5YapQmPJCahjredJ3fs.VlquxaIxWfSQhS7u82DBdGBOK','AGENT','2026-01-13 16:42:56'),(20,'Tom','Ndegwa','tom10@gmail.com','0711000009','$2y$10$0L.4AyomYevMyybNyBoGue4ZEUqn8I1S..h8MV7jyyx1Mi5sW3S5G','AGENT','2026-01-13 16:42:56'),(21,'Uma','Abdi','uma1@gmail.com','0722000000','$2y$10$1YvBeMq4Ot4OIzluMOkL8eSPlDm/vf1RvJDhD/S94gnKL7hVgKHx2','PASSENGER','2026-01-13 16:42:56'),(22,'Vin','Ndlovu','vin2@gmail.com','0722000001','$2y$10$NHV5qfiqIxq8CX08oMIAzu1HUU1aAzAg/VeCszLban9to86DGxr7O','PASSENGER','2026-01-13 16:42:56'),(23,'Will','Chamele','will3@gmail.com','0722000002','$2y$10$isNOgS0qXW3/jaKOHK/5aeMDajNAnJPxU5NhcAzUdRPr5NzdSReiu','PASSENGER','2026-01-13 16:42:56'),(24,'Xena','Tesfaye','xena4@gmail.com','0722000003','$2y$10$KY1KRlHaK7fU.fPjWQzrGehf4EtXzqD6cZKOU55Ysbwwv9ojHR3Fi','PASSENGER','2026-01-13 16:42:56'),(25,'Yara','Mensah','yara5@gmail.com','0722000004','$2y$10$/cYOmtVIWUga.v0oLa6kEukgJT922bVzTMjCmkLuuBLK6WlPUC6Fi','PASSENGER','2026-01-13 16:42:56'),(26,'Zac','Diallo','zac6@gmail.com','0722000005','$2y$10$uI27jAHOQfMptYojSuWiQ.U/ZrbkbPRZsk8wG0IQr8CXWljPEWGv2','PASSENGER','2026-01-13 16:42:56'),(27,'Adam','Keita','adam7@gmail.com','0722000006','$2y$10$cKzfWlu7dfxQaVZ9h4YdOumimgSVLp8isYQzHvspOMom9.RUuUAhG','PASSENGER','2026-01-13 16:42:56'),(28,'Bella','Sow','bella8@gmail.com','0722000007','$2y$10$CE03fm6meXEWMKeL48fXsunPESkwcP.krgkbZSUDv.v2.YMioHu6q','PASSENGER','2026-01-13 16:42:56'),(29,'Chris','Traore','chris9@gmail.com','0722000008','$2y$10$J/xI66qvwJoFOei8mACNSueIhH9xVv6va7E.4tgaolFxQLyx86xvW','PASSENGER','2026-01-13 16:42:56'),(30,'Drake','Kone','drake10@gmail.com','0722000009','$2y$10$FjK8MDssTU5GMnetQ9FAIe1NIK/yi0JNY7bIbqAoxi2i9MouNqxfS','PASSENGER','2026-01-13 16:42:56'),(31,'Yvette','Makindu','Yvette@gmail.com','0783637373','$2y$10$3paTmBgfY7LUyrDPZiHK5e7PQ69iivFD6cjpgbRQ2qMxUIoDP7Pxe','PASSENGER','2026-01-19 11:07:34'),(32,'dfdfdfdf','fdfdfdf','wafula@gmail.com','24343434','$2y$10$H/vNHa9Zd8BkkdkuXwCTneJCw.dwwSjFw7lyn/ILOnO64B8/3c9La','PASSENGER','2026-01-27 13:17:44');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-27 22:39:55
