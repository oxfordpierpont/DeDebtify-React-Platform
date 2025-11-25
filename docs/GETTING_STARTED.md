# Getting Started with DeDebtify React Platform

## Prerequisites

Before you begin, ensure you have the following installed:

- **Node.js** 20 or higher ([Download](https://nodejs.org/))
- **npm** (comes with Node.js)
- **Docker** and **Docker Compose** ([Download](https://www.docker.com/))
- **Git** ([Download](https://git-scm.com/))

---

## Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/oxfordpierpont/DeDebtify-React-Platform.git
cd DeDebtify-React-Platform
```

### Step 2: Install Dependencies

#### Backend
```bash
cd backend
npm install
cd ..
```

#### Frontend
```bash
cd frontend
npm install
cd ..
```

---

## Configuration

### Step 3: Set Up Environment Variables

#### Backend Environment

```bash
cp backend/.env.example backend/.env
```

Edit `backend/.env`:

```env
# Required
DATABASE_URL="postgresql://dedebtify:dedebtify_dev_password@localhost:5432/dedebtify"
JWT_SECRET="your-super-secret-jwt-key-min-32-characters-long"

# Optional (for Plaid integration)
PLAID_CLIENT_ID="your-plaid-client-id"
PLAID_SECRET="your-plaid-secret"
PLAID_ENV="sandbox"
```

#### Frontend Environment

```bash
cp frontend/.env.example frontend/.env
```

Edit `frontend/.env`:

```env
VITE_API_URL=http://localhost:3001/api
```

---

## Running the Application

### Option 1: Using Docker Compose (Recommended)

This is the easiest way to get started. Docker Compose will start PostgreSQL, backend, and frontend all at once.

```bash
docker-compose up
```

Wait for all services to start. You should see:
```
dedebtify-postgres   | ready to accept connections
dedebtify-backend    | 🚀 DeDebtify API Server running on port 3001
dedebtify-frontend   | ➜  Local: http://localhost:5173/
```

Open your browser and go to: **http://localhost:5173**

### Option 2: Manual Setup (Without Docker)

#### 1. Start PostgreSQL

If you have PostgreSQL installed locally:

```bash
# Create database
createdb dedebtify
```

Or use Docker for just PostgreSQL:

```bash
docker run -d \
  --name dedebtify-postgres \
  -e POSTGRES_USER=dedebtify \
  -e POSTGRES_PASSWORD=dedebtify_dev_password \
  -e POSTGRES_DB=dedebtify \
  -p 5432:5432 \
  postgres:16-alpine
```

#### 2. Set Up Database Schema

```bash
cd backend
npx prisma generate
npx prisma migrate dev
```

#### 3. Start Backend

```bash
cd backend
npm run dev
```

You should see:
```
🚀 DeDebtify API Server running on port 3001
```

#### 4. Start Frontend (in a new terminal)

```bash
cd frontend
npm run dev
```

You should see:
```
➜  Local: http://localhost:5173/
```

Open your browser and go to: **http://localhost:5173**

---

## Verify Installation

### Check Backend Health

```bash
curl http://localhost:3001/health
```

Expected response:
```json
{
  "status": "ok",
  "timestamp": "2025-11-25T...",
  "uptime": 5.123,
  "environment": "development"
}
```

### Check Frontend

Open **http://localhost:5173** in your browser. You should see the DeDebtify login page.

---

## Development Workflow

### Backend Development

```bash
cd backend

# Start dev server with hot reload
npm run dev

# Run Prisma Studio (database GUI)
npm run prisma:studio

# Generate Prisma Client after schema changes
npm run prisma:generate

# Create a new migration
npm run prisma:migrate

# View logs
docker logs -f dedebtify-backend
```

### Frontend Development

```bash
cd frontend

# Start dev server
npm run dev

# Run linter
npm run lint

# Build for production
npm run build

# Preview production build
npm run preview
```

### Database Management

#### Prisma Studio (GUI)

```bash
cd backend
npm run prisma:studio
```

Opens at **http://localhost:5555**

#### View Database in Terminal

```bash
docker exec -it dedebtify-postgres psql -U dedebtify
```

Then run SQL:
```sql
\dt              -- List all tables
\d users         -- Describe users table
SELECT * FROM users;
```

---

## Common Tasks

### Reset Database

```bash
cd backend
npx prisma migrate reset
```

This will:
1. Drop the database
2. Create a new database
3. Run all migrations
4. Run seed data (if configured)

### Add a New Database Table

1. Edit `backend/prisma/schema.prisma`
2. Add your model:
   ```prisma
   model Example {
     id        String   @id @default(cuid())
     name      String
     createdAt DateTime @default(now())
     updatedAt DateTime @updatedAt
   }
   ```
3. Create migration:
   ```bash
   cd backend
   npx prisma migrate dev --name add_example_table
   ```
4. Generate Prisma Client:
   ```bash
   npx prisma generate
   ```

### Stop All Services

```bash
# If using Docker Compose
docker-compose down

# Remove volumes (deletes database data)
docker-compose down -v
```

---

## Testing

### Backend Tests

```bash
cd backend
npm test
```

### Frontend Tests

```bash
cd frontend
npm test
```

---

## Troubleshooting

### Port Already in Use

**Backend (3001):**
```bash
# Find process
lsof -i :3001

# Kill process
kill -9 <PID>
```

**Frontend (5173):**
```bash
lsof -i :5173
kill -9 <PID>
```

### Database Connection Failed

1. Check if PostgreSQL is running:
   ```bash
   docker ps | grep postgres
   ```

2. Check DATABASE_URL in `backend/.env`

3. Restart PostgreSQL:
   ```bash
   docker restart dedebtify-postgres
   ```

### Prisma Issues

```bash
cd backend

# Reset Prisma Client
rm -rf node_modules/.prisma
npx prisma generate

# Reset entire database
npx prisma migrate reset
```

### Docker Issues

```bash
# View logs
docker logs dedebtify-backend
docker logs dedebtify-frontend
docker logs dedebtify-postgres

# Rebuild containers
docker-compose up --build

# Clean up everything
docker-compose down -v
docker system prune -a
```

### Frontend Build Errors

```bash
cd frontend

# Clear cache
rm -rf node_modules dist
npm install
npm run dev
```

---

## Environment URLs

| Service | Development | Production |
|---------|------------|------------|
| Frontend | http://localhost:5173 | https://app.dedebtify.com |
| Backend API | http://localhost:3001 | https://api.dedebtify.com |
| PostgreSQL | localhost:5432 | (managed database) |
| Prisma Studio | http://localhost:5555 | N/A |

---

## Next Steps

### For Development

1. **Task 2:** Create database schema based on WordPress CPTs
   - Read `includes/class-dedebtify-cpt.php`
   - Update `backend/prisma/schema.prisma`

2. **Task 3:** Port calculation functions
   - Read `includes/class-dedebtify-calculations.php`
   - Create `backend/src/utils/calculations.ts`

3. **Task 4:** Build REST API
   - Create route handlers
   - Add authentication
   - Connect to database

4. **Task 5:** Build React frontend
   - Create components
   - Build forms
   - Add charts

### For Deployment

See `docs/DEPLOYMENT.md` (coming soon)

---

## Useful Commands Reference

```bash
# Backend
cd backend
npm run dev              # Start dev server
npm run build            # Compile TypeScript
npm run start            # Start production server
npm run prisma:generate  # Generate Prisma Client
npm run prisma:migrate   # Run migrations
npm run prisma:studio    # Open Prisma Studio GUI

# Frontend
cd frontend
npm run dev              # Start dev server
npm run build            # Build for production
npm run preview          # Preview production build
npm run lint             # Run ESLint

# Docker
docker-compose up        # Start all services
docker-compose down      # Stop all services
docker-compose logs -f   # Follow logs
docker ps                # List running containers
docker exec -it <name> bash  # SSH into container

# Database
psql -U dedebtify -d dedebtify  # Connect to database
docker exec -it dedebtify-postgres psql -U dedebtify  # Connect via Docker
```

---

## Additional Resources

- [Prisma Documentation](https://www.prisma.io/docs/)
- [React Documentation](https://react.dev/)
- [Vite Documentation](https://vitejs.dev/)
- [Express Documentation](https://expressjs.com/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)
- [React Query Documentation](https://tanstack.com/query/latest)

---

## Getting Help

- **GitHub Issues:** [Report a bug](https://github.com/oxfordpierpont/DeDebtify-React-Platform/issues)
- **Documentation:** See `docs/` folder
- **Migration Notes:** See `docs/MIGRATION_NOTES.md`

---

**Ready to start developing? Run `docker-compose up` and open http://localhost:5173 🚀**
