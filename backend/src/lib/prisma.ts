/**
 * Prisma Client Singleton
 *
 * Creates a single Prisma client instance for the entire application.
 * Prevents creating multiple instances in development due to hot reload.
 */

import { PrismaClient } from '@prisma/client';

// Extend global namespace to store Prisma client
declare global {
  var prisma: PrismaClient | undefined;
}

// Create Prisma client instance
const prisma = global.prisma || new PrismaClient({
  log: process.env.NODE_ENV === 'development' ? ['query', 'error', 'warn'] : ['error'],
});

// Store in global during development to prevent multiple instances
if (process.env.NODE_ENV !== 'production') {
  global.prisma = prisma;
}

export default prisma;
