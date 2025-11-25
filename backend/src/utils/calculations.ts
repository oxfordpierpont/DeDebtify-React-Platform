/**
 * Financial Calculations Engine
 *
 * This module handles all financial calculations including debt payoff,
 * interest calculations, and financial metrics.
 *
 * Ported from WordPress PHP plugin to TypeScript.
 *
 * @module calculations
 */

import { PrismaClient, CreditCardStatus, BillFrequency } from '@prisma/client';
import { Decimal } from '@prisma/client/runtime/library';

const prisma = new PrismaClient();

// ============================================
// CORE CALCULATION FUNCTIONS
// Pure math functions with no database access
// ============================================

/**
 * Calculate months to pay off a credit card.
 *
 * Uses logarithmic formula: n = -log(1 - (B * r / P)) / log(1 + r)
 * Where: B = balance, r = monthly rate, P = monthly payment
 *
 * @param balance - Current balance
 * @param interestRate - Annual interest rate (percentage, e.g., 18.5)
 * @param monthlyPayment - Monthly payment amount
 * @returns Months to payoff, or Infinity if payment doesn't cover interest
 */
export function calculateMonthsToPayoff(
  balance: number,
  interestRate: number,
  monthlyPayment: number
): number {
  // Convert annual rate to monthly decimal
  const monthlyRate = interestRate / 100 / 12;

  // Special case for 0% interest - simple division
  if (monthlyRate === 0) {
    return Math.ceil(balance / monthlyPayment);
  }

  // If payment doesn't cover interest, return infinity
  if (monthlyPayment <= balance * monthlyRate) {
    return Infinity;
  }

  // Calculate months using logarithmic formula
  const months = -Math.log(1 - (balance * monthlyRate / monthlyPayment)) / Math.log(1 + monthlyRate);

  return Math.ceil(months);
}

/**
 * Calculate total interest paid over the life of debt.
 *
 * @param balance - Current balance
 * @param monthlyPayment - Monthly payment amount
 * @param months - Number of months to pay off
 * @returns Total interest paid
 */
export function calculateTotalInterest(
  balance: number,
  monthlyPayment: number,
  months: number
): number {
  const totalPaid = monthlyPayment * months;
  const totalInterest = totalPaid - balance;
  return Math.max(0, totalInterest);
}

/**
 * Calculate loan payment using amortization formula.
 *
 * Formula: P = L[c(1 + c)^n]/[(1 + c)^n - 1]
 * Where: L = principal, c = monthly rate, n = term in months
 *
 * @param principal - Loan principal amount
 * @param annualRate - Annual interest rate (percentage)
 * @param termMonths - Term in months
 * @returns Monthly payment amount
 */
export function calculateLoanPayment(
  principal: number,
  annualRate: number,
  termMonths: number
): number {
  const monthlyRate = annualRate / 100 / 12;

  // If no interest, just divide principal by months
  if (monthlyRate === 0) {
    return principal / termMonths;
  }

  const payment =
    principal *
    (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) /
    (Math.pow(1 + monthlyRate, termMonths) - 1);

  return Math.round(payment * 100) / 100;
}

/**
 * Calculate credit utilization percentage.
 *
 * @param balance - Current balance
 * @param creditLimit - Credit limit
 * @returns Utilization percentage
 */
export function calculateUtilization(balance: number, creditLimit: number): number {
  if (creditLimit <= 0) {
    return 0;
  }

  return Math.round((balance / creditLimit) * 100 * 100) / 100;
}

/**
 * Calculate debt-to-income ratio.
 *
 * @param monthlyDebt - Total monthly debt payments
 * @param monthlyIncome - Monthly income
 * @returns DTI percentage
 */
export function calculateDTI(monthlyDebt: number, monthlyIncome: number): number {
  if (monthlyIncome <= 0) {
    return 0;
  }

  return Math.round((monthlyDebt / monthlyIncome) * 100 * 100) / 100;
}

/**
 * Convert bill frequency to monthly equivalent.
 *
 * @param amount - Bill amount
 * @param frequency - Frequency (WEEKLY, BI_WEEKLY, MONTHLY, QUARTERLY, ANNUALLY)
 * @returns Monthly equivalent amount
 */
export function convertToMonthly(amount: number, frequency: BillFrequency): number {
  switch (frequency) {
    case 'WEEKLY':
      return amount * 52 / 12;
    case 'BI_WEEKLY':
      return amount * 26 / 12;
    case 'MONTHLY':
      return amount;
    case 'QUARTERLY':
      return amount / 3;
    case 'ANNUALLY':
      return amount / 12;
    default:
      return amount;
  }
}

/**
 * Calculate payoff date from current date.
 *
 * @param months - Number of months from now
 * @returns Formatted date string (e.g., "December 2025") or "Never (payment too low)"
 */
export function calculatePayoffDate(months: number): string {
  if (months === Infinity) {
    return 'Never (payment too low)';
  }

  const date = new Date();
  date.setMonth(date.getMonth() + months);

  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
}

/**
 * Generate amortization schedule for a loan.
 *
 * @param principal - Loan principal amount
 * @param annualRate - Annual interest rate (percentage)
 * @param termMonths - Term in months
 * @param monthlyPayment - Monthly payment amount
 * @returns Array of monthly payment details
 */
export function generateAmortizationSchedule(
  principal: number,
  annualRate: number,
  termMonths: number,
  monthlyPayment: number
): Array<{
  month: number;
  payment: number;
  principal: number;
  interest: number;
  balance: number;
}> {
  const schedule = [];
  const monthlyRate = annualRate / 100 / 12;
  let balance = principal;

  for (let month = 1; month <= termMonths; month++) {
    const interestPayment = balance * monthlyRate;
    const principalPayment = monthlyPayment - interestPayment;
    balance = Math.max(0, balance - principalPayment);

    schedule.push({
      month,
      payment: Math.round(monthlyPayment * 100) / 100,
      principal: Math.round(principalPayment * 100) / 100,
      interest: Math.round(interestPayment * 100) / 100,
      balance: Math.round(balance * 100) / 100,
    });

    if (balance === 0) break;
  }

  return schedule;
}

// ============================================
// AGGREGATE CALCULATION FUNCTIONS
// Functions that fetch data from database
// ============================================

/**
 * Convert Prisma Decimal to number.
 */
function toNumber(decimal: Decimal | null | undefined): number {
  if (!decimal) return 0;
  return decimal.toNumber();
}

/**
 * Calculate total credit card debt for user.
 * Excludes paid off and closed cards.
 *
 * @param userId - User ID
 * @returns Total credit card debt
 */
export async function getTotalCreditCardDebt(userId: string): Promise<number> {
  const cards = await prisma.creditCard.findMany({
    where: {
      userId,
      status: {
        notIn: [CreditCardStatus.PAID_OFF, CreditCardStatus.CLOSED],
      },
    },
  });

  return cards.reduce((total, card) => total + toNumber(card.balance), 0);
}

/**
 * Calculate total loan debt for user.
 *
 * @param userId - User ID
 * @returns Total loan debt
 */
export async function getTotalLoanDebt(userId: string): Promise<number> {
  const loans = await prisma.loan.findMany({
    where: { userId },
  });

  return loans.reduce((total, loan) => total + toNumber(loan.currentBalance), 0);
}

/**
 * Calculate total mortgage debt for user.
 *
 * @param userId - User ID
 * @returns Total mortgage debt
 */
export async function getTotalMortgageDebt(userId: string): Promise<number> {
  const mortgages = await prisma.mortgage.findMany({
    where: { userId },
  });

  return mortgages.reduce((total, mortgage) => total + toNumber(mortgage.currentBalance), 0);
}

/**
 * Calculate total debt for user (all sources).
 *
 * @param userId - User ID
 * @returns Total debt
 */
export async function getTotalDebt(userId: string): Promise<number> {
  const [ccDebt, loanDebt, mortgageDebt] = await Promise.all([
    getTotalCreditCardDebt(userId),
    getTotalLoanDebt(userId),
    getTotalMortgageDebt(userId),
  ]);

  return ccDebt + loanDebt + mortgageDebt;
}

/**
 * Calculate total monthly debt payments for user.
 * Includes minimum payments, extra payments, and mortgage-related costs.
 *
 * @param userId - User ID
 * @returns Total monthly payments
 */
export async function getTotalMonthlyPayments(userId: string): Promise<number> {
  let total = 0;

  // Credit cards
  const cards = await prisma.creditCard.findMany({
    where: {
      userId,
      status: {
        notIn: [CreditCardStatus.PAID_OFF, CreditCardStatus.CLOSED],
      },
    },
  });

  cards.forEach((card) => {
    total += toNumber(card.minimumPayment);
    total += toNumber(card.extraPayment);
  });

  // Loans
  const loans = await prisma.loan.findMany({
    where: { userId },
  });

  loans.forEach((loan) => {
    total += toNumber(loan.monthlyPayment);
    total += toNumber(loan.extraPayment);
  });

  // Mortgages
  const mortgages = await prisma.mortgage.findMany({
    where: { userId },
  });

  mortgages.forEach((mortgage) => {
    total += toNumber(mortgage.monthlyPayment);
    total += toNumber(mortgage.extraPayment);
    total += toNumber(mortgage.propertyTax) / 12; // Annual to monthly
    total += toNumber(mortgage.homeownersInsurance) / 12; // Annual to monthly
    total += toNumber(mortgage.pmi); // Already monthly
  });

  return Math.round(total * 100) / 100;
}

/**
 * Calculate total monthly bills for user.
 * Converts all bills to monthly equivalent based on frequency.
 *
 * @param userId - User ID
 * @returns Total monthly bills
 */
export async function getTotalMonthlyBills(userId: string): Promise<number> {
  const bills = await prisma.bill.findMany({
    where: { userId },
  });

  const total = bills.reduce((sum, bill) => {
    return sum + convertToMonthly(toNumber(bill.amount), bill.frequency);
  }, 0);

  return Math.round(total * 100) / 100;
}

/**
 * Calculate overall credit utilization for user.
 * Excludes closed cards.
 *
 * @param userId - User ID
 * @returns Credit utilization percentage
 */
export async function getOverallCreditUtilization(userId: string): Promise<number> {
  const cards = await prisma.creditCard.findMany({
    where: {
      userId,
      status: {
        not: CreditCardStatus.CLOSED,
      },
    },
  });

  const totalBalance = cards.reduce((sum, card) => sum + toNumber(card.balance), 0);
  const totalLimit = cards.reduce((sum, card) => sum + toNumber(card.creditLimit), 0);

  return calculateUtilization(totalBalance, totalLimit);
}

/**
 * Calculate user's debt-to-income ratio.
 *
 * @param userId - User ID
 * @returns DTI percentage
 */
export async function getUserDTI(userId: string): Promise<number> {
  const user = await prisma.user.findUnique({
    where: { id: userId },
    select: { monthlyIncome: true },
  });

  const monthlyPayments = await getTotalMonthlyPayments(userId);
  const monthlyIncome = toNumber(user?.monthlyIncome);

  return calculateDTI(monthlyPayments, monthlyIncome);
}

// ============================================
// DEBT PAYOFF STRATEGY FUNCTIONS
// ============================================

/**
 * Debt item for ordering strategies.
 */
export interface DebtItem {
  id: string;
  type: 'credit_card' | 'loan' | 'mortgage';
  name: string;
  balance: number;
  interestRate: number;
  minimumPayment: number;
}

/**
 * Get all active debts for a user.
 *
 * @param userId - User ID
 * @returns Array of debt items
 */
async function getAllDebts(userId: string): Promise<DebtItem[]> {
  const debts: DebtItem[] = [];

  // Get credit cards
  const cards = await prisma.creditCard.findMany({
    where: {
      userId,
      status: {
        notIn: [CreditCardStatus.PAID_OFF, CreditCardStatus.CLOSED],
      },
    },
  });

  cards.forEach((card) => {
    debts.push({
      id: card.id,
      type: 'credit_card',
      name: card.name,
      balance: toNumber(card.balance),
      interestRate: toNumber(card.interestRate),
      minimumPayment: toNumber(card.minimumPayment),
    });
  });

  // Get loans
  const loans = await prisma.loan.findMany({
    where: { userId },
  });

  loans.forEach((loan) => {
    debts.push({
      id: loan.id,
      type: 'loan',
      name: loan.name,
      balance: toNumber(loan.currentBalance),
      interestRate: toNumber(loan.interestRate),
      minimumPayment: toNumber(loan.monthlyPayment),
    });
  });

  // Note: Mortgages are typically excluded from debt payoff strategies
  // as they are long-term and lower priority

  return debts;
}

/**
 * Generate debt avalanche payoff order.
 * Sorts debts by highest interest rate first (saves most money).
 *
 * @param userId - User ID
 * @returns Ordered array of debts (highest interest first)
 */
export async function getAvalancheOrder(userId: string): Promise<DebtItem[]> {
  const debts = await getAllDebts(userId);

  // Sort by interest rate (highest first)
  return debts.sort((a, b) => b.interestRate - a.interestRate);
}

/**
 * Generate debt snowball payoff order.
 * Sorts debts by smallest balance first (psychological wins).
 *
 * @param userId - User ID
 * @returns Ordered array of debts (smallest balance first)
 */
export async function getSnowballOrder(userId: string): Promise<DebtItem[]> {
  const debts = await getAllDebts(userId);

  // Sort by balance (smallest first)
  return debts.sort((a, b) => a.balance - b.balance);
}

/**
 * Get custom debt order defined by user.
 *
 * @param userId - User ID
 * @returns Ordered array of debts based on user's custom priority
 */
export async function getCustomOrder(userId: string): Promise<DebtItem[]> {
  // Get user's custom debt order
  const debtOrders = await prisma.debtOrder.findMany({
    where: { userId },
    orderBy: { priority: 'asc' },
  });

  if (debtOrders.length === 0) {
    // If no custom order set, default to avalanche
    return getAvalancheOrder(userId);
  }

  // Get all debts
  const debts = await getAllDebts(userId);

  // Create a map of debt order priorities
  const priorityMap = new Map<string, number>();
  debtOrders.forEach((order) => {
    priorityMap.set(order.debtId, order.priority);
  });

  // Sort debts by custom priority
  const sortedDebts = debts.sort((a, b) => {
    const priorityA = priorityMap.get(a.id) ?? 999;
    const priorityB = priorityMap.get(b.id) ?? 999;
    return priorityA - priorityB;
  });

  return sortedDebts;
}

/**
 * Calculate projected payoff with extra payment.
 *
 * @param debts - Array of debts in payoff order
 * @param extraPayment - Extra monthly payment to distribute
 * @returns Payoff projection details
 */
export function calculatePayoffProjection(
  debts: DebtItem[],
  extraPayment: number
): {
  totalMonths: number;
  totalInterest: number;
  payoffDate: string;
  debtPayoffSchedule: Array<{
    debtId: string;
    debtName: string;
    monthsPaid: number;
    totalInterest: number;
  }>;
} {
  let remainingExtra = extraPayment;
  let totalMonths = 0;
  let totalInterest = 0;
  const schedule = [];

  for (const debt of debts) {
    const payment = debt.minimumPayment + remainingExtra;
    const months = calculateMonthsToPayoff(debt.balance, debt.interestRate, payment);
    const interest = calculateTotalInterest(debt.balance, payment, months);

    totalMonths = Math.max(totalMonths, months);
    totalInterest += interest;

    schedule.push({
      debtId: debt.id,
      debtName: debt.name,
      monthsPaid: months,
      totalInterest: Math.round(interest * 100) / 100,
    });

    // After this debt is paid off, its payment becomes extra payment for next debt
    remainingExtra += debt.minimumPayment;
  }

  return {
    totalMonths,
    totalInterest: Math.round(totalInterest * 100) / 100,
    payoffDate: calculatePayoffDate(totalMonths),
    debtPayoffSchedule: schedule,
  };
}

// ============================================
// SNAPSHOT CREATION
// ============================================

/**
 * Create a financial snapshot for user.
 * Captures current financial state at a point in time.
 *
 * @param userId - User ID
 * @param name - Snapshot name (e.g., "December 2025")
 * @returns Created snapshot
 */
export async function createSnapshot(userId: string, name: string) {
  // Calculate all values
  const [
    totalDebt,
    ccDebt,
    loanDebt,
    mortgageDebt,
    monthlyPayments,
    monthlyBills,
    creditUtil,
    user,
  ] = await Promise.all([
    getTotalDebt(userId),
    getTotalCreditCardDebt(userId),
    getTotalLoanDebt(userId),
    getTotalMortgageDebt(userId),
    getTotalMonthlyPayments(userId),
    getTotalMonthlyBills(userId),
    getOverallCreditUtilization(userId),
    prisma.user.findUnique({
      where: { id: userId },
      select: { monthlyIncome: true, totalAssets: true },
    }),
  ]);

  const monthlyIncome = toNumber(user?.monthlyIncome);
  const totalAssets = toNumber(user?.totalAssets);
  const dti = calculateDTI(monthlyPayments, monthlyIncome);
  const netWorth = totalAssets - totalDebt;

  // Create snapshot
  const snapshot = await prisma.snapshot.create({
    data: {
      userId,
      name,
      snapshotDate: new Date(),
      totalDebt,
      totalCreditCardDebt: ccDebt,
      totalLoanDebt: loanDebt,
      totalMortgageDebt: mortgageDebt,
      totalMonthlyPayments: monthlyPayments,
      totalMonthlyBills: monthlyBills,
      monthlyIncome,
      debtToIncomeRatio: dti,
      creditUtilization: creditUtil,
      totalAssets,
      netWorth,
    },
  });

  return snapshot;
}

/**
 * Export all calculation functions.
 */
export default {
  // Core calculations
  calculateMonthsToPayoff,
  calculateTotalInterest,
  calculateLoanPayment,
  calculateUtilization,
  calculateDTI,
  convertToMonthly,
  calculatePayoffDate,
  generateAmortizationSchedule,

  // Aggregate calculations
  getTotalCreditCardDebt,
  getTotalLoanDebt,
  getTotalMortgageDebt,
  getTotalDebt,
  getTotalMonthlyPayments,
  getTotalMonthlyBills,
  getOverallCreditUtilization,
  getUserDTI,

  // Strategy functions
  getAvalancheOrder,
  getSnowballOrder,
  getCustomOrder,
  calculatePayoffProjection,

  // Snapshot
  createSnapshot,
};
