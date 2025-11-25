import { Router } from 'express';

const router = Router();

// Health check for API
router.get('/health', (req, res) => {
  res.json({
    status: 'ok',
    message: 'DeDebtify API is running',
    version: '1.0.0',
  });
});

// TODO: Add route modules here
// router.use('/auth', authRoutes);
// router.use('/dashboard', dashboardRoutes);
// router.use('/credit-cards', creditCardRoutes);
// router.use('/loans', loanRoutes);
// router.use('/mortgages', mortgageRoutes);
// router.use('/bills', billRoutes);
// router.use('/goals', goalRoutes);
// router.use('/snapshots', snapshotRoutes);
// router.use('/plaid', plaidRoutes);
// router.use('/calculate', calculationRoutes);

export default router;
