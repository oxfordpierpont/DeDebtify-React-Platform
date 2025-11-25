import { Router } from 'express';
import * as goalController from '../controllers/goal.controller';
import { authenticate } from '../middleware/auth';

const router = Router();
router.use(authenticate);

router.post('/', goalController.createGoal);
router.get('/', goalController.getAllGoals);
router.get('/:id', goalController.getGoalById);
router.patch('/:id', goalController.updateGoal);
router.delete('/:id', goalController.deleteGoal);

export default router;
