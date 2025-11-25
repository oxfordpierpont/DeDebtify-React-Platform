import { Router } from 'express';
import * as snapshotController from '../controllers/snapshot.controller';
import { authenticate } from '../middleware/auth';

const router = Router();
router.use(authenticate);

router.post('/', snapshotController.createNewSnapshot);
router.get('/', snapshotController.getAllSnapshots);
router.get('/:id', snapshotController.getSnapshotById);
router.delete('/:id', snapshotController.deleteSnapshot);

export default router;
