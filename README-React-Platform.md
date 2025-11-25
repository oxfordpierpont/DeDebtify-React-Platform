# DeDebtify React Platform

> **Modern React-based debt management platform**
> Converted from WordPress plugin to standalone web application

**Version:** 2.0.0 (React Platform)
**Original WordPress Plugin:** 1.0.0
**Author:** Oxford Pierpont

---

## 🚀 Overview

DeDebtify is a comprehensive debt management and financial tracking platform that helps users:
- Track credit cards, loans, mortgages, and bills
- Calculate payoff strategies (Debt Avalanche & Snowball methods)
- Visualize financial progress with charts
- Connect bank accounts via Plaid integration
- Set and monitor financial goals
- Generate monthly snapshots to track progress over time

This is the **React Platform version** - a complete rewrite of the original WordPress plugin using modern web technologies.

---

## 🏗️ Architecture

### Monorepo Structure

```
DeDebtify-React-Platform/
├── backend/              # Node.js + Express + Prisma + PostgreSQL
│   ├── src/
│   │   ├── routes/      # API route handlers
│   │   ├── services/    # Business logic
│   │   ├── middleware/  # Auth, error handling, etc.
│   │   ├── utils/       # Helper functions
│   │   └── types/       # TypeScript type definitions
│   ├── prisma/
│   │   └── schema.prisma
│   └── package.json
│
├── frontend/            # React + TypeScript + Vite
│   ├── src/
│   │   ├── components/  # Reusable React components
│   │   ├── pages/       # Page-level components
│   │   ├── hooks/       # Custom React hooks
│   │   ├── lib/         # Utilities and API client
│   │   ├── stores/      # Zustand state management
│   │   └── types/       # TypeScript types
│   └── package.json
│
├── docs/                # Documentation
├── docker-compose.yml   # Local development setup
└── README.md            # This file
```

---

## 🛠️ Tech Stack

### Backend
- **Runtime:** Node.js 20+
- **Framework:** Express.js (REST API)
- **Database:** PostgreSQL 16
- **ORM:** Prisma
- **Authentication:** JWT (jsonwebtoken)
- **Password Hashing:** bcryptjs
- **Validation:** Zod
- **API Integration:** Plaid SDK (bank account linking)
- **Language:** TypeScript

### Frontend
- **Framework:** React 18+
- **Build Tool:** Vite
- **Language:** TypeScript
- **Routing:** React Router v6
- **Data Fetching:** React Query (TanStack Query)
- **State Management:** Zustand
- **Forms:** React Hook Form + Zod validation
- **Styling:** Tailwind CSS
- **Charts:** Recharts
- **Icons:** Lucide React
- **HTTP Client:** Axios

### DevOps
- **Containerization:** Docker + Docker Compose
- **Deployment:** Dokploy (planned)
- **Database Migrations:** Prisma Migrate

---

## 📋 Prerequisites

- Node.js 20+ and npm
- Docker and Docker Compose (for local development)
- PostgreSQL 16 (if running without Docker)

---

## 🚦 Quick Start

### 1. Clone the Repository

```bash
git clone https://github.com/oxfordpierpont/DeDebtify-React-Platform.git
cd DeDebtify-React-Platform
```

### 2. Set Up Environment Variables

#### Backend (.env)
```bash
cp backend/.env.example backend/.env
```

Edit `backend/.env` with your configuration:
```env
DATABASE_URL="postgresql://dedebtify:password@localhost:5432/dedebtify"
JWT_SECRET="your-super-secret-jwt-key"
JWT_EXPIRES_IN="7d"
PLAID_CLIENT_ID="your-plaid-client-id"
PLAID_SECRET="your-plaid-secret"
PLAID_ENV="sandbox"
FRONTEND_URL="http://localhost:5173"
PORT=3001
NODE_ENV="development"
```

#### Frontend (.env)
```bash
cp frontend/.env.example frontend/.env
```

Edit `frontend/.env`:
```env
VITE_API_URL=http://localhost:3001/api
```

### 3. Run with Docker Compose (Recommended)

```bash
docker-compose up
```

This starts:
- PostgreSQL on port **5432**
- Backend API on port **3001**
- Frontend on port **5173**

Access the app at: **http://localhost:5173**

### 4. OR Run Manually (Without Docker)

#### Install Dependencies
```bash
# Backend
cd backend
npm install

# Frontend
cd ../frontend
npm install
```

#### Set Up Database
```bash
cd backend
npx prisma generate
npx prisma migrate dev
```

#### Run Development Servers
```bash
# Terminal 1 - Backend
cd backend
npm run dev

# Terminal 2 - Frontend
cd frontend
npm run dev
```

---

## 📚 API Documentation

### Authentication Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register new user |
| POST | `/api/auth/login` | Login user |
| POST | `/api/auth/refresh` | Refresh JWT token |

### Dashboard

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/dashboard` | Get user dashboard with all metrics |

### Credit Cards

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/credit-cards` | Get all credit cards |
| POST | `/api/credit-cards` | Create credit card |
| PUT | `/api/credit-cards/:id` | Update credit card |
| DELETE | `/api/credit-cards/:id` | Delete credit card |

### Loans

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/loans` | Get all loans |
| POST | `/api/loans` | Create loan |
| PUT | `/api/loans/:id` | Update loan |
| DELETE | `/api/loans/:id` | Delete loan |

### Bills, Goals, Snapshots

Similar CRUD endpoints available for:
- `/api/bills`
- `/api/goals`
- `/api/snapshots`
- `/api/mortgages`

### Calculations

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/calculate/payoff` | Calculate debt payoff scenarios |
| POST | `/api/calculate/loan-payment` | Calculate loan payment |

### Plaid Integration

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/plaid/link-token` | Create Plaid Link token |
| POST | `/api/plaid/exchange-token` | Exchange public token |
| POST | `/api/plaid/sync` | Sync account data |
| GET | `/api/plaid/accounts` | Get linked accounts |

---

## 🧮 Calculation Formulas

### Credit Card Payoff
```
Months to Payoff = -log(1 - (Balance × MonthlyRate / Payment)) / log(1 + MonthlyRate)
```

### Loan Payment (Amortization)
```
Payment = Principal × [MonthlyRate × (1 + MonthlyRate)^Months] / [(1 + MonthlyRate)^Months - 1]
```

### DTI (Debt-to-Income Ratio)
```
DTI = (Total Monthly Debt Payments / Monthly Gross Income) × 100
```

### Credit Utilization
```
Utilization = (Total Credit Card Balances / Total Credit Limits) × 100
```

---

## 🗄️ Database Schema

The database schema includes:

- **Users** - User accounts with authentication
- **CreditCards** - Credit card tracking
- **Loans** - Personal loans, auto loans, etc.
- **Mortgages** - Home mortgages
- **Bills** - Recurring bills and expenses
- **Goals** - Financial goals
- **Snapshots** - Monthly financial snapshots
- **PlaidItems** - Plaid bank connections
- **PlaidAccounts** - Linked bank accounts

See `backend/prisma/schema.prisma` for full schema details.

---

## 📝 Development Scripts

### Backend
```bash
npm run dev          # Start development server with hot reload
npm run build        # Compile TypeScript
npm run start        # Start production server
npm run prisma:generate  # Generate Prisma Client
npm run prisma:migrate   # Run database migrations
npm run prisma:studio    # Open Prisma Studio (GUI)
```

### Frontend
```bash
npm run dev          # Start development server
npm run build        # Build for production
npm run preview      # Preview production build
npm run lint         # Run ESLint
```

---

## 🔒 Security Features

- JWT-based authentication
- Password hashing with bcryptjs
- CORS protection
- Helmet.js security headers
- Rate limiting
- Input validation with Zod
- SQL injection protection via Prisma ORM

---

## 📦 Deployment

### Using Dokploy

1. Push code to Git repository
2. Configure Dokploy with this repository
3. Set environment variables in Dokploy dashboard
4. Deploy!

### Manual Deployment

1. Build backend: `cd backend && npm run build`
2. Build frontend: `cd frontend && npm run build`
3. Deploy `backend/dist` to Node.js server
4. Deploy `frontend/dist` to static hosting (Vercel, Netlify, etc.)
5. Configure PostgreSQL database
6. Set environment variables

---

## 🗺️ Roadmap

### ✅ Phase 1: Foundation (Current)
- [x] Monorepo setup
- [x] Backend API structure
- [x] Frontend React structure
- [x] Database schema
- [x] Docker setup

### 🔄 Phase 2: Core Features (Next)
- [ ] Authentication system
- [ ] Credit card CRUD
- [ ] Loan CRUD
- [ ] Dashboard with metrics
- [ ] Calculation engine

### 📅 Phase 3: Advanced Features
- [ ] Plaid bank integration
- [ ] Charts and visualizations
- [ ] Debt payoff strategies
- [ ] Goal tracking
- [ ] Snapshot system

### 🚀 Phase 4: Polish
- [ ] Mobile responsive design
- [ ] PWA features
- [ ] Email notifications
- [ ] PDF reports
- [ ] Onboarding flow

---

## 🐛 Troubleshooting

### Database Connection Issues
```bash
# Reset database
cd backend
npx prisma migrate reset
npx prisma generate
```

### Port Already in Use
```bash
# Find and kill process using port 3001
lsof -ti:3001 | xargs kill -9

# Or change PORT in backend/.env
```

### Docker Issues
```bash
# Clean up and restart
docker-compose down -v
docker-compose up --build
```

---

## 📄 License

GPL-2.0+

---

## 🙋 Support

For issues and questions:
- GitHub Issues: [Create an issue](https://github.com/oxfordpierpont/DeDebtify-React-Platform/issues)
- Email: support@dedebtify.com

---

## 🔗 Links

- Original WordPress Plugin: See `README.md`
- Plaid Integration Guide: See `PLAID_INTEGRATION.md`
- Product Requirements: See `dedebtify-PRD.md`

---

**Built with ❤️ by Oxford Pierpont**
