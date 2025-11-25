/**
 * Snapshot Controller
 */

import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../lib/prisma';
import { ApiError } from '../middleware/errorHandler';
import { createSnapshot } from '../utils/calculations';

const createSnapshotSchema = z.object({
  name: z.string().min(1, 'Name is required'),
});

export async function createNewSnapshot(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const { name } = createSnapshotSchema.parse(req.body);

    // Use the calculation function to create snapshot with all metrics
    const snapshot = await createSnapshot(userId, name);

    res.status(201).json({
      success: true,
      data: snapshot,
      message: 'Snapshot created successfully',
    });
  } catch (error) {
    next(error);
  }
}

export async function getAllSnapshots(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const snapshots = await prisma.snapshot.findMany({
      where: { userId },
      orderBy: { snapshotDate: 'desc' },
    });

    res.json({ success: true, data: snapshots });
  } catch (error) {
    next(error);
  }
}

export async function getSnapshotById(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const snapshot = await prisma.snapshot.findFirst({ where: { id, userId } });
    if (!snapshot) throw new ApiError(404, 'Snapshot not found');

    res.json({ success: true, data: snapshot });
  } catch (error) {
    next(error);
  }
}

export async function deleteSnapshot(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const existing = await prisma.snapshot.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Snapshot not found');

    await prisma.snapshot.delete({ where: { id } });
    res.json({ success: true, message: 'Snapshot deleted successfully' });
  } catch (error) {
    next(error);
  }
}
