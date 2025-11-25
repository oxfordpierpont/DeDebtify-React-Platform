# DeDebtify Migration Notes
## WordPress Plugin → React Platform Conversion

**Date:** 2025-11-25
**Status:** Phase 1 - Foundation Complete ✅

---

## Overview

This document tracks the migration from the DeDebtify WordPress plugin to a standalone React-based web application.

### Why Migrate?

1. **Performance**: React SPA is faster than WordPress page loads
2. **Modern UX**: Better user experience with React Router and React Query
3. **Scalability**: Standalone backend can scale independently
4. **API-First**: RESTful API can support mobile apps in the future
5. **Type Safety**: Full TypeScript implementation
6. **Developer Experience**: Modern tooling (Vite, Prisma, ESLint)

---

## Architecture Comparison

### WordPress Plugin Architecture (Old)

```
WordPress
├── PHP Backend
│   ├── Custom Post Types (CPTs)
│   │   ├── dd_credit_card
│   │   ├── dd_loan
│   │   ├── dd_mortgage
│   │   ├── dd_bill
│   │   ├── dd_goal
│   │   └── dd_snapshot
│   ├── Meta Boxes
│   └── REST API Endpoints
├── MySQL Database (wp_posts + wp_postmeta)
├── jQuery Frontend
└── PHP Templates
```

### React Platform Architecture (New)

```
Monorepo
├── Backend (Node.js + Express)
│   ├── PostgreSQL Database (proper tables)
│   ├── Prisma ORM
│   ├── REST API
│   └── TypeScript
└── Frontend (React + Vite)
    ├── React Components
    ├── React Router
    ├── React Query
    └── TypeScript
```

---

## Data Model Migration

### WordPress Custom Post Types → PostgreSQL Tables

| WordPress CPT | PostgreSQL Table | Status |
|--------------|-----------------|--------|
| `dd_credit_card` | `credit_cards` | ⏳ Pending (Task 2) |
| `dd_loan` | `loans` | ⏳ Pending (Task 2) |
| `dd_mortgage` | `mortgages` | ⏳ Pending (Task 2) |
| `dd_bill` | `bills` | ⏳ Pending (Task 2) |
| `dd_goal` | `goals` | ⏳ Pending (Task 2) |
| `dd_snapshot` | `snapshots` | ⏳ Pending (Task 2) |
| `wp_users` | `users` | ✅ Basic schema created |
| N/A (new) | `plaid_items` | ⏳ Pending (Task 2) |
| N/A (new) | `plaid_accounts` | ⏳ Pending (Task 2) |

### Key Differences

**WordPress:**
- All data stored in `wp_posts` table
- Custom fields stored in `wp_postmeta` (key-value pairs)
- User ID from `wp_users`

**React Platform:**
- Each entity has its own table
- Proper columns with correct data types
- Foreign key relationships
- Indexes for performance

---

## Code Migration Status

### ✅ Phase 1: Foundation (COMPLETED)

#### Backend Structure
- [x] Node.js + Express setup
- [x] TypeScript configuration
- [x] Prisma ORM initialization
- [x] Basic Express server with health check
- [x] JWT authentication middleware
- [x] Error handling middleware
- [x] CORS configuration
- [x] Environment variable setup

#### Frontend Structure
- [x] Vite + React + TypeScript setup
- [x] React Router configuration
- [x] Tailwind CSS setup
- [x] React Query setup
- [x] API client (Axios)
- [x] Basic page components (stubs)
- [x] Layout with navigation
- [x] TypeScript types

#### DevOps
- [x] Docker Compose for local development
- [x] Dockerfiles for backend and frontend
- [x] .gitignore
- [x] README documentation
- [x] Environment file templates

### ⏳ Phase 2: Database Schema (TASK 2)

Reference files from WordPress plugin:
- `includes/class-dedebtify-cpt.php` - CPT definitions

Tasks:
- [ ] Convert all 6 CPTs to Prisma models
- [ ] Add Plaid-specific tables
- [ ] Set up relationships
- [ ] Create migrations
- [ ] Seed data for testing

### ⏳ Phase 3: Calculations (TASK 3)

Reference files:
- `includes/class-dedebtify-calculations.php` - Calculation formulas

Tasks:
- [ ] Port `calculateMonthsToPayoff()`
- [ ] Port `calculateTotalInterest()`
- [ ] Port `calculateUtilization()`
- [ ] Port `calculateDTI()`
- [ ] Port `calculateLoanPayment()`
- [ ] Port `generateAvalancheOrder()`
- [ ] Port `generateSnowballOrder()`
- [ ] Add unit tests

### ⏳ Phase 4: Backend API (TASK 4)

Reference files:
- `includes/class-dedebtify-api.php` - WordPress REST API
- `includes/class-dedebtify-rest-api.php` - Additional endpoints

Tasks:
- [ ] Authentication routes
- [ ] Dashboard endpoint
- [ ] Credit card CRUD
- [ ] Loan CRUD
- [ ] Mortgage CRUD
- [ ] Bill CRUD
- [ ] Goal CRUD
- [ ] Snapshot CRUD
- [ ] Calculation endpoints
- [ ] Plaid integration endpoints

### ⏳ Phase 5: Frontend Components (TASK 5)

Reference files:
- `admin/dashboard.php` - Dashboard UI
- `templates/dashboard.php` - Public dashboard
- `assets/js/dedebtify-public.js` - Frontend logic

Tasks:
- [ ] Dashboard page with metrics
- [ ] Credit card management
- [ ] Loan management
- [ ] Bill tracking
- [ ] Goal setting
- [ ] Action plan (payoff strategies)
- [ ] Charts and visualizations
- [ ] Forms with validation

---

## API Endpoint Mapping

### WordPress REST API → New Express API

| WordPress Endpoint | New Express Endpoint | Status |
|-------------------|---------------------|--------|
| `wp-json/dedebtify/v1/dashboard` | `GET /api/dashboard` | ⏳ Task 4 |
| `wp-json/dedebtify/v1/credit-cards` | `GET /api/credit-cards` | ⏳ Task 4 |
| `wp-json/dedebtify/v1/loans` | `GET /api/loans` | ⏳ Task 4 |
| `wp-json/dedebtify/v1/bills` | `GET /api/bills` | ⏳ Task 4 |
| `wp-json/dedebtify/v1/goals` | `GET /api/goals` | ⏳ Task 4 |
| `wp-json/dedebtify/v1/snapshots` | `GET /api/snapshots` | ⏳ Task 4 |
| `wp-json/dedebtify/v1/calculate-payoff` | `POST /api/calculate/payoff` | ⏳ Task 4 |
| N/A | `POST /api/auth/login` | ⏳ Task 4 |
| N/A | `POST /api/auth/register` | ⏳ Task 4 |
| N/A | `POST /api/plaid/link-token` | ⏳ Task 4 |

---

## Plaid Integration

### WordPress Implementation
- File: `includes/class-dedebtify-plaid.php`
- Storage: WordPress options table or user meta

### React Platform Implementation
- Files: `backend/src/services/plaid.ts`
- Storage: Dedicated `plaid_items` and `plaid_accounts` tables
- SDK: Official Plaid Node.js SDK

**Migration Notes:**
- Keep existing Plaid credentials
- Bank connections will need to be re-linked by users
- Consider migration script to preserve existing connections

---

## Calculation Engine

### Formulas to Port

All formulas in `includes/class-dedebtify-calculations.php`:

1. **Credit Card Payoff**
   ```php
   $months = -log(1 - ($balance * $monthly_rate / $payment)) / log(1 + $monthly_rate);
   ```
   → TypeScript equivalent needed

2. **Loan Payment**
   ```php
   $payment = $principal * ($monthly_rate * pow(1 + $monthly_rate, $months)) / (pow(1 + $monthly_rate, $months) - 1);
   ```
   → TypeScript equivalent needed

3. **DTI Calculation**
   ```php
   $dti = ($total_monthly_payments / $monthly_income) * 100;
   ```
   → TypeScript equivalent needed

4. **Utilization**
   ```php
   $utilization = ($total_balance / $total_limit) * 100;
   ```
   → TypeScript equivalent needed

**⚠️ Important:** Preserve exact same logic and formulas. Results must match WordPress version.

---

## Data Migration Strategy

### For Existing Users

#### Option 1: Export/Import (Recommended for MVP)
1. Create WordPress export script
2. Export user data to JSON
3. Create import script for React platform
4. Users upload JSON file to migrate

#### Option 2: Direct Database Migration (Future)
1. Write migration script
2. Map WordPress tables → PostgreSQL tables
3. Convert user passwords (WordPress uses different hashing)
4. Migrate all CPT data

#### Option 3: Live Sync (Complex)
- Maintain WordPress plugin
- Add sync functionality
- Gradually migrate users

**Recommendation:** Start with Option 1 (Export/Import) for initial launch.

---

## Breaking Changes

### Authentication
- **Old:** WordPress cookies and sessions
- **New:** JWT tokens
- **Impact:** All users must re-login

### Data Storage
- **Old:** `wp_posts` + `wp_postmeta`
- **New:** PostgreSQL with proper tables
- **Impact:** Data migration required

### API Format
- **Old:** WordPress REST API format
- **New:** Custom JSON format
- **Impact:** Mobile apps or integrations need updates

### URLs
- **Old:** `yoursite.com/dedebtify-dashboard/`
- **New:** `app.dedebtify.com`
- **Impact:** Update bookmarks

---

## Testing Checklist

### Backend API
- [ ] All endpoints return correct status codes
- [ ] Authentication works correctly
- [ ] CRUD operations work for all entities
- [ ] Calculations match WordPress version
- [ ] Error handling works
- [ ] Validation catches bad input
- [ ] Plaid integration works

### Frontend
- [ ] All pages load correctly
- [ ] Navigation works
- [ ] Forms validate correctly
- [ ] API calls work
- [ ] Error messages display
- [ ] Loading states work
- [ ] Mobile responsive
- [ ] Charts render correctly

### Integration
- [ ] Login flow works end-to-end
- [ ] CRUD operations work end-to-end
- [ ] Plaid link works
- [ ] Calculations display correctly

---

## Performance Benchmarks

### Target Metrics

| Metric | WordPress | React Target |
|--------|-----------|-------------|
| Initial Load | ~3-5s | <2s |
| Page Navigation | ~1-2s | <500ms |
| API Response | ~500ms | <200ms |
| Time to Interactive | ~5s | <3s |

---

## Security Considerations

### WordPress
- WordPress core security
- Plugin security updates
- WordPress authentication

### React Platform
- JWT token security (httpOnly cookies recommended for production)
- CORS configuration
- Rate limiting
- Input validation (Zod)
- SQL injection protection (Prisma)
- XSS protection (React's built-in escaping)
- HTTPS required in production
- Environment variable protection

---

## Deployment Strategy

### Phase 1: Beta (Internal Testing)
- Deploy to staging environment
- Test with dummy data
- Verify all features work

### Phase 2: Alpha Users
- Invite 10-20 users
- Provide migration tool
- Collect feedback

### Phase 3: Public Launch
- Open to all users
- Keep WordPress plugin running
- Gradual migration

### Phase 4: Deprecation
- Set deadline for WordPress plugin
- Final migration push
- Shut down WordPress version

---

## Rollback Plan

If critical issues occur:

1. Keep WordPress plugin running
2. Redirect users back to WordPress
3. Fix issues in React platform
4. Re-deploy when ready

**Timeline:** First 6 months, maintain both versions.

---

## Next Steps

### Immediate (Task 2)
1. Read `includes/class-dedebtify-cpt.php`
2. Create complete Prisma schema
3. Generate migrations
4. Test with seed data

### Short-term (Tasks 3-4)
1. Port calculation functions
2. Build REST API endpoints
3. Add Plaid integration
4. Write tests

### Medium-term (Task 5)
1. Build React components
2. Implement forms
3. Add charts
4. Polish UI/UX

---

## Resources

### WordPress Plugin Files (Reference)
- `includes/class-dedebtify-cpt.php` - CPT definitions
- `includes/class-dedebtify-calculations.php` - Formulas
- `includes/class-dedebtify-api.php` - API endpoints
- `includes/class-dedebtify-plaid.php` - Plaid integration
- `admin/dashboard.php` - Admin UI
- `templates/dashboard.php` - Public UI

### New React Platform Files
- `backend/prisma/schema.prisma` - Database schema
- `backend/src/utils/calculations.ts` - Calculation functions
- `backend/src/routes/` - API routes
- `frontend/src/pages/` - Page components
- `frontend/src/components/` - Reusable components

---

## Questions & Decisions

### Decided
- ✅ Use PostgreSQL (not MongoDB)
- ✅ Use Prisma ORM (not TypeORM)
- ✅ Use JWT (not sessions)
- ✅ Use React Query (not Redux)
- ✅ Use Tailwind CSS (not Material-UI)

### To Decide
- ❓ Password reset flow?
- ❓ Email notifications?
- ❓ Multi-language support?
- ❓ Mobile app plan?
- ❓ Premium features?

---

**Last Updated:** 2025-11-25
**Author:** Oxford Pierpont
**Status:** Phase 1 Complete - Ready for Task 2
