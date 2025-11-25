/**
 * Dashboard Controller
 *
 * Provides aggregate financial data for the dashboard view.
 */

import { Request, Response, NextFunction } from 'express';
import prisma from '../lib/prisma';
import { ApiError } from '../middleware/errorHandler';
import {
  getTotalDebt,
  getTotalCreditCardDebt,
  getTotalLoanDebt,
  getTotalMortgageDebt,
  getTotalMonthlyPayments,
  getTotalMonthlyBills,
  getOverallCreditUtilization,
  getUserDTI,
} from '../utils/calculations';

/**
 * Get dashboard data
 *
 * GET /api/dashboard
 *
 * Returns:
 * - User info
 * - Financial summary (totals, ratios)
 * - All debt accounts
 * - Bills
 * - Goals
 * - Recent snapshots
 */
export async function getDashboard(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;

    if (!userId) {
      throw new ApiError(401, 'Not authenticated');
    }

    // Fetch all data in parallel
    const [
      user,
      totalDebt,
      ccDebt,
      loanDebt,
      mortgageDebt,
      monthlyPayments,
      monthlyBills,
      creditUtilization,
      dti,
      creditCards,
      loans,
      mortgages,
      bills,
      goals,
      recentSnapshots,
    ] = await Promise.all([
      prisma.user.findUnique({
        where: { id: userId },
        select: {
          id: true,
          email: true,
          firstName: true,
          lastName: true,
          monthlyIncome: true,
          targetDebtFreeDate: true,
          preferredPayoffMethod: true,
          currency: true,
          timeZone: true,
          totalAssets: true,
          createdAt: true,
          updatedAt: true,
        },
      }),
      getTotalDebt(userId),
      getTotalCreditCardDebt(userId),
      getTotalLoanDebt(userId),
      getTotalMortgageDebt(userId),
      getTotalMonthlyPayments(userId),
      getTotalMonthlyBills(userId),
      getOverallCreditUtilization(userId),
      getUserDTI(userId),
      prisma.creditCard.findMany({
        where: { userId },
        orderBy: { createdAt: 'desc' },
      }),
      prisma.loan.findMany({
        where: { userId },
        orderBy: { createdAt: 'desc' },
      }),
      prisma.mortgage.findMany({
        where: { userId },
        orderBy: { createdAt: 'desc' },
      }),
      prisma.bill.findMany({
        where: { userId },
        orderBy: { createdAt: 'desc' },
      }),
      prisma.goal.findMany({
        where: { userId },
        orderBy: [{ priority: 'desc' }, { createdAt: 'desc' }],
      }),
      prisma.snapshot.findMany({
        where: { userId },
        orderBy: { snapshotDate: 'desc' },
        take: 5,
      }),
    ]);

    if (!user) {
      throw new ApiError(404, 'User not found');
    }

    res.json({
      success: true,
      data: {
        user,
        summary: {
          totalDebt,
          totalCreditCardDebt: ccDebt,
          totalLoanDebt: loanDebt,
          totalMortgageDebt: mortgageDebt,
          totalMonthlyPayments: monthlyPayments,
          totalMonthlyBills: monthlyBills,
          debtToIncomeRatio: dti,
          creditUtilization,
        },
        creditCards,
        loans,
        mortgages,
        bills,
        goals,
        recentSnapshots,
      },
    });
  } catch (error) {
    next(error);
  }
}
