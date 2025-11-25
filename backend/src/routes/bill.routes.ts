import { Router } from 'express';
import * as billController from '../controllers/bill.controller';
import { authenticate } from '../middleware/auth';

const router = Router();
router.use(authenticate);

router.post('/', billController.createBill);
router.get('/', billController.getAllBills);
router.get('/:id', billController.getBillById);
router.patch('/:id', billController.updateBill);
router.delete('/:id', billController.deleteBill);

export default router;
