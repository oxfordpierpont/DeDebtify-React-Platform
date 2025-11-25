/**
 * Credit Card Routes
 */

import { Router } from 'express';
import * as creditCardController from '../controllers/creditCard.controller';
import { authenticate } from '../middleware/auth';

const router = Router();

// All routes require authentication
router.use(authenticate);

router.post('/', creditCardController.createCreditCard);
router.get('/', creditCardController.getAllCreditCards);
router.get('/:id', creditCardController.getCreditCardById);
router.patch('/:id', creditCardController.updateCreditCard);
router.delete('/:id', creditCardController.deleteCreditCard);

export default router;
