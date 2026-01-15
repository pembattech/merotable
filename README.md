# **Mero Table – Multi-Restaurant Dine-In Order Management System**

**Mero Table** is a modern, QR-based platform built with **Laravel** and **Laravel Sanctum**, designed to streamline operations for dine-in restaurants. It simplifies the transition from menu browsing to billing, reducing staff workload and eliminating table-service confusion.

The platform allows multiple restaurants to register, undergo admin verification, and manage their daily business through a secure, centralized dashboard.

### **Flexible Ordering Modes**

To maximize efficiency and cater to different service styles, Mero Table supports two primary ordering workflows:

* **Self-Service QR Ordering:** Customers can simply scan a unique QR code located on their table to browse the digital menu and place orders directly from their own devices. This empowers guests and speeds up the ordering process.
* **Waiter-Assisted Ordering:** Staff members can take orders traditionally using the system on their mobile devices. This allows waiters to remain mobile on the floor, instantly syncing table requests with the kitchen.

---

### **Key Benefits**

* **Centralized Management:** A single dashboard for restaurant owners to manage menus, staff, and analytics.
* **Eliminated Table Confusion:** Orders are digitally mapped to specific tables, ensuring accuracy in delivery and billing.
* **Optimized Operations:** Faster communication between the front-of-house and the kitchen via real-time data sync.
* **Scalable Multi-Tenancy:** Designed to host numerous independent restaurant brands on one secure platform.

---

## **Core Concept**

Mero Table transforms traditional dine-in ordering into a **fast, efficient, and error-free experience** through **QR codes and table-based management**. Whether **customers place orders directly** from their phones or **waiters take orders** via mobile devices, the kitchen receives real-time updates instantly. This seamless integration ensures billing staff can accurately identify tables and manage orders, even during peak hours.

---

## **How Mero Table Works**

1. **Restaurant Registration & Verification**
* Restaurants register and submit business verification documents.
* Super Admin verifies and activates the restaurant account.
* Once verified, default tables (**T1–T15**) are automatically generated.


2. **Dual-Mode Ordering**
* **Customer Self-Service:** Customers scan the table’s unique QR code to open the digital menu and place orders without needing to log in.
* **Waiter-Led Ordering:** Staff can take orders directly at the table using the mobile-optimized dashboard, instantly assigning items to the correct table.


3. **Real-Time Order Flow**
* Orders from both customers and waiters are instantly synced to the **Kitchen Display System**.
* Kitchen staff updates order status in real-time (**Pending → Preparing → Ready**).
* Staff receives notifications for new requests, ensuring no order is missed.


4. **Smart Billing & Checkout**
* Centralized billing tracks all items ordered per table, regardless of who placed the order.
* Tables are cleared and marked as "Available" once the final bill is settled.
* Supports multiple payment methods: **Cash, Card, and Online Payments**.


---

## **Key Feature: Item-Based Table Identification**

Mero Table solves a common dine-in problem:

> “The customer doesn’t remember their table number.”
> 

### **Solution**

Billing staff can search tables based on ordered items.

**Example:**

- Customer says: *“We ordered 2 plates of Momo.”*
- Staff selects **Momo** → system shows all tables that ordered Momo.
- Staff adds quantity filter → tables narrow down.
- Exact table is identified instantly.

✔ Staff can selects mulitple items.

✔ Faster checkout

✔ No confusion

✔ Better customer experience

---

### **User Roles & Permissions**

| Role | Responsibility | Key Permissions |
| --- | --- | --- |
| **Super Admin** | Platform Oversight | Verify/Deactivate restaurants, manage subscription plans, view global analytics. |
| **Restaurant Owner** | Business Management | Manage menu items, view revenue reports, manage staff accounts (Waiters/Kitchen). |
| **Waiter** | Front-of-House Service | Place orders for customers, view table status, update order notes, request bill generation. |
| **Kitchen Staff** | Order Fulfillment | View live order queue, update preparation status (**Preparing → Ready**). |
| **Billing/Cashier** | Financial Settlement | Process payments, apply discounts, print invoices, and close/clear tables. |
| **Customer** | Self-Service | Scan QR code, browse menu, place orders, and track live order status. |

---

### **Technical Specifications**

Mero Table is built with a modern tech stack focused on high performance, security, and real-time updates.

* **Core Framework:** **Laravel** (Robust, scalable PHP framework).
* **Authentication:** **Laravel Sanctum** (Secure, token-based API authentication for mobile and web).
* **Frontend:** **Tailwind CSS & Blade/Livewire** (For a responsive, mobile-first UI).
* **Database:** **MySQL** (Structured data management for restaurants, menus, and orders).
* **Real-Time Engine:** **Pusher / Laravel Reverb** (Ensures kitchen and billing screens update instantly without refreshing).
* **QR System:** Dynamic **Table-Specific QR Generation** (Unique identifiers per table per restaurant).

---

### **System Features for Developers**

* **Multi-Tenancy Architecture:** Data isolation ensures Restaurant A can never see Restaurant B's data.
* **RESTful API:** Ready for future native mobile app integration.
* **Optimized Database:** Efficient indexing for fast table-wise bill calculation.
* **Scalable Table Management:** While T1–T15 are default, the system allows for custom table naming and expansion.

---

## **Future-Ready & Scalable**

Mero Table is built with scalability in mind and supports:

- Dynamic table management
- Staff roles & permissions
- POS integration
- Online payment gateways
- Order analytics & reports
- Multi-branch restaurant support

---

## **Business Value**

- Reduces manual order taking
- Minimizes billing errors
- Speeds up kitchen operations
- Improves customer experience
- Scales easily across multiple restaurants

---

