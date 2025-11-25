/**
 * Debt Strategy Controller
 *
 * Provides debt payoff strategies and calculations.
 */

import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import { ApiError } from '../middleware/errorHandler';
import {
  getAvalancheOrder,
  getSnowballOrder,
  getCustomOrder,
  calculatePayoffProjection,
  calculateMonthsToPayoff,
  calculateTotalInterest,
  calculateLoanPayment,
  generateAmortizationSchedule,
} from '../utils/calculations';

/**
 * Get avalanche payoff order
 *
 * GET /api/strategy/avalanche
 */
export async function getAvalanche(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const order = await getAvalancheOrder(userId);

    res.json({
      success: true,
      data: {
        method: 'avalanche',
        description: 'Pay off debts with highest interest rates first (saves most money)',
        debts: order,
      },
    });
  } catch (error) {
    next(error);
  }
}

/**
 * Get snowball payoff order
 *
 * GET /api/strategy/snowball
 */
export async function getSnowball(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const order = await getSnowballOrder(userId);

    res.json({
      success: true,
      data: {
        method: 'snowball',
        description: 'Pay off debts with smallest balances first (quick wins)',
        debts: order,
      },
    });
  } catch (error) {
    next(error);
  }
}

/**
 * Get custom payoff order
 *
 * GET /api/strategy/custom
 */
export async function getCustom(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const order = await getCustomOrder(userId);

    res.json({
      success: true,
      data: {
        method: 'custom',
        description: 'Pay off debts in your custom order',
        debts: order,
      },
    });
  } catch (error) {
    next(error);
  }
}

/**
 * Calculate payoff projection
 *
 * POST /api/strategy/projection
 *
 * Body:
 * - method: 'avalanche' | 'snowball' | 'custom'
 * - extraPayment: number
 */
const projectionSchema = z.object({
  method: z.enum(['avalanche', 'snowball', 'custom']),
  extraPayment: z.number().nonnegative(),
});

export async function getProjection(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    if (!userId) throw new ApiError(401, 'Not authenticated');

    const { method, extraPayment } = projectionSchema.parse(req.body);

    // Get debts in specified order
    let debts;
    if (method === 'avalanche') {
      debts = await getAvalancheOrder(userId);
    } else if (method === 'snowball') {
      debts = await getSnowballOrder(userId);
    } else {
      debts = await getCustomOrder(userId);
    }

    // Calculate projection
    const projection = calculatePayoffProjection(debts, extraPayment);

    res.json({
      success: true,
      data: {
        method,
        extraPayment,
        ...projection,
      },
    });
  } catch (error) {
    next(error);
  }
}

/**
 * Calculate credit card payoff
 *
 * POST /api/strategy/calculate/credit-card
 *
 * Body:
 * - balance: number
 * - interestRate: number
 * - monthlyPayment: number
 */
const creditCardCalcSchema = z.object({
  balance: z.number().nonnegative(),
  interestRate: z.number().nonnegative().max(100),
  monthlyPayment: z.number().positive(),
});

export async function calculateCreditCardPayoff(req: Request, res: Response, next: NextFunction) {
  try {
    const { balance, interestRate, monthlyPayment } = creditCardCalcSchema.parse(req.body);

    const months = calculateMonthsToPayoff(balance, interestRate, monthlyPayment);
    const totalInterest = calculateTotalInterest(balance, monthlyPayment, months);

    res.json({
      success: true,
      data: {
        months,
        totalInterest,
        totalPaid: monthlyPayment * months,
      },
    });
  } catch (error) {
    next(error);
  }
}

/**
 * Calculate loan payment
 *
 * POST /api/strategy/calculate/loan
 *
 * Body:
 * - principal: number
 * - annualRate: number
 * - termMonths: number
 * - includeSchedule: boolean (optional)
 */
const loanCalcSchema = z.object({
  principal: z.number().positive(),
  annualRate: z.number().nonnegative().max(100),
  termMonths: z.number().int().positive(),
  includeSchedule: z.boolean().optional().default(false),
});

export async function calculateLoan(req: Request, res: Response, next: NextFunction) {
  try {
    const { principal, annualRate, termMonths, includeSchedule } = loanCalcSchema.parse(req.body);

    const monthlyPayment = calculateLoanPayment(principal, annualRate, termMonths);
    const totalPaid = monthlyPayment * termMonths;
    const totalInterest = totalPaid - principal;

    const result: any = {
      monthlyPayment,
      totalPaid,
      totalInterest,
    };

    if (includeSchedule) {
      result.amortizationSchedule = generateAmortizationSchedule(principal, annualRate, termMonths, monthlyPayment);
    }

    res.json({
      success: true,
      data: result,
    });
  } catch (error) {
    next(error);
  }
}
