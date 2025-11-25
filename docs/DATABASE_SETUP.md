# Database Setup Guide

## Prerequisites

- PostgreSQL 16 installed (or using Docker)
- Node.js 20+ installed
- Backend dependencies installed (`npm install` in backend/)

---

## Step 1: Start PostgreSQL

### Option A: Using Docker Compose (Recommended)

```bash
# From project root
docker-compose up postgres
```

This will start PostgreSQL on port 5432 with:
- Database: `dedebtify`
- User: `dedebtify`
- Password: `dedebtify_dev_password`

### Option B: Using Docker Directly

```bash
docker run -d \
  --name dedebtify-postgres \
  -e POSTGRES_USER=dedebtify \
  -e POSTGRES_PASSWORD=dedebtify_dev_password \
  -e POSTGRES_DB=dedebtify \
  -p 5432:5432 \
  postgres:16-alpine
```

### Option C: Local PostgreSQL Installation

If you have PostgreSQL installed locally:

```bash
# Create database
createdb dedebtify

# Or using psql
psql -U postgres
CREATE DATABASE dedebtify;
\q
```

---

## Step 2: Configure Environment Variables

Create `backend/.env` from the template:

```bash
cd backend
cp .env.example .env
```

Edit `.env` and set the DATABASE_URL:

```env
DATABASE_URL="postgresql://dedebtify:dedebtify_dev_password@localhost:5432/dedebtify"
```

**For local PostgreSQL:**
```env
DATABASE_URL="postgresql://your_user:your_password@localhost:5432/dedebtify"
```

---

## Step 3: Generate Prisma Client

```bash
cd backend
npx prisma generate
```

This generates the Prisma Client based on your schema.

---

## Step 4: Run Migrations

### Create Initial Migration

```bash
npx prisma migrate dev --name init
```

This will:
1. Create all tables
2. Create all enums
3. Set up indexes
4. Set up foreign key relationships

### Or Push Schema Directly (Development Only)

For rapid prototyping:

```bash
npx prisma db push
```

This pushes the schema without creating migration files.

---

## Step 5: Seed Database (Optional)

To populate the database with test data:

```bash
npx prisma db seed
```

This creates:
- 1 test user (test@dedebtify.com / password123)
- 2 credit cards
- 2 loans
- 1 mortgage
- 4 bills
- 2 goals
- 1 snapshot

---

## Step 6: Verify Setup

### Check Database Connection

```bash
npx prisma studio
```

This opens Prisma Studio at http://localhost:5555 where you can:
- View all tables
- Browse data
- Edit records
- Test relationships

### Or Use psql

```bash
psql -U dedebtify -d dedebtify

# List tables
\dt

# Describe a table
\d users

# Query data
SELECT * FROM users;
```

---

## Common Commands

### View Database Schema

```bash
npx prisma db pull
```

### Reset Database (⚠️ Deletes All Data)

```bash
npx prisma migrate reset
```

This will:
1. Drop the database
2. Create a new database
3. Run all migrations
4. Run seed script

### Create New Migration

After editing `schema.prisma`:

```bash
npx prisma migrate dev --name describe_your_change
```

Example:
```bash
npx prisma migrate dev --name add_user_preferences
```

### Deploy Migrations (Production)

```bash
npx prisma migrate deploy
```

### View Migration Status

```bash
npx prisma migrate status
```

---

## Schema Validation

Before migrating, validate your schema:

```bash
npx prisma validate
```

This checks for:
- Syntax errors
- Invalid relationships
- Type mismatches

---

## Troubleshooting

### Error: Can't reach database server

**Problem:** PostgreSQL is not running

**Solution:**
```bash
# Check if PostgreSQL is running
docker ps | grep postgres

# Restart PostgreSQL
docker restart dedebtify-postgres

# Or restart docker-compose
docker-compose restart postgres
```

### Error: Database does not exist

**Problem:** Database hasn't been created

**Solution:**
```bash
# Create database manually
docker exec -it dedebtify-postgres createdb -U dedebtify dedebtify

# Or use psql
docker exec -it dedebtify-postgres psql -U dedebtify
CREATE DATABASE dedebtify;
```

### Error: Migration failed

**Problem:** Schema conflicts or data issues

**Solution:**
```bash
# Reset database (caution: deletes all data)
npx prisma migrate reset

# Or resolve conflicts manually
npx prisma migrate resolve --applied [migration_name]
```

### Error: Prisma Client out of sync

**Problem:** Schema changed but client not regenerated

**Solution:**
```bash
npx prisma generate
```

### Error: Connection pool timeout

**Problem:** Too many database connections

**Solution:**
```typescript
// In prisma.ts, configure connection pool
const prisma = new PrismaClient({
  datasources: {
    db: {
      url: process.env.DATABASE_URL,
    },
  },
  log: ['query', 'error', 'warn'],
})
```

---

## Production Deployment

### 1. Environment Variables

Set these in your production environment:

```env
DATABASE_URL="postgresql://user:password@host:5432/dedebtify?schema=public&sslmode=require"
NODE_ENV="production"
```

### 2. Run Migrations

```bash
npx prisma migrate deploy
```

### 3. Generate Client

```bash
npx prisma generate
```

### 4. Connection Pooling

For production, use connection pooling:

**Recommended:** PgBouncer or Prisma Data Proxy

Example with PgBouncer:
```env
DATABASE_URL="postgresql://user:password@pgbouncer:6432/dedebtify"
DIRECT_URL="postgresql://user:password@postgres:5432/dedebtify"
```

Update `schema.prisma`:
```prisma
datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
  directUrl = env("DIRECT_URL")
}
```

### 5. Backup Strategy

Set up automated backups:

```bash
# Daily backup example
pg_dump -U dedebtify dedebtify > backup_$(date +%Y%m%d).sql

# Restore from backup
psql -U dedebtify dedebtify < backup_20251125.sql
```

---

## Database Maintenance

### Analyze Tables

```sql
ANALYZE users;
ANALYZE credit_cards;
ANALYZE loans;
-- etc.
```

### Vacuum Database

```sql
VACUUM ANALYZE;
```

### Check Table Sizes

```sql
SELECT
  schemaname,
  tablename,
  pg_size_pretty(pg_total_relation_size(schemaname||'.'||tablename)) AS size
FROM pg_tables
WHERE schemaname = 'public'
ORDER BY pg_total_relation_size(schemaname||'.'||tablename) DESC;
```

### Check Index Usage

```sql
SELECT
  schemaname,
  tablename,
  indexname,
  idx_scan as number_of_scans,
  idx_tup_read as tuples_read,
  idx_tup_fetch as tuples_fetched
FROM pg_stat_user_indexes
WHERE schemaname = 'public'
ORDER BY idx_scan DESC;
```

---

## Security Best Practices

### 1. Database User Permissions

Create a limited user for the application:

```sql
-- Create user
CREATE USER dedebtify_app WITH PASSWORD 'secure_password';

-- Grant only necessary permissions
GRANT CONNECT ON DATABASE dedebtify TO dedebtify_app;
GRANT USAGE ON SCHEMA public TO dedebtify_app;
GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO dedebtify_app;
GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO dedebtify_app;
```

### 2. Enable SSL

```env
DATABASE_URL="postgresql://user:password@host:5432/dedebtify?sslmode=require"
```

### 3. Rotate Passwords Regularly

Use secrets management:
- AWS Secrets Manager
- HashiCorp Vault
- Environment variable management

### 4. Monitor Connections

```sql
SELECT count(*) FROM pg_stat_activity WHERE datname = 'dedebtify';
```

---

## Performance Tuning

### Connection Pool Configuration

```typescript
const prisma = new PrismaClient({
  datasources: {
    db: {
      url: process.env.DATABASE_URL,
    },
  },
  // Connection pool settings
  log: process.env.NODE_ENV === 'development' ? ['query'] : ['error'],
})

// For production
// pool_timeout = 10
// connection_limit = 10
```

Add to DATABASE_URL:
```
?connection_limit=10&pool_timeout=10
```

### Enable Query Logging

```typescript
const prisma = new PrismaClient({
  log: [
    {
      emit: 'event',
      level: 'query',
    },
    'error',
    'warn',
  ],
})

prisma.$on('query', (e) => {
  console.log('Query: ' + e.query)
  console.log('Duration: ' + e.duration + 'ms')
})
```

---

## Schema Evolution

### Adding a New Field

1. Edit `schema.prisma`
2. Create migration
3. Deploy

Example:
```prisma
model User {
  // ... existing fields
  phoneNumber String? // New field
}
```

```bash
npx prisma migrate dev --name add_user_phone_number
```

### Renaming a Field

Use `@map`:
```prisma
model User {
  fullName String @map("full_name")
}
```

### Adding a New Table

```prisma
model PaymentHistory {
  id        String   @id @default(cuid())
  userId    String
  amount    Decimal  @db.Decimal(10, 2)
  paidAt    DateTime
  user      User     @relation(fields: [userId], references: [id])

  @@map("payment_history")
}
```

```bash
npx prisma migrate dev --name add_payment_history
```

---

**Last Updated:** 2025-11-25
**Prisma Version:** 5.20.0
