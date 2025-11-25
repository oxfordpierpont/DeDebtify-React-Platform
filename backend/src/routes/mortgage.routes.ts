import { Router } from 'express';
import * as mortgageController from '../controllers/mortgage.controller';
import { authenticate } from '../middleware/auth';

const router = Router();
router.use(authenticate);

router.post('/', mortgageController.createMortgage);
router.get('/', mortgageController.getAllMortgages);
router.get('/:id', mortgageController.getMortgageById);
router.patch('/:id', mortgageController.updateMortgage);
router.delete('/:id', mortgageController.deleteMortgage);

export default router;
