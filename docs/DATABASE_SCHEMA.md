# DeDebtify Database Schema Documentation

## Overview

This document describes the complete PostgreSQL database schema for the DeDebtify React Platform, implemented using Prisma ORM.

---

## Database Summary

- **Database:** PostgreSQL 16
- **ORM:** Prisma
- **Total Tables:** 10
- **Total Enums:** 7

---

## Table Structure

### 1. Users Table (`users`)

Stores user authentication and profile information.

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| email | String | ✅ | Unique email address |
| password | String | ✅ | Hashed password (bcryptjs) |
| firstName | String | ❌ | User's first name |
| lastName | String | ❌ | User's last name |
| monthlyIncome | Decimal(10,2) | ❌ | User's monthly gross income |
| createdAt | DateTime | ✅ | Account creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `email` (unique)

**Relations:**
- Has many: `creditCards`, `loans`, `mortgages`, `bills`, `goals`, `snapshots`, `plaidItems`

---

### 2. Credit Cards Table (`credit_cards`)

Tracks credit card accounts and balances.

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| userId | String | ✅ | Foreign key to users |
| name | String | ✅ | Card nickname/name |
| balance | Decimal(10,2) | ✅ | Current balance |
| creditLimit | Decimal(10,2) | ✅ | Credit limit |
| interestRate | Decimal(5,2) | ✅ | APR percentage (e.g., 18.99) |
| minimumPayment | Decimal(10,2) | ✅ | Minimum monthly payment |
| extraPayment | Decimal(10,2) | ❌ | Extra payment amount (default: 0) |
| dueDay | Integer | ❌ | Day of month (1-31) |
| autoPay | Boolean | ❌ | Auto-pay enabled (default: false) |
| status | Enum | ✅ | ACTIVE, PAID_OFF, or CLOSED |
| plaidAccountId | String | ❌ | Foreign key to plaid_accounts |
| createdAt | DateTime | ✅ | Creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `userId`
- `status`
- `plaidAccountId`

**Relations:**
- Belongs to: `user`, `plaidAccount` (optional)

**Calculated Fields** (computed in application):
- Utilization: `(balance / creditLimit) * 100`
- Months to payoff (based on payment amount)

---

### 3. Loans Table (`loans`)

Tracks personal loans, auto loans, and student loans.

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| userId | String | ✅ | Foreign key to users |
| name | String | ✅ | Loan nickname |
| loanType | Enum | ✅ | PERSONAL, AUTO, STUDENT, OTHER |
| principal | Decimal(10,2) | ✅ | Original loan amount |
| currentBalance | Decimal(10,2) | ✅ | Current balance |
| interestRate | Decimal(5,2) | ✅ | APR percentage |
| termMonths | Integer | ✅ | Loan term in months |
| monthlyPayment | Decimal(10,2) | ✅ | Monthly payment amount |
| extraPayment | Decimal(10,2) | ❌ | Extra payment (default: 0) |
| startDate | DateTime | ✅ | Loan start date |
| plaidAccountId | String | ❌ | Foreign key to plaid_accounts |
| createdAt | DateTime | ✅ | Creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `userId`
- `loanType`
- `plaidAccountId`

**Relations:**
- Belongs to: `user`, `plaidAccount` (optional)

---

### 4. Mortgages Table (`mortgages`)

Tracks home mortgages with additional property costs.

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| userId | String | ✅ | Foreign key to users |
| name | String | ✅ | Property nickname |
| propertyAddress | String | ❌ | Property address |
| loanAmount | Decimal(12,2) | ✅ | Original loan amount |
| currentBalance | Decimal(12,2) | ✅ | Current balance |
| interestRate | Decimal(5,2) | ✅ | APR percentage |
| termYears | Integer | ✅ | Mortgage term in years |
| monthlyPayment | Decimal(10,2) | ✅ | P&I payment only |
| extraPayment | Decimal(10,2) | ❌ | Extra principal (default: 0) |
| propertyTax | Decimal(10,2) | ❌ | Annual property tax |
| homeownersInsurance | Decimal(10,2) | ❌ | Annual insurance |
| pmi | Decimal(10,2) | ❌ | Monthly PMI |
| startDate | DateTime | ✅ | Mortgage start date |
| plaidAccountId | String | ❌ | Foreign key to plaid_accounts |
| createdAt | DateTime | ✅ | Creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `userId`
- `plaidAccountId`

**Relations:**
- Belongs to: `user`, `plaidAccount` (optional)

**Calculated Fields:**
- Total Monthly Payment: `monthlyPayment + (propertyTax/12) + (homeownersInsurance/12) + pmi`

---

### 5. Bills Table (`bills`)

Tracks recurring bills and expenses.

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| userId | String | ✅ | Foreign key to users |
| name | String | ✅ | Bill name |
| category | Enum | ✅ | Bill category (9 options) |
| amount | Decimal(10,2) | ✅ | Bill amount |
| frequency | Enum | ✅ | WEEKLY, BI_WEEKLY, MONTHLY, QUARTERLY, ANNUALLY |
| dueDay | Integer | ❌ | Day of month (1-31) |
| autoPay | Boolean | ❌ | Auto-pay enabled (default: false) |
| isEssential | Boolean | ❌ | Essential bill flag (default: true) |
| createdAt | DateTime | ✅ | Creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `userId`
- `category`
- `frequency`

**Relations:**
- Belongs to: `user`

**Categories:**
- HOUSING
- TRANSPORTATION
- UTILITIES
- FOOD
- HEALTHCARE
- INSURANCE
- ENTERTAINMENT
- SUBSCRIPTIONS
- OTHER

---

### 6. Goals Table (`goals`)

Tracks financial goals and savings targets.

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| userId | String | ✅ | Foreign key to users |
| name | String | ✅ | Goal name |
| goalType | Enum | ✅ | Goal type (6 options) |
| targetAmount | Decimal(10,2) | ✅ | Target amount |
| currentAmount | Decimal(10,2) | ✅ | Current saved amount (default: 0) |
| monthlyContribution | Decimal(10,2) | ❌ | Monthly contribution |
| targetDate | DateTime | ❌ | Target completion date |
| priority | Enum | ✅ | LOW, MEDIUM, HIGH (default: MEDIUM) |
| createdAt | DateTime | ✅ | Creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `userId`
- `goalType`
- `priority`

**Relations:**
- Belongs to: `user`

**Goal Types:**
- SAVINGS
- EMERGENCY_FUND
- DEBT_PAYOFF
- INVESTMENT
- PURCHASE
- OTHER

**Calculated Fields:**
- Progress Percentage: `(currentAmount / targetAmount) * 100`
- Remaining Amount: `targetAmount - currentAmount`

---

### 7. Snapshots Table (`snapshots`)

Monthly financial snapshots for tracking progress over time.

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| userId | String | ✅ | Foreign key to users |
| name | String | ✅ | Snapshot name (e.g., "November 2025") |
| snapshotDate | DateTime | ✅ | Snapshot date |
| totalDebt | Decimal(12,2) | ✅ | Total debt across all types |
| totalCreditCardDebt | Decimal(12,2) | ✅ | Total CC debt (default: 0) |
| totalLoanDebt | Decimal(12,2) | ✅ | Total loan debt (default: 0) |
| totalMortgageDebt | Decimal(12,2) | ✅ | Total mortgage debt (default: 0) |
| totalMonthlyPayments | Decimal(10,2) | ✅ | Sum of all debt payments |
| totalMonthlyBills | Decimal(10,2) | ✅ | Sum of all bills (monthly equivalent) |
| monthlyIncome | Decimal(10,2) | ❌ | User's income at snapshot time |
| debtToIncomeRatio | Decimal(5,2) | ❌ | DTI percentage |
| creditUtilization | Decimal(5,2) | ❌ | Utilization percentage |
| createdAt | DateTime | ✅ | Creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `userId`
- `snapshotDate`

**Relations:**
- Belongs to: `user`

**Notes:**
- Snapshots are typically auto-generated monthly
- Used for tracking financial progress over time
- Powers trend charts and historical views

---

### 8. Plaid Items Table (`plaid_items`)

Stores Plaid bank connections (one per financial institution).

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| userId | String | ✅ | Foreign key to users |
| accessToken | String | ✅ | Encrypted Plaid access token |
| itemId | String | ✅ | Unique Plaid item ID |
| institutionId | String | ❌ | Plaid institution ID |
| institutionName | String | ❌ | Bank name (e.g., "Chase") |
| status | Enum | ✅ | ACTIVE, DISCONNECTED, ERROR |
| connectedAt | DateTime | ✅ | Connection timestamp |
| lastSyncAt | DateTime | ❌ | Last sync timestamp |
| createdAt | DateTime | ✅ | Creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `userId`
- `itemId` (unique)
- `status`

**Relations:**
- Belongs to: `user`
- Has many: `accounts` (PlaidAccount)

**Security:**
- `accessToken` must be encrypted before storage
- Never expose access tokens in API responses

---

### 9. Plaid Accounts Table (`plaid_accounts`)

Individual accounts within a Plaid connection (checking, savings, credit cards, loans).

| Column | Type | Required | Description |
|--------|------|----------|-------------|
| id | String (cuid) | ✅ | Primary key |
| plaidItemId | String | ✅ | Foreign key to plaid_items |
| accountId | String | ✅ | Unique Plaid account ID |
| accountName | String | ✅ | Account name |
| officialName | String | ❌ | Official bank account name |
| accountType | String | ✅ | checking, savings, credit card, loan, mortgage |
| accountSubtype | String | ❌ | Detailed subtype |
| currentBalance | Decimal(12,2) | ❌ | Current balance |
| availableBalance | Decimal(12,2) | ❌ | Available balance |
| limit | Decimal(12,2) | ❌ | Credit limit (for credit cards) |
| mask | String | ❌ | Last 4 digits |
| currency | String | ✅ | Currency code (default: USD) |
| lastSyncAt | DateTime | ❌ | Last sync timestamp |
| createdAt | DateTime | ✅ | Creation timestamp |
| updatedAt | DateTime | ✅ | Last update timestamp |

**Indexes:**
- `plaidItemId`
- `accountId` (unique)
- `accountType`

**Relations:**
- Belongs to: `plaidItem`
- Has many: `creditCards`, `loans`, `mortgages` (optional links)

---

## Enums

### CreditCardStatus
- `ACTIVE` - Card is active
- `PAID_OFF` - Card balance is $0
- `CLOSED` - Card account is closed

### LoanType
- `PERSONAL` - Personal loan
- `AUTO` - Auto/car loan
- `STUDENT` - Student loan
- `OTHER` - Other loan types

### BillCategory
- `HOUSING` - Rent, mortgage, HOA
- `TRANSPORTATION` - Car, gas, public transit
- `UTILITIES` - Electric, water, gas
- `FOOD` - Groceries, dining
- `HEALTHCARE` - Medical, prescriptions
- `INSURANCE` - Health, auto, life insurance
- `ENTERTAINMENT` - Movies, events
- `SUBSCRIPTIONS` - Streaming, software
- `OTHER` - Miscellaneous

### BillFrequency
- `WEEKLY` - Weekly
- `BI_WEEKLY` - Every 2 weeks
- `MONTHLY` - Monthly
- `QUARTERLY` - Every 3 months
- `ANNUALLY` - Yearly

### GoalType
- `SAVINGS` - General savings
- `EMERGENCY_FUND` - Emergency fund
- `DEBT_PAYOFF` - Debt payoff goal
- `INVESTMENT` - Investment goal
- `PURCHASE` - Specific purchase
- `OTHER` - Other goals

### GoalPriority
- `LOW` - Low priority
- `MEDIUM` - Medium priority
- `HIGH` - High priority

### PlaidItemStatus
- `ACTIVE` - Connection is active
- `DISCONNECTED` - User disconnected
- `ERROR` - Connection error

---

## Relationships Diagram

```
User (1) ──┬── (N) CreditCard
           ├── (N) Loan
           ├── (N) Mortgage
           ├── (N) Bill
           ├── (N) Goal
           ├── (N) Snapshot
           └── (N) PlaidItem

PlaidItem (1) ──── (N) PlaidAccount

PlaidAccount (1) ──┬── (N) CreditCard [optional]
                   ├── (N) Loan [optional]
                   └── (N) Mortgage [optional]
```

---

## Cascade Delete Behavior

When a user is deleted:
- ✅ All related records are CASCADE deleted
- ✅ This includes: credit cards, loans, mortgages, bills, goals, snapshots, plaid items

When a PlaidItem is deleted:
- ✅ All related PlaidAccounts are CASCADE deleted

When a PlaidAccount is deleted:
- ⚠️ Credit cards, loans, and mortgages keep their data but lose the Plaid link

---

## Migration Commands

### Generate Prisma Client
```bash
cd backend
npx prisma generate
```

### Create Migration
```bash
npx prisma migrate dev --name init
```

### Apply Migrations
```bash
npx prisma migrate deploy
```

### Reset Database (CAUTION)
```bash
npx prisma migrate reset
```

### Seed Database with Test Data
```bash
npx prisma db seed
```

### Open Prisma Studio (GUI)
```bash
npx prisma studio
```

---

## Performance Indexes

All foreign keys have indexes for optimal query performance:

- `users.email` - Unique login lookups
- `credit_cards.userId` - User's credit cards
- `credit_cards.status` - Active cards queries
- `loans.userId` - User's loans
- `loans.loanType` - Filter by loan type
- `mortgages.userId` - User's mortgages
- `bills.userId` - User's bills
- `bills.category` - Filter by category
- `goals.userId` - User's goals
- `goals.priority` - Filter by priority
- `snapshots.userId` - User's snapshots
- `snapshots.snapshotDate` - Sort by date
- `plaid_items.userId` - User's connections
- `plaid_items.itemId` - Unique item lookups
- `plaid_accounts.plaidItemId` - Item's accounts
- `plaid_accounts.accountId` - Unique account lookups

---

## Data Types and Precision

### Decimal Fields
- **Money fields:** `Decimal(10, 2)` - Up to $99,999,999.99
- **Large money fields:** `Decimal(12, 2)` - Up to $9,999,999,999.99
- **Percentages:** `Decimal(5, 2)` - Up to 999.99%

### String Fields
- **IDs:** CUID (Collision-resistant Unique Identifier)
- **Text:** VARCHAR (no explicit limit)

### Date/Time Fields
- **DateTime:** Full timestamp with timezone

---

## Security Considerations

### Password Storage
- ✅ Hashed with bcryptjs (salt rounds: 10)
- ❌ Never store plaintext passwords

### Plaid Tokens
- ✅ Access tokens must be encrypted
- ✅ Never expose in API responses
- ✅ Rotate on security events

### User Data
- ✅ All debt data belongs to user (userId foreign key)
- ✅ CASCADE delete on user deletion
- ✅ Row-level security enforced in API

---

## Future Enhancements

Potential additions for future versions:

1. **Account Sharing**
   - `shared_accounts` table
   - Multi-user access to same debt accounts

2. **Payment History**
   - `payment_history` table
   - Track actual payments made

3. **Debt Strategies**
   - `payoff_plans` table
   - Save and compare avalanche vs snowball

4. **Notifications**
   - `notifications` table
   - Bill reminders, goal milestones

5. **Documents**
   - `documents` table
   - Upload and store financial documents

6. **Budget Categories**
   - `budget_categories` table
   - Monthly budget planning

---

**Last Updated:** 2025-11-25
**Schema Version:** 1.0.0
**Prisma Version:** 5.20.0
