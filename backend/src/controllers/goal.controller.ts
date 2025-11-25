/**
 * Goal Controller
 */

import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../lib/prisma';
import { ApiError } from '../middleware/errorHandler';

const createGoalSchema = z.object({
  name: z.string().min(1),
  goalType: z.enum(['SAVINGS', 'EMERGENCY_FUND', 'DEBT_PAYOFF', 'INVESTMENT', 'PURCHASE', 'OTHER']),
  targetAmount: z.number().positive(),
  currentAmount: z.number().nonnegative().optional().default(0),
  monthlyContribution: z.number().nonnegative().optional(),
  targetDate: z.string().datetime().optional(),
  priority: z.enum(['LOW', 'MEDIUM', 'HIGH']).optional().default('MEDIUM'),
});

const updateGoalSchema = createGoalSchema.partial();

export async function createGoal(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const data = createGoalSchema.parse(req.body);
    const goal = await prisma.goal.create({
      data: { ...data, userId, targetDate: data.targetDate ? new Date(data.targetDate) : undefined },
    });

    res.status(201).json({ success: true, data: goal, message: 'Goal created successfully' });
  } catch (error) {
    next(error);
  }
}

export async function getAllGoals(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const goals = await prisma.goal.findMany({
      where: { userId },
      orderBy: [{ priority: 'desc' }, { createdAt: 'desc' }],
    });

    res.json({ success: true, data: goals });
  } catch (error) {
    next(error);
  }
}

export async function getGoalById(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const goal = await prisma.goal.findFirst({ where: { id, userId } });
    if (!goal) throw new ApiError(404, 'Goal not found');

    res.json({ success: true, data: goal });
  } catch (error) {
    next(error);
  }
}

export async function updateGoal(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const data = updateGoalSchema.parse(req.body);
    const existing = await prisma.goal.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Goal not found');

    const goal = await prisma.goal.update({
      where: { id },
      data: data.targetDate ? { ...data, targetDate: new Date(data.targetDate) } : data,
    });

    res.json({ success: true, data: goal, message: 'Goal updated successfully' });
  } catch (error) {
    next(error);
  }
}

export async function deleteGoal(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const existing = await prisma.goal.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Goal not found');

    await prisma.goal.delete({ where: { id } });
    res.json({ success: true, message: 'Goal deleted successfully' });
  } catch (error) {
    next(error);
  }
}
