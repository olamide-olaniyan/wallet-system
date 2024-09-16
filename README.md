
# **Basic Wallet System API with Laravel**

This project is a mock basic wallet system built with **Laravel** and integrates **Paystack** for payment processing. It allows users to create wallets, credit them via Paystack, transfer funds between wallets, and generate admin payment summaries. This project also uses **Laravel Sanctum** for authentication.

## **Features**
- User registration and login via phone number
- Multiple wallet creation per user with unique currencies
- Credit wallets using Paystack payment gateway
- Transfer funds between wallets
- Admin approval for transfers above N1,000,000
- Admin monthly payment summaries

---

## **Installation Guide**

Follow these steps to install and set up the project locally:

### **Requirements**
- PHP 8.x
- Composer
- MySQL or other supported databases
- Laravel 10.x
- Paystack Account
- Postman or any API testing tool (for testing the API)

### **Step 1: Clone the Repository**

```bash
git clone https://github.com/olamide-olaniyan/wallet-system
cd wallet-system
```

### **Step 2: Install Dependencies**

Use Composer to install the project dependencies:

```bash
composer install
```

### **Step 3: Set Up Environment Variables**

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Open the `.env` file and configure the following settings:

#### **Database Configuration**
Set up your database connection:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

#### **Paystack Configuration**
Add the following Paystack environment variables:

```bash
PAYSTACK_PUBLIC_KEY=your_paystack_public_key
PAYSTACK_SECRET_KEY=your_paystack_secret_key
PAYSTACK_PAYMENT_URL=https://api.paystack.co
MERCHANT_EMAIL=your_paystack_merchant_email
```

### **Step 4: Migrate Database**

Run the migrations the database:

```bash
php artisan migrate
```

### **Step 5: Generate Application Key**

Generate the Laravel application key:

```bash
php artisan key:generate
```

### **Step 6: Serve the Application**

Finally, start the Laravel development server:

```bash
php artisan serve
```

Your API is now running at `http://127.0.0.1:8000`.

---

## **API Documentation**

Below is the detailed documentation for each API endpoint.

### **Authentication**

#### **Register**
- **Method**: `POST`
- **URL**: `/register`
- **Request Body**:
  ```json
  {
    "phone_number": "08123456789",
    "password": "password123"
  }
  ```
- **Response**:
  ```json
  {
    "message": "User registered successfully"
  }
  ```

#### **Login**
- **Method**: `POST`
- **URL**: `/login`
- **Request Body**:
  ```json
  {
    "phone_number": "08123456789",
    "password": "password123"
  }
  ```
- **Response**:
  ```json
  {
    "token": "your-api-token"
  }
  ```

#### **Logout**
- **Method**: `POST`
- **URL**: `/logout`
- **Headers**:
  ```http
  Authorization: Bearer <your-api-token>
  ```
- **Response**:
  ```json
  {
    "message": "Logged out successfully"
  }
  ```

---

### **Wallet Endpoints**

#### **Create Wallet**
- **Method**: `POST`
- **URL**: `/wallets`
- **Headers**:
  ```http
  Authorization: Bearer <your-api-token>
  ```
- **Request Body**:
  ```json
  {
    "currency": "NGN"
  }
  ```
- **Response**:
  ```json
  {
    "message": "Wallet created successfully",
    "wallet": {
      "id": 1,
      "currency": "NGN",
      "balance": 0
    }
  }
  ```

#### **Credit Wallet**
- **Method**: `POST`
- **URL**: `/wallets/{id}/credit`
- **Headers**:
  ```http
  Authorization: Bearer <your-api-token>
  ```
- **Request Body**:
  ```json
  {
    "amount": 10000,
    "email": "user@example.com"
  }
  ```
- **Response**:
  Redirects to Paystack for payment processing.

#### **Transfer Funds Between Wallets**
- **Method**: `POST`
- **URL**: `/wallets/transfer`
- **Headers**:
  ```http
  Authorization: Bearer <your-api-token>
  ```
- **Request Body**:
  ```json
  {
    "from_wallet_id": 1,
    "to_wallet_id": 2,
    "amount": 5000
  }
  ```
- **Response**:
  ```json
  {
    "message": "Transfer successful",
    "from_wallet": {
      "id": 1,
      "balance": 5000
    },
    "to_wallet": {
      "id": 2,
      "balance": 5000
    }
  }
  ```

---

### **Admin Endpoints**

#### **Approve Transfer Over N1,000,000**
- **Method**: `POST`
- **URL**: `/admin/transfers/{id}/approve`
- **Headers**:
  ```http
  Authorization: Bearer <admin-api-token>
  ```
- **Response**:
  ```json
  {
    "message": "Transfer approved successfully"
  }
  ```

#### **Monthly Payment Summary**
- **Method**: `GET`
- **URL**: `/admin/summary/{month}`
- **Headers**:
  ```http
  Authorization: Bearer <admin-api-token>
  ```
- **Response**:
  ```json
  {
    "month": "September",
    "summary": [
      {
        "wallet_id": 1,
        "total_credits": 500000,
        "total_debits": 200000
      }
    ]
  }
  ```

---

## **Testing**

### **Postman**
You can use **Postman** or similar API testing tools to test the API by sending HTTP requests to the various endpoints mentioned above.


---

## **Project Structure**

```bash
.
├── app
├── bootstrap
├── config
├── database
├── public
├── resources
├── routes
│   └── api.php     # API routes are defined here
├── storage
└── tests           # Test files can be found here
```

---

## **Contribution Guide**

1. Fork the repository
2. Create a new branch for your feature or bugfix
3. Commit your changes and push to your branch
4. Open a pull request

---

## **License**

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## **Contact**

If you have any questions, feel free to reach out:

- Name: Olamide Olaniyan
- Email: olaniyanolamide42@gmail.com
