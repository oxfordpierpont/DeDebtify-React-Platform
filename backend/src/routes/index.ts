import { Router } from 'express';
import authRoutes from './auth.routes';
import dashboardRoutes from './dashboard.routes';
import creditCardRoutes from './creditCard.routes';
import loanRoutes from './loan.routes';
import mortgageRoutes from './mortgage.routes';
import billRoutes from './bill.routes';
import goalRoutes from './goal.routes';
import snapshotRoutes from './snapshot.routes';
import strategyRoutes from './strategy.routes';

const router = Router();

// Health check for API
router.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    message: 'DeDebtify API is running',
    version: '1.0.0',
  });
});

// Route modules
router.use('/auth', authRoutes);
router.use('/dashboard', dashboardRoutes);
router.use('/credit-cards', creditCardRoutes);
router.use('/loans', loanRoutes);
router.use('/mortgages', mortgageRoutes);
router.use('/bills', billRoutes);
router.use('/goals', goalRoutes);
router.use('/snapshots', snapshotRoutes);
router.use('/strategy', strategyRoutes);

export default router;
