import { Router } from 'express';
import * as strategyController from '../controllers/strategy.controller';
import { authenticate } from '../middleware/auth';

const router = Router();
router.use(authenticate);

// Strategy endpoints
router.get('/avalanche', strategyController.getAvalanche);
router.get('/snowball', strategyController.getSnowball);
router.get('/custom', strategyController.getCustom);
router.post('/projection', strategyController.getProjection);

// Calculation endpoints
router.post('/calculate/credit-card', strategyController.calculateCreditCardPayoff);
router.post('/calculate/loan', strategyController.calculateLoan);

export default router;
