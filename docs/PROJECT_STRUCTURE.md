# DeDebtify React Platform - Project Structure

## Directory Tree

```
DeDebtify-React-Platform/
├── backend/                          # Node.js + Express Backend
│   ├── src/
│   │   ├── index.ts                 # Application entry point
│   │   ├── app.ts                   # Express app configuration
│   │   ├── routes/
│   │   │   └── index.ts             # Main router (TODO: add route modules)
│   │   ├── services/                # Business logic layer
│   │   ├── middleware/
│   │   │   ├── auth.ts              # JWT authentication
│   │   │   └── errorHandler.ts     # Global error handling
│   │   ├── utils/
│   │   │   └── prisma.ts            # Prisma client singleton
│   │   └── types/                   # TypeScript type definitions
│   ├── prisma/
│   │   └── schema.prisma            # Database schema (basic User model)
│   ├── package.json
│   ├── tsconfig.json
│   ├── Dockerfile
│   └── .env.example
│
├── frontend/                         # React + TypeScript Frontend
│   ├── src/
│   │   ├── main.tsx                 # React entry point
│   │   ├── App.tsx                  # Main app with routing
│   │   ├── index.css                # Global styles (Tailwind)
│   │   ├── components/
│   │   │   └── Layout.tsx           # Main layout with sidebar
│   │   ├── pages/
│   │   │   ├── Dashboard.tsx        # Dashboard page (stub)
│   │   │   ├── Login.tsx            # Login page (stub)
│   │   │   ├── Register.tsx         # Register page (stub)
│   │   │   ├── CreditCards.tsx      # Credit cards page (stub)
│   │   │   ├── Loans.tsx            # Loans page (stub)
│   │   │   ├── Bills.tsx            # Bills page (stub)
│   │   │   ├── Goals.tsx            # Goals page (stub)
│   │   │   └── ActionPlan.tsx       # Action plan page (stub)
│   │   ├── hooks/                   # Custom React hooks
│   │   ├── lib/
│   │   │   └── api.ts               # Axios API client
│   │   ├── stores/                  # Zustand state management
│   │   ├── types/
│   │   │   └── index.ts             # TypeScript types
│   │   └── assets/                  # Images, fonts, etc.
│   ├── public/                      # Static assets
│   ├── index.html                   # HTML entry point
│   ├── package.json
│   ├── tsconfig.json
│   ├── tsconfig.node.json
│   ├── vite.config.ts               # Vite configuration
│   ├── tailwind.config.js           # Tailwind CSS config
│   ├── postcss.config.js            # PostCSS config
│   ├── Dockerfile
│   └── .env.example
│
├── docs/                             # Documentation
│   ├── MIGRATION_NOTES.md           # WordPress → React migration notes
│   ├── GETTING_STARTED.md           # Quick start guide
│   └── PROJECT_STRUCTURE.md         # This file
│
├── admin/                            # WordPress plugin admin files (reference)
├── includes/                         # WordPress plugin includes (reference)
├── assets/                           # WordPress plugin assets (reference)
├── templates/                        # WordPress plugin templates (reference)
├── page-templates/                   # WordPress plugin page templates (reference)
│
├── docker-compose.yml                # Docker Compose for local dev
├── .gitignore                        # Git ignore rules
├── README.md                         # Original WordPress plugin README
├── README-React-Platform.md          # React platform README
├── PLAID_INTEGRATION.md              # Plaid integration guide
└── dedebtify-PRD.md                  # Product requirements document
```

## Component Responsibilities

### Backend

#### `src/index.ts`
- Application entry point
- Starts Express server
- Handles uncaught exceptions

#### `src/app.ts`
- Express app configuration
- Middleware setup (CORS, helmet, morgan)
- Route mounting
- Error handling

#### `src/routes/index.ts`
- Main API router
- TODO: Mount sub-routers for each entity

#### `src/middleware/auth.ts`
- JWT token verification
- User authentication
- Token generation utilities

#### `src/middleware/errorHandler.ts`
- Global error handling
- Zod validation error formatting
- JWT error handling
- Prisma error handling

#### `src/utils/prisma.ts`
- Prisma Client singleton
- Database connection management

#### `prisma/schema.prisma`
- Database schema definition
- Currently has basic User model
- TODO: Add all entity models

### Frontend

#### `src/main.tsx`
- React entry point
- React Query setup
- Router setup

#### `src/App.tsx`
- Main application component
- Route definitions
- Authentication check
- Route protection

#### `src/components/Layout.tsx`
- Application layout
- Sidebar navigation
- Header/footer
- Page wrapper

#### `src/lib/api.ts`
- Axios instance configuration
- Request/response interceptors
- Authentication token handling

#### `src/types/index.ts`
- TypeScript type definitions
- User types
- Auth types
- Entity types (CreditCard, Loan, etc.)

#### Pages (`src/pages/`)
All page components are currently stubs that will be implemented in Task 5.

### Configuration Files

#### `docker-compose.yml`
Defines three services:
1. **postgres** - PostgreSQL 16 database
2. **backend** - Node.js API server
3. **frontend** - Vite dev server

#### `backend/tsconfig.json`
- TypeScript compiler options for backend
- Target: ES2022
- Module: CommonJS
- Strict mode enabled

#### `frontend/tsconfig.json`
- TypeScript compiler options for frontend
- Target: ES2020
- Module: ESNext
- React JSX support

#### `frontend/vite.config.ts`
- Vite build configuration
- Path aliases (@/ → src/)
- Proxy configuration for API calls

#### `frontend/tailwind.config.js`
- Tailwind CSS configuration
- Custom color palette
- Content paths

## Technology Choices

### Why Express?
- Simple and flexible
- Large ecosystem
- Easy to understand
- Great for REST APIs

### Why Prisma?
- Type-safe database access
- Great TypeScript support
- Easy migrations
- Prisma Studio for GUI

### Why React Query?
- Server state management
- Automatic caching
- Background refetching
- Optimistic updates

### Why Zustand?
- Simple state management
- Less boilerplate than Redux
- TypeScript-friendly

### Why Tailwind CSS?
- Utility-first CSS
- Fast development
- Consistent design
- Great documentation

### Why Vite?
- Fast HMR (Hot Module Replacement)
- Modern build tool
- Great DX (Developer Experience)
- Native ESM support

## File Naming Conventions

### Backend
- **Routes:** `camelCase.ts` (e.g., `creditCards.ts`)
- **Services:** `camelCase.ts` (e.g., `authService.ts`)
- **Middleware:** `camelCase.ts` (e.g., `errorHandler.ts`)
- **Utilities:** `camelCase.ts` (e.g., `calculations.ts`)

### Frontend
- **Components:** `PascalCase.tsx` (e.g., `Layout.tsx`)
- **Pages:** `PascalCase.tsx` (e.g., `Dashboard.tsx`)
- **Hooks:** `camelCase.ts` with `use` prefix (e.g., `useAuth.ts`)
- **Utilities:** `camelCase.ts` (e.g., `api.ts`)
- **Types:** `index.ts` or `types.ts`

## Import Patterns

### Backend
```typescript
// External dependencies first
import express from 'express';
import { Request, Response } from 'express';

// Internal modules
import { authenticate } from '../middleware/auth';
import prisma from '../utils/prisma';

// Types
import { User } from '@prisma/client';
```

### Frontend
```typescript
// React first
import React from 'react';
import { useState, useEffect } from 'react';

// External dependencies
import { useQuery } from '@tanstack/react-query';
import { useNavigate } from 'react-router-dom';

// Internal modules
import { api } from '@/lib/api';

// Components
import { Button } from '@/components/Button';

// Types
import type { User } from '@/types';
```

## Port Allocation

| Service | Port | Purpose |
|---------|------|---------|
| Frontend (Vite) | 5173 | React development server |
| Backend (Express) | 3001 | REST API |
| PostgreSQL | 5432 | Database |
| Prisma Studio | 5555 | Database GUI (manual start) |

## Environment Variables

### Backend (`backend/.env`)
- `DATABASE_URL` - PostgreSQL connection string
- `JWT_SECRET` - Secret key for JWT signing
- `JWT_EXPIRES_IN` - Token expiration time
- `PORT` - Server port (default: 3001)
- `NODE_ENV` - Environment (development/production)
- `FRONTEND_URL` - Frontend URL for CORS
- `PLAID_CLIENT_ID` - Plaid client ID
- `PLAID_SECRET` - Plaid secret key
- `PLAID_ENV` - Plaid environment (sandbox/development/production)

### Frontend (`frontend/.env`)
- `VITE_API_URL` - Backend API URL

## Next Steps

### Task 2: Database Schema
Update `backend/prisma/schema.prisma` with all models:
- CreditCard
- Loan
- Mortgage
- Bill
- Goal
- Snapshot
- PlaidItem
- PlaidAccount

### Task 3: Calculations
Create `backend/src/utils/calculations.ts` with all formulas from WordPress plugin.

### Task 4: Backend API
Create route handlers in `backend/src/routes/`:
- `auth.ts`
- `dashboard.ts`
- `creditCards.ts`
- `loans.ts`
- `mortgages.ts`
- `bills.ts`
- `goals.ts`
- `snapshots.ts`
- `plaid.ts`
- `calculations.ts`

### Task 5: Frontend Components
Build out all page components and reusable UI components.

---

**This structure is designed for:**
- ✅ Scalability
- ✅ Maintainability
- ✅ Type safety
- ✅ Developer experience
- ✅ Production readiness
