# StockMaster – Laravel Inventory Management System

A modern, high-performance **Inventory Management System** built with **Laravel 12, PHP 8.2, and MySQL**, featuring real-time stock level monitoring, an immutable transaction audit ledger, low-stock threshold triggers, and dynamic analytics.

---

## 🌟 Key Features

1. **Dashboard & Real-Time Analytics**
   - **Financial Valuation**: Tracks both cost valuation and retail inventory worth in real-time.
   - **KPI Metrics**: Active catalog count, physical units in stock, low-stock count, and out-of-stock count.
   - **Interactive Visualizations**: 7-Day Stock Inflow vs Outflow bar chart and Category valuation doughnut chart (powered by Chart.js).
   - **Urgent Restock Action Center**: Immediate notification table showing items below reorder threshold with 1-click restock.

2. **Product Catalog Management**
   - Full CRUD operations with auto-calculated stock health badges (`In Stock`, `Low Stock`, `Out of Stock`).
   - SKU, Barcode, Multi-category classification, primary supplier, cost price, selling price, and warehouse bin location.
   - Instant search by SKU, name, or barcode with category and stock status filters.

3. **Stock Movements & Immutable Audit Trail**
   - **Stock In (Receiving)**: Add units received from purchase orders or suppliers.
   - **Stock Out (Dispatch)**: Deduct units for customer sales orders with insufficient-stock guards.
   - **Stock Adjustment (Audit)**: Set new physical count during warehouse audits.
   - Concurrency safety with database transaction locks (`lockForUpdate` & `DB::transaction`).

4. **Suppliers & Categories Directory**
   - Multi-vendor directory with contact persons, email, phone, and addresses.
   - Category management with live inventory valuation totals.

5. **Reporting & Data Export**
   - Streamed CSV export for complete Product Catalog.
   - Streamed CSV export for the Stock Movements audit ledger.

---

## 🚀 Getting Started

### 1. Requirements
- PHP 8.2+ (`pdo_mysql`, `mbstring`, `curl` extensions enabled)
- MySQL Server running on port `3306` (e.g., via XAMPP, WAMP, or standalone MySQL)
- Composer 2.x

### 2. Database Configuration
The application is pre-configured in `.env` to connect to your local MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_management
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations & Seed Data
```bash
php artisan migrate --seed
```

### 4. Start the Application Server
```bash
php artisan serve
```
Open **[http://localhost:8000](http://localhost:8000)** in your browser.

---

## 👥 Role-Based Access Control (RBAC)

The system includes 4 distinct user roles with strict server-side authorization:

| Role | Badge | Permissions & Capabilities | Demo Login |
| :--- | :--- | :--- | :--- |
| **System Administrator** | `ADMIN` | Superuser: Team & user management, delete items, category/supplier CRUD, stock auditing, exports. | `admin@inventory.local` / `password123` |
| **Warehouse Manager** | `MANAGER` | Add/edit catalog items, suppliers, categories, perform inventory count adjustments. | `manager@inventory.local` / `password123` |
| **Inventory Staff** | `STAFF` | Daily floor operations: view catalog, record Stock In & dispatch Stock Out. | `staff@inventory.local` / `password123` |
| **Auditor / Viewer** | `AUDITOR` | Read-only access: view inventory valuations, trend charts, and export CSV data. | `auditor@inventory.local` / `password123` |

> ⚡ **Tip:** You can switch between roles with 1 click directly from the login page or using the Role Switcher pills in the top navigation bar!

---

## 🧪 Running Automated Tests
Run the automated test suite covering all modules and RBAC rules:
```bash
php artisan test
```
All 17 feature and unit tests pass with 46 assertions.

