# DeDebtify API Documentation

## Base URL

```
http://localhost:3001/api
```

## Authentication

Most endpoints require JWT authentication. Include the token in the Authorization header:

```
Authorization: Bearer <your_jwt_token>
```

---

## Authentication Endpoints

### POST /api/auth/register

Register a new user.

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "firstName": "John",
  "lastName": "Doe"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "eyJhbGci...",
    "user": {
      "id": "cuid123",
      "email": "user@example.com",
      "firstName": "John",
      "lastName": "Doe",
      ...
    }
  },
  "message": "User registered successfully"
}
```

### POST /api/auth/login

Login existing user.

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "token": "eyJhbGci...",
    "user": { ... }
  },
  "message": "Login successful"
}
```

### GET /api/auth/me

Get current authenticated user.

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "cuid123",
    "email": "user@example.com",
    ...
  }
}
```

### PATCH /api/auth/preferences

Update user preferences.

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
  "monthlyIncome": 5000,
  "preferredPayoffMethod": "AVALANCHE",
  "currency": "USD",
  "timeZone": "America/New_York"
}
```

---

## Dashboard Endpoint

### GET /api/dashboard

Get complete dashboard data with all financial information.

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "user": { ... },
    "summary": {
      "totalDebt": 50000,
      "totalCreditCardDebt": 5000,
      "totalLoanDebt": 25000,
      "totalMortgageDebt": 20000,
      "totalMonthlyPayments": 1500,
      "totalMonthlyBills": 800,
      "debtToIncomeRatio": 35.5,
      "creditUtilization": 45.2
    },
    "creditCards": [ ... ],
    "loans": [ ... ],
    "mortgages": [ ... ],
    "bills": [ ... ],
    "goals": [ ... ],
    "recentSnapshots": [ ... ]
  }
}
```

---

## Credit Card Endpoints

### POST /api/credit-cards

Create a new credit card.

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
  "name": "Chase Freedom",
  "balance": 2500,
  "creditLimit": 5000,
  "interestRate": 18.99,
  "minimumPayment": 50,
  "extraPayment": 100,
  "dueDay": 15,
  "autoPay": false
}
```

### GET /api/credit-cards

Get all credit cards for current user.

**Headers:** `Authorization: Bearer <token>`

### GET /api/credit-cards/:id

Get a single credit card by ID.

**Headers:** `Authorization: Bearer <token>`

### PATCH /api/credit-cards/:id

Update a credit card.

**Headers:** `Authorization: Bearer <token>`

**Request Body:** Any credit card fields to update

### DELETE /api/credit-cards/:id

Delete a credit card.

**Headers:** `Authorization: Bearer <token>`

---

## Loan Endpoints

### POST /api/loans

Create a new loan.

**Headers:** `Authorization: Bearer <token>`

**Request Body:**
```json
{
  "name": "Auto Loan",
  "loanType": "AUTO",
  "principal": 25000,
  "currentBalance": 22000,
  "interestRate": 6.5,
  "termMonths": 60,
  "monthlyPayment": 488,
  "extraPayment": 50,
  "startDate": "2023-01-01T00:00:00.000Z"
}
```

**Loan Types:**
- `PERSONAL`
- `AUTO`
- `STUDENT`
- `OTHER`

### GET /api/loans

Get all loans for current user.

### GET /api/loans/:id

Get a single loan by ID.

### PATCH /api/loans/:id

Update a loan.

### DELETE /api/loans/:id

Delete a loan.

---

## Mortgage Endpoints

### POST /api/mortgages

Create a new mortgage.

**Request Body:**
```json
{
  "name": "Primary Residence",
  "propertyAddress": "123 Main St, City, State 12345",
  "loanAmount": 350000,
  "currentBalance": 342000,
  "interestRate": 4.5,
  "termYears": 30,
  "monthlyPayment": 1773,
  "extraPayment": 200,
  "propertyTax": 4800,
  "homeownersInsurance": 1200,
  "pmi": 150,
  "startDate": "2020-06-01T00:00:00.000Z"
}
```

### GET /api/mortgages

Get all mortgages.

### GET /api/mortgages/:id

Get a single mortgage by ID.

### PATCH /api/mortgages/:id

Update a mortgage.

### DELETE /api/mortgages/:id

Delete a mortgage.

---

## Bill Endpoints

### POST /api/bills

Create a new bill.

**Request Body:**
```json
{
  "name": "Electric Bill",
  "category": "UTILITIES",
  "amount": 150,
  "frequency": "MONTHLY",
  "dueDay": 1,
  "autoPay": true,
  "isEssential": true
}
```

**Categories:**
- `HOUSING`
- `TRANSPORTATION`
- `UTILITIES`
- `FOOD`
- `HEALTHCARE`
- `INSURANCE`
- `ENTERTAINMENT`
- `SUBSCRIPTIONS`
- `OTHER`

**Frequencies:**
- `WEEKLY`
- `BI_WEEKLY`
- `MONTHLY`
- `QUARTERLY`
- `ANNUALLY`

### GET /api/bills

Get all bills.

### GET /api/bills/:id

Get a single bill by ID.

### PATCH /api/bills/:id

Update a bill.

### DELETE /api/bills/:id

Delete a bill.

---

## Goal Endpoints

### POST /api/goals

Create a new goal.

**Request Body:**
```json
{
  "name": "Emergency Fund",
  "goalType": "EMERGENCY_FUND",
  "targetAmount": 10000,
  "currentAmount": 2500,
  "monthlyContribution": 500,
  "targetDate": "2025-12-31T00:00:00.000Z",
  "priority": "HIGH"
}
```

**Goal Types:**
- `SAVINGS`
- `EMERGENCY_FUND`
- `DEBT_PAYOFF`
- `INVESTMENT`
- `PURCHASE`
- `OTHER`

**Priorities:**
- `LOW`
- `MEDIUM`
- `HIGH`

### GET /api/goals

Get all goals.

### GET /api/goals/:id

Get a single goal by ID.

### PATCH /api/goals/:id

Update a goal.

### DELETE /api/goals/:id

Delete a goal.

---

## Snapshot Endpoints

### POST /api/snapshots

Create a new snapshot with calculated metrics.

**Request Body:**
```json
{
  "name": "December 2025"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "cuid123",
    "name": "December 2025",
    "snapshotDate": "2025-12-01T00:00:00.000Z",
    "totalDebt": 50000,
    "totalCreditCardDebt": 5000,
    "totalLoanDebt": 25000,
    "totalMortgageDebt": 20000,
    "totalMonthlyPayments": 1500,
    "totalMonthlyBills": 800,
    "monthlyIncome": 5000,
    "debtToIncomeRatio": 30,
    "creditUtilization": 45,
    "totalAssets": 75000,
    "netWorth": 25000,
    ...
  },
  "message": "Snapshot created successfully"
}
```

### GET /api/snapshots

Get all snapshots.

### GET /api/snapshots/:id

Get a single snapshot by ID.

### DELETE /api/snapshots/:id

Delete a snapshot.

---

## Debt Strategy Endpoints

### GET /api/strategy/avalanche

Get avalanche payoff order (highest interest first).

**Headers:** `Authorization: Bearer <token>`

**Response:**
```json
{
  "success": true,
  "data": {
    "method": "avalanche",
    "description": "Pay off debts with highest interest rates first (saves most money)",
    "debts": [
      {
        "id": "cuid1",
        "type": "credit_card",
        "name": "Chase Freedom",
        "balance": 2500,
        "interestRate": 22.99,
        "minimumPayment": 50
      },
      ...
    ]
  }
}
```

### GET /api/strategy/snowball

Get snowball payoff order (smallest balance first).

**Response:**
```json
{
  "success": true,
  "data": {
    "method": "snowball",
    "description": "Pay off debts with smallest balances first (quick wins)",
    "debts": [ ... ]
  }
}
```

### GET /api/strategy/custom

Get custom payoff order based on user's DebtOrder preferences.

### POST /api/strategy/projection

Calculate payoff projection with extra payment.

**Request Body:**
```json
{
  "method": "avalanche",
  "extraPayment": 300
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "method": "avalanche",
    "extraPayment": 300,
    "totalMonths": 36,
    "totalInterest": 5234.50,
    "payoffDate": "December 2028",
    "debtPayoffSchedule": [
      {
        "debtId": "cuid1",
        "debtName": "Chase Freedom",
        "monthsPaid": 12,
        "totalInterest": 487.25
      },
      ...
    ]
  }
}
```

### POST /api/strategy/calculate/credit-card

Calculate credit card payoff.

**Request Body:**
```json
{
  "balance": 2500,
  "interestRate": 18.99,
  "monthlyPayment": 150
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "months": 19,
    "totalInterest": 350.75,
    "totalPaid": 2850.75
  }
}
```

### POST /api/strategy/calculate/loan

Calculate loan payment and amortization.

**Request Body:**
```json
{
  "principal": 25000,
  "annualRate": 6.5,
  "termMonths": 60,
  "includeSchedule": true
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "monthlyPayment": 488.71,
    "totalPaid": 29322.60,
    "totalInterest": 4322.60,
    "amortizationSchedule": [
      {
        "month": 1,
        "payment": 488.71,
        "principal": 353.54,
        "interest": 135.17,
        "balance": 24646.46
      },
      ...
    ]
  }
}
```

---

## Error Responses

All endpoints return errors in this format:

```json
{
  "success": false,
  "error": "Error message",
  "statusCode": 400
}
```

**Common Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request (validation error)
- `401` - Unauthorized (not authenticated)
- `404` - Not Found
- `500` - Internal Server Error

---

## Rate Limiting

API requests are rate-limited to prevent abuse. Default limits:
- 100 requests per 15 minutes per IP address

---

## Testing with cURL

### Register a new user:
```bash
curl -X POST http://localhost:3001/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "firstName": "Test",
    "lastName": "User"
  }'
```

### Login:
```bash
curl -X POST http://localhost:3001/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### Get Dashboard (with token):
```bash
curl -X GET http://localhost:3001/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

**Last Updated:** 2025-11-25
**API Version:** 1.0.0
