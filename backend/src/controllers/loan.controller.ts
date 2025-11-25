/**
 * Loan Controller
 *
 * CRUD operations for loans.
 */

import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../lib/prisma';
import { ApiError } from '../middleware/errorHandler';

const createLoanSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  loanType: z.enum(['PERSONAL', 'AUTO', 'STUDENT', 'OTHER']),
  principal: z.number().positive('Principal must be positive'),
  currentBalance: z.number().nonnegative('Current balance must be non-negative'),
  interestRate: z.number().nonnegative().max(100),
  termMonths: z.number().int().positive('Term must be positive'),
  monthlyPayment: z.number().positive('Monthly payment must be positive'),
  extraPayment: z.number().nonnegative().optional().default(0),
  startDate: z.string().datetime(),
});

const updateLoanSchema = createLoanSchema.partial();

export async function createLoan(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const data = createLoanSchema.parse(req.body);
    const loan = await prisma.loan.create({
      data: { ...data, userId, startDate: new Date(data.startDate) },
    });

    res.status(201).json({ success: true, data: loan, message: 'Loan created successfully' });
  } catch (error) {
    next(error);
  }
}

export async function getAllLoans(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const loans = await prisma.loan.findMany({
      where: { userId },
      orderBy: { createdAt: 'desc' },
    });

    res.json({ success: true, data: loans });
  } catch (error) {
    next(error);
  }
}

export async function getLoanById(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const loan = await prisma.loan.findFirst({ where: { id, userId } });
    if (!loan) throw new ApiError(404, 'Loan not found');

    res.json({ success: true, data: loan });
  } catch (error) {
    next(error);
  }
}

export async function updateLoan(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const data = updateLoanSchema.parse(req.body);
    const existing = await prisma.loan.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Loan not found');

    const loan = await prisma.loan.update({
      where: { id },
      data: data.startDate ? { ...data, startDate: new Date(data.startDate) } : data,
    });

    res.json({ success: true, data: loan, message: 'Loan updated successfully' });
  } catch (error) {
    next(error);
  }
}

export async function deleteLoan(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const existing = await prisma.loan.findFirst({ where: { id, userId } });
    if (!existing) throw new ApiError(404, 'Loan not found');

    await prisma.loan.delete({ where: { id } });
    res.json({ success: true, message: 'Loan deleted successfully' });
  } catch (error) {
    next(error);
  }
}
