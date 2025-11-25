/**
 * Mortgage Controller
 */

import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../lib/prisma';
import { ApiError } from '../middleware/errorHandler';

const createMortgageSchema = z.object({
  name: z.string().min(1),
  propertyAddress: z.string().optional(),
  loanAmount: z.number().positive(),
  currentBalance: z.number().nonnegative(),
  interestRate: z.number().nonnegative().max(100),
  termYears: z.number().int().positive(),
  monthlyPayment: z.number().positive(),
  extraPayment: z.number().nonnegative().optional().default(0),
  propertyTax: z.number().nonnegative().optional(),
  homeownersInsurance: z.number().nonnegative().optional(),
  pmi: z.number().nonnegative().optional(),
  startDate: z.string().datetime(),
});

const updateMortgageSchema = createMortgageSchema.partial();

export async function createMortgage(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const data = createMortgageSchema.parse(req.body);
    const mortgage = await prisma.mortgage.create({
      data: { ...data, userId, startDate: new Date(data.startDate) },
    });

    res.status(201).json({ success: true, data: mortgage, message: 'Mortgage created successfully' });
  } catch (error) {
    next(error);
  }
}

export async function getAllMortgages(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const mortgages = await prisma.mortgage.findMany({
      where: { userId },
      orderBy: { createdAt: 'desc' },
    });

    res.json({ success: true, data: mortgages });
  } catch (error) {
    next(error);
  }
}

export async function getMortgageById(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const mortgage = await prisma.mortgage.findFirst({ where: { id, userId } });
    if (!mortgage) throw new ApiError(404, 'Mortgage not found');

    res.json({ success: true, data: mortgage });
  } catch (error) {
    next(error);
  }
}

export async function updateMortgage(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const data = updateMortgageSchema.parse(req.body);
    const existing = await prisma.mortgage.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Mortgage not found');

    const mortgage = await prisma.mortgage.update({
      where: { id },
      data: data.startDate ? { ...data, startDate: new Date(data.startDate) } : data,
    });

    res.json({ success: true, data: mortgage, message: 'Mortgage updated successfully' });
  } catch (error) {
    next(error);
  }
}

export async function deleteMortgage(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const existing = await prisma.mortgage.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Mortgage not found');

    await prisma.mortgage.delete({ where: { id } });
    res.json({ success: true, message: 'Mortgage deleted successfully' });
  } catch (error) {
    next(error);
  }
}
