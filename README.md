# Konbi (コンビニ) System

A modern, comprehensive Convenience Store Management System built for scalability and multi-store operations.

![Laravel](https://img.shields.io/badge/Laravel-v12-FF2D20)
![Vue](https://img.shields.io/badge/Vue-v3-4FC08D)
![Inertia](https://img.shields.io/badge/Inertia-v2-9553E9)
![Status](https://img.shields.io/badge/Status-Beta-blue)

## 📋 Overview

**Konbi** is a full-stack Point of Sale (POS) and Inventory Management System designed to handle the dynamic needs of convenience stores (like 7-Eleven). It supports multiple store locations, real-time inventory tracking, automated reordering, and a dual-mode POS interface optimized for both desktop and tablet usage.

### Key Features

- **🏪 Multi-Store Architecture**: Manage multiple branches from a single installation with strict data isolation.
- **🛒 Dual-Mode POS**:
    - **Desktop Mode**: Keyboard-first design for rapid checkout.
    - **Tablet Mode**: Touch-friendly interface with on-screen numpad.
- **📦 Smart Inventory**:
    - Real-time stock tracking.
    - **Auto-Reorder**: Automatically creates draft Purchase Orders when stock hits low thresholds.
    - **History Logs**: Detailed audit trail of every stock movement (Sales, Adjustments, Corrections).
- **🏷️ Product Management**:
    - Auto-generation of **SKUs** and **Barcodes** (EAN-13 style).
    - Dynamic creation of Categories and Brands during product entry.
- **👥 Role-Based Access**:
    - **Super Admin**: Global view and management.
    - **Store Manager**: Isolated access to their specific store.
    - **Onboarding Flow**: New users are guided to create their first store immediately.

---

## 🚀 Getting Started

### Prerequisites

- **PHP**: 8.2 or higher
- **Composer**: Dependency Manager for PHP
- **Node.js** or **Bun**: Javascript Runtime (Bun recommended)
- **Database**: MySQL, MariaDB, or SQLite

### Installation

1.  **Clone the repository**

    ```bash
    git clone https://github.com/yourusername/konbi.git
    cd konbi
    ```

2.  **Install Backend Dependencies**

    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies**

    ```bash
    bun install
    # or
    npm install
    ```

4.  **Environment Setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    _Configure your `.env` file with your database credentials._

5.  **Database Setup**

    ```bash
    php artisan migrate --seed
    ```

    _This will set up the database and create a default "Konbi Main Store" with a Super Admin account._

6.  **Run the Application**
    Open two terminal tabs:

    ```bash
    # Terminal 1: Laravel Server
    php artisan serve

    # Terminal 2: Vite Dev Server
    bun run dev
    ```

7.  **Access the App**
    - URL: `http://localhost:8000`
    - **Default Admin**: `admin@konbi.test`
    - **Password**: `password`

---

## 📖 User Guide

### 1. Initial Setup (Onboarding)

When you register a new account, you will be redirected to the **Create Store** page. You must create a store to proceed to the dashboard. This store will automatically be assigned to you.

### 2. Dashboard & Switching Stores

- **Dashboard**: Shows real-time sales, transactions, and low stock alerts.
- **Sidebar Switcher**: Click your store name in the top-left sidebar to:
    - Switch between stores (if you manage multiple).
    - Create a **New Store**.
    - Access **Global View** (Super Admins only).

### 3. Point of Sale (POS)

Navigate to **POS** in the sidebar.

- **Desktop Mode**: Use your physical keyboard. Press `F12` to checkout.
- **Tablet Mode**: Click the "Tablet" toggle. The interface expands for touch targets. Checkout opens a sidebar with a large Numpad for cash entry.

### 4. Inventory & Reordering

- **Tracking**: Go to **Inventory**. Click "History" on any item to see who changed stock and why.
- **Auto-Reorder**: Set a `Reorder Level` (e.g., 10) and `Reorder Quantity` (e.g., 50) for a product. When sales drop stock to 10, a draft Purchase Order is automatically created in the backend.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 (Composition API) + Inertia.js
- **Styling**: Tailwind CSS v4
- **Components**: Shadcn Vue (Radix UI) + Lucide Icons
- **Database**: SQLite (Dev) / MySQL (Prod)
- **Testing**: Pest PHP

---

## 🧪 Testing

The project uses **Pest** for feature and unit testing.

```bash
# Run all tests
php artisan test

# Run specific feature tests
php artisan test --filter=AutoReorderTest
php artisan test --filter=POSTest
```

---

## 🤝 Contributing

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.
