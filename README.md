# Wema Travellers: International Bus Booking System (IBBS)

![Project Status](https://img.shields.io/badge/Status-Prototype-purple)
![Tech Stack](https://img.shields.io/badge/Tech-PHP%20%7C%20MySQL%20%7C%20Vanilla%20CSS-blue)

## 🚌 Overview
**Wema Travellers (IBBS)** is a digital-first transit platform designed to eliminate the "Last-Mile Manual Bottleneck" in the international bus industry across East Africa. While many systems handle digital bookings, IBBS focuses on the critical boarding and verification phase, replacing slow manual paper checks with a **Secure Digital Token Search Framework**.

---

## 🎯 The Core Problem: The Manual Bottleneck
Despite the growth of digital bookings, passengers are often forced into long queues at bus stations while staff manually check names against paper manifests. This leads to:
1. **Boarding Delays**: High friction during peak travel hours.
2. **Revenue Fraud**: Vulnerability to counterfeit paper tickets.
3. **Immigration Non-Compliance**: Lack of real-time passenger data for border security.

**The IBBS Solution:**
Our system generates a unique **Digital Token** for every booking. Staff verify passengers instantly by entering their **National ID or Passport Number**, allowing for a "Single Source of Truth" that prevents double-booking and ensures manifest accuracy for immigration authorities.

---

## ✨ Key Features
- **🌐 Online Travel Portal**: Real-time route searching and seat selection.
- **🛡️ Secure Verification**: Staff can validate boarding passes using Passenger ID documents (No paper required).
- **🛂 Border Compliance**: Automatic generation of passenger manifests including ID/Passport details.
- **🏢 Admin Central Hub**: Fleet management, real-time revenue reports, and user auditing.
- **🔐 Agent Safeguards**: Centralized seat locking to prevent overbooking across decentralized agencies.

---

## 🛠️ Technology Stack
- **Backend**: PHP 8.x
- **Database**: MySQL (RDBMS)
- **Frontend**: Custom Vanilla CSS & Javascript (No heavy frameworks for maximum performance).
- **Security**: Cryptographic password hashing (BCrypt) and session-based role authorization.

---

## 🚀 Quick Setup
1. **Clone the project** into your local server directory (e.g., `htdocs` or `www`).
2. **Database Initialization**: 
   - Ensure your MySQL server is running.
   - Run the automated setup script: `http://localhost/IBBS_PROTOTYPE/setup_database.php`.
   - This will create the `IBBS_PROTOTYPE` database and populate it with sample routes, buses, and users.
3. **Login**:
   - **Admin Access**: `alice1@gmail.com` / `123456`
   - **Agent Access**: `karen1@gmail.com` / `123456`
   - **Passenger Access**: `uma1@gmail.com` / `123456`

---

## 📁 Repository Structure
- `/pages/`: Core application logic and UI views.
- `/pages/css/`: Global styling tokens and component designs.
- `/pages/js/`: Dynamic UI orchestration (Seat Map, Header, Table Management).
- `/mysql_dump/`: Backup SQL files for manual database restoration.
- `setup_database.php`: Automated environment configuration tool.

---

## 📝 License
Proprietary Prototype - Developed for Academic and Commercial Demonstration.
