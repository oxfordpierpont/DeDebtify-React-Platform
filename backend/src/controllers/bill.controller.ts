/**
 * Bill Controller
 */

import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../lib/prisma';
import { ApiError } from '../middleware/errorHandler';

const createBillSchema = z.object({
  name: z.string().min(1),
  category: z.enum(['HOUSING', 'TRANSPORTATION', 'UTILITIES', 'FOOD', 'HEALTHCARE', 'INSURANCE', 'ENTERTAINMENT', 'SUBSCRIPTIONS', 'OTHER']),
  amount: z.number().positive(),
  frequency: z.enum(['WEEKLY', 'BI_WEEKLY', 'MONTHLY', 'QUARTERLY', 'ANNUALLY']),
  dueDay: z.number().int().min(1).max(31).optional(),
  autoPay: z.boolean().optional().default(false),
  isEssential: z.boolean().optional().default(true),
});

const updateBillSchema = createBillSchema.partial();

export async function createBill(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const data = createBillSchema.parse(req.body);
    const bill = await prisma.bill.create({ data: { ...data, userId } });

    res.status(201).json({ success: true, data: bill, message: 'Bill created successfully' });
  } catch (error) {
    next(error);
  }
}

export async function getAllBills(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const bills = await prisma.bill.findMany({
      where: { userId },
      orderBy: { createdAt: 'desc' },
    });

    res.json({ success: true, data: bills });
  } catch (error) {
    next(error);
  }
}

export async function getBillById(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const bill = await prisma.bill.findFirst({ where: { id, userId } });
    if (!bill) throw new ApiError(404, 'Bill not found');

    res.json({ success: true, data: bill });
  } catch (error) {
    next(error);
  }
}

export async function updateBill(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const data = updateBillSchema.parse(req.body);
    const existing = await prisma.bill.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Bill not found');

    const bill = await prisma.bill.update({ where: { id }, data });
    res.json({ success: true, data: bill, message: 'Bill updated successfully' });
  } catch (error) {
    next(error);
  }
}

export async function deleteBill(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const existing = await prisma.bill.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Bill not found');

    await prisma.bill.delete({ where: { id } });
    res.json({ success: true, message: 'Bill deleted successfully' });
  } catch (error) {
    next(error);
  }
}
