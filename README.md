# 🏨 Hotel Booking System

A responsive hotel booking system built with Laravel that allows users to browse available rooms, view details, add them to a cart, and complete secure payments using Stripe. Designed with a clean UI and smooth UX to ensure a seamless booking experience.

## ⚙️ Tech Stack

- **Backend**: Laravel 10
- **Frontend**: Blade, Bootstrap 5, Tailwind CSS, ShadCN UI
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **Payment Gateway**: Stripe
- **JavaScript Libraries**: Stripe.js
- **Icons**: Lucide Icons
- **Package Management**: Composer, NPM
- **Version Control**: Git & GitHub

## 🌟 Features

- Browse available rooms  
- View room details and prices  
- Add to cart with real-time total price calculation  
- Stripe-powered secure payments  
- Order summary and confirmation alerts  
- Responsive, mobile-friendly design  
- Authentication & session handling  
- Reusable Laravel components  
- Error handling & alert messages

## 🖼️ Demo Screenshot

### 🏠 Home Page  
![Screenshot 2025-04-11 135932](https://github.com/user-attachments/assets/2bb368ad-5738-4144-9c50-48e1e38287b8)

### 🛒 Cart Summary  
![Screenshot 2025-04-11 151210](https://github.com/user-attachments/assets/fd2ca687-75ee-45bf-be57-6142edeaba52)

### 🏠 Room Listings  
![Screenshot 2025-04-11 135945](https://github.com/user-attachments/assets/d10afb65-1540-4585-9572-4280be843769)

### 💳 Checkout Page  
![Screenshot 2025-04-11 151228](https://github.com/user-attachments/assets/e8668c3b-d55a-4a81-b29c-1350a755e2fe)

---

## 🧰 Installation & Setup

### Prerequisites

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL or any supported DB

---

### 🛠️ Clone & Install

```bash
git clone https://github.com/anasnashat/hotel-system
cd hotel-system
composer install
npm install && npm run dev
cp .env.example .env
php artisan key:generate
