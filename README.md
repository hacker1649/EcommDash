# EcommDash
EcommDash is a comprehensive e-commerce platform built with PHP and MySQL. It includes a robust admin panel for managing users, products, and orders, alongside a user-friendly interface for customers to browse and purchase products. This project demonstrates an end-to-end implementation of an e-commerce website with full-stack development.

## Key Features:
### Admin Panel:

- User management: View, add, edit, and delete users.
- Product management: Add, edit, and delete products from the catalog.
- Order management: View and update order statuses.
- Dashboard: View statistics like total orders, total products, and total users.

### User Panel:

- User authentication: Register, login, and manage profiles.
- Browse products: View and filter products by category, price, etc.
- Cart: Add products to the cart, update quantity, and remove items.
- Checkout: Complete the order process with payment options.

### Database:

- MySQL database integration for storing user, product, and order data.
- Secure password management and user authentication using hashing techniques.

## Features
### Admin Panel
#### 1. User Management:

- View all registered users.
- Update or delete user profiles as needed.

#### 2. Product Management:

- Add new products to the store, including descriptions, images, and prices.
- Edit or remove existing products.
- Inventory tracking to ensure stock availability.

#### 3. Order Management:

- View all orders placed by customers.
- Manage order statuses (e.g., pending, processing, completed).

### User Interface

#### 1. Product Browsing:

- Easy navigation to explore various categories and products.
- View detailed product descriptions and images.

#### 2. Shopping Cart:

- Add items to the cart.
- Modify quantities or remove items before checkout.

#### 3. User Registration/Login:

- Secure user authentication system.
- Access to personalized order history.

#### 4. Checkout Process:

- Seamless order placement.
- Email notifications for order confirmations (if configured).
- Secure integration with PayPal for processing payments.
- Users can pay for their orders using their PayPal accounts or credit/debit cards via PayPal.
- Payment status updates automatically for admins to track transactions.

## Technologies Used

### Frontend
- HTML5: For structuring content.
- CSS3: For styling the website and enhancing user experience.
- JavaScript: For client-side interactivity and validations.

### Backend
- PHP: Server-side scripting for business logic.
- MySQL: Database for storing user, product, and order data.

### Other Tools
- Bootstrap: For responsive design, pre-built components, and faster development.
- XAMPP/WAMP: Local server for development and testing.

## Installation & Setup

Follow these steps to set up the project on your local machine:

### Prerequisites
- Install a local server such as XAMPP or WAMP.
- Ensure PHP and MySQL are properly configured.
- Set up a PayPal Developer Account to obtain your client ID and secret.

1. Clone the repository to your local machine:
    
    ```bash
    git clone https://github.com/hacker1649/EcommDash.git 
    ```

2. Move the project to your server directory e.g. for XAMPP, move to `htdocs/` and for WAMP move to `www/`.

3. Before running the project, make sure that you have a valid database connection file.

```bash
<?php

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_sneat_users";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
```

4. Move the EcommDash folder to the htdocs directory (if using XAMPP) or www directory (if using WAMP).

5. Open your browser and type http://localhost/sneat/user/website/website_page.php to access the website.

6. Log in with the default admin credentials (if available) or register as a new user.

## Steps to Configure PayPal

- Create or log in to a PayPal Developer account ([PayPal Developer](https://developer.paypal.com/home/)).
- Obtain API credentials (Client ID and Secret).
- Open `payment_page.php` and locate the PayPal API integration code.
- Replace placeholders with your PayPal Client ID and Secret.
- Enable PayPal's sandbox mode for testing.
- Place test orders to ensure proper integration.
- Once testing is complete, switch to PayPal’s live environment for real transactions.

## Usage
### Admin Usage:

- After logging in, the admin can access the dashboard and manage users, products, and orders.
- Admin can create new products, delete or update existing ones, and manage the inventory.
- The order management page allows the admin to view all orders and update their status.

### User Usage:

- Users can browse through categories and search for specific products.
- Items can be added to the shopping cart and removed if necessary.
- Users can proceed to checkout and complete their purchase.

## Contributing
Contributions are welcome! If you have suggestions or want to add features, feel free to fork the repository, make changes, and submit a pull request.
