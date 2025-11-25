import { Router } from 'express';
import * as loanController from '../controllers/loan.controller';
import { authenticate } from '../middleware/auth';

const router = Router();
router.use(authenticate);

router.post('/', loanController.createLoan);
router.get('/', loanController.getAllLoans);
router.get('/:id', loanController.getLoanById);
router.patch('/:id', loanController.updateLoan);
router.delete('/:id', loanController.deleteLoan);

export default router;
