# Cryptocurrency Trading Platform

A real-time cryptocurrency trading platform built with Laravel 12, Vue 3, and PostgreSQL. This application features a complete order matching engine with real-time updates via Laravel Echo and Pusher.

## Features

-   **User Authentication**: Secure registration and login system
-   **Real-Time Trading**: Live order book updates using WebSockets
-   **Order Management**: Place, view, and cancel limit orders
-   **Wallet System**: Track USD balance and cryptocurrency holdings
-   **Order Matching Engine**: Automatic matching of buy/sell orders with price-time priority
-   **Commission System**: 1.5% commission on all trades (deducted from seller's USD)
-   **Asset Management**: Track available and locked balances for each asset

## Tech Stack

-   **Backend**: Laravel 12, PHP 8.3
-   **Frontend**: Vue 3 with Vite
-   **Database**: PostgreSQL
-   **Real-Time**: Laravel Echo, Pusher
-   **Authentication**: Laravel Sanctum (Session-based)
-   **Styling**: Tailwind CSS v4

## Prerequisites

-   PHP 8.3+
-   Composer
-   Node.js 18+
-   PostgreSQL
-   Pusher account (for real-time features)

## Installation

### 1. Clone the repository

```bash
git clone <repository-url>
cd virgosoft1
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Environment setup

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure database

Update your `.env` file with PostgreSQL credentials:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Configure Pusher

Update your `.env` file with Pusher credentials:

```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
PUSHER_SCHEME=https
```

### 6. Run migrations and seeders

```bash
php artisan migrate:fresh --seed
```

This will create:

-   10 test users (each with $10,000 balance)
-   Sample assets (BTC, ETH, USDT) for each user
-   Sample orders in the order book

### 7. Build frontend assets

```bash
npm run build
# or for development with hot reload
npm run dev
```

### 8. Start the application

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Usage

### Authentication

1. **Register**: Create a new account at `/register`
    - Each new user receives $10,000 USD starting balance
2. **Login**: Access your account at `/login`

### Trading

1. **View Order Book**: See all open buy and sell orders grouped by price
2. **Place Order**:
    - Select a symbol from your assets
    - Choose buy or sell
    - Enter price and amount
    - Orders are matched automatically if price conditions are met
3. **View My Orders**: See your order history and status
4. **Cancel Order**: Cancel any open order

### Real-Time Updates

The application automatically updates:

-   Order book when new orders are placed or matched
-   Your wallet balance when trades execute
-   Your assets when trades complete
-   Your orders list when status changes

## Architecture

### Models

-   **User**: User accounts with balance
-   **Asset**: Cryptocurrency holdings (amount + locked_amount)
-   **Order**: Limit orders (buy/sell)
-   **Trade**: Executed trades between orders

### Controllers (Skinny)

-   **ProfileController**: User profile and assets
-   **OrderController**: Order management and order book
-   **LoginController**: Authentication
-   **RegisterController**: User registration

### Services

-   **OrderService**: Order placement and cancellation logic
-   **MatchEngine**: Order matching algorithm with price-time priority

### API Resources

-   **UserResource**: User data transformation
-   **AssetResource**: Asset data with formatted amounts
-   **OrderResource**: Order data with status
-   **OrderbookResource**: Aggregated order book data

### Key Features

#### Order Matching Algorithm

```
1. Find matching orders (opposite side, valid price)
2. Sort by price-time priority
3. Lock necessary assets/funds
4. Execute trade with commission
5. Update orders and balances
6. Broadcast events to both users
```

#### Commission System

-   **Rate**: 1.5% of trade value (USD)
-   **Calculation**: `commission = (price × amount) × 0.015`
-   **Deduction**: From seller's USD proceeds
-   **Example**: 0.01 BTC @ $95,000 = $950 volume → $14.25 commission

#### Data Formatting

-   Prices ≥ $1,000: 2 decimals (e.g., "45,350.25")
-   Prices ≥ $1: 2-4 decimals (e.g., "150.2345")
-   Prices < $1: 2-8 decimals (e.g., "0.00012345")
-   Amounts: 0-8 decimals with trailing zeros removed

## Testing

Run the test suite:

```bash
php artisan test
```

Current test coverage:

-   Profile API endpoints
-   Order placement, viewing, and cancellation
-   Authentication requirements

## API Endpoints

### Authentication Required

```
GET    /api/profile          - Get user profile and assets
GET    /api/orders           - Get order book (optional: ?symbol=BTC)
GET    /api/user/orders      - Get user's orders
POST   /api/orders           - Place a new order
DELETE /api/orders/{id}      - Cancel an order
```

### Request Examples

**Place Order:**

```json
POST /api/orders
{
    "symbol": "BTC",
    "side": "sell",
    "price": "50000",
    "amount": "0.1"
}
```

**Response:**

```json
{
    "message": "Order placed successfully",
    "order": {
        "id": 1,
        "symbol": "BTC",
        "side": "sell",
        "price": "50000.00000000",
        "amount": "0.10000000",
        "formatted_price": "50,000",
        "formatted_amount": "0.1",
        "status": 1
    }
}
```

## Broadcasting Events

### OrderMatched Event

Broadcast when orders are matched:

-   **Channels**:
    -   `private-user.{buyer_id}`
    -   `private-user.{seller_id}`
    -   `public-symbol.{symbol}`
-   **Data**: Trade details, updated orders

## Code Style

The project follows Laravel coding standards:

```bash
# Format code
vendor/bin/pint

# Check for issues
vendor/bin/pint --test
```

## Best Practices Implemented

-   ✅ Skinny controllers, fat models
-   ✅ Form Request validation
-   ✅ API Resources for data transformation
-   ✅ Service classes for business logic
-   ✅ Eloquent scopes for reusable queries
-   ✅ Factory pattern for testing
-   ✅ Database transactions for data integrity
-   ✅ Real-time broadcasting with Laravel Echo
