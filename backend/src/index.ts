import dotenv from 'dotenv';
import app from './app';
import { env } from './config/env';

// Load environment variables
dotenv.config();

// Validate environment variables (will exit if invalid)
console.log('✅ Environment variables validated successfully');

const PORT = env.PORT || 3001;

app.listen(PORT, () => {
  console.log(`🚀 DeDebtify API Server running on port ${PORT}`);
  console.log(`📊 Environment: ${env.NODE_ENV}`);
  console.log(`🔗 Health check: http://localhost:${PORT}/health`);
});

process.on('unhandledRejection', (reason, promise) => {
  console.error('Unhandled Rejection at:', promise, 'reason:', reason);
  process.exit(1);
});

process.on('uncaughtException', (error) => {
  console.error('Uncaught Exception:', error);
  process.exit(1);
});
