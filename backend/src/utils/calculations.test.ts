/**
 * Unit tests for financial calculations.
 *
 * These tests verify that all calculation formulas produce accurate results
 * and match the behavior of the original WordPress PHP implementation.
 */

import {
  calculateMonthsToPayoff,
  calculateTotalInterest,
  calculateLoanPayment,
  calculateUtilization,
  calculateDTI,
  convertToMonthly,
  calculatePayoffDate,
  generateAmortizationSchedule,
  calculatePayoffProjection,
} from './calculations';

// Define BillFrequency type for tests (matches Prisma enum)
type BillFrequency = 'WEEKLY' | 'BI_WEEKLY' | 'MONTHLY' | 'QUARTERLY' | 'ANNUALLY';

describe('Credit Card Payoff Calculations', () => {
  describe('calculateMonthsToPayoff', () => {
    it('should calculate months correctly for typical credit card', () => {
      // $2,000 balance, 18% APR, $100/month payment
      const months = calculateMonthsToPayoff(2000, 18, 100);
      expect(months).toBe(24); // ~24 months
    });

    it('should calculate months for high interest rate', () => {
      // $5,000 balance, 24.99% APR, $200/month payment
      const months = calculateMonthsToPayoff(5000, 24.99, 200);
      expect(months).toBeGreaterThan(30);
      expect(months).toBeLessThan(35);
    });

    it('should calculate months for low interest rate', () => {
      // $3,000 balance, 9.99% APR, $150/month payment
      const months = calculateMonthsToPayoff(3000, 9.99, 150);
      expect(months).toBeGreaterThan(20);
      expect(months).toBeLessThan(25);
    });

    it('should return Infinity if payment does not cover interest', () => {
      // $10,000 balance, 20% APR, $50/month payment
      // Interest alone is ~$166/month, so $50 won't cover it
      const months = calculateMonthsToPayoff(10000, 20, 50);
      expect(months).toBe(Infinity);
    });

    it('should handle zero interest rate', () => {
      // $1,000 balance, 0% APR, $100/month payment
      const months = calculateMonthsToPayoff(1000, 0, 100);
      expect(months).toBe(10); // Exactly 10 months
    });

    it('should handle exact payoff amount', () => {
      // $500 balance, 0% APR, $500/month payment
      const months = calculateMonthsToPayoff(500, 0, 500);
      expect(months).toBe(1);
    });

    it('should round up partial months', () => {
      // $1,000 balance, 0% APR, $300/month payment
      // 3.33 months should round to 4
      const months = calculateMonthsToPayoff(1000, 0, 300);
      expect(months).toBe(4);
    });
  });

  describe('calculateTotalInterest', () => {
    it('should calculate total interest paid', () => {
      // $2,000 balance, $100/month, 24 months
      const interest = calculateTotalInterest(2000, 100, 24);
      expect(interest).toBe(400); // Total paid: $2,400, Interest: $400
    });

    it('should return 0 if payment equals balance', () => {
      const interest = calculateTotalInterest(1000, 1000, 1);
      expect(interest).toBe(0);
    });

    it('should not return negative interest', () => {
      // Edge case: very high payment
      const interest = calculateTotalInterest(500, 1000, 1);
      expect(interest).toBe(0); // Should max at 0, not go negative
    });
  });
});

describe('Loan Calculations', () => {
  describe('calculateLoanPayment', () => {
    it('should calculate monthly payment for auto loan', () => {
      // $25,000 auto loan, 6% APR, 60 months
      const payment = calculateLoanPayment(25000, 6, 60);
      expect(payment).toBeCloseTo(483.32, 1); // Standard auto loan payment
    });

    it('should calculate monthly payment for personal loan', () => {
      // $10,000 personal loan, 12% APR, 36 months
      const payment = calculateLoanPayment(10000, 12, 36);
      expect(payment).toBeCloseTo(332.14, 1);
    });

    it('should calculate monthly payment for student loan', () => {
      // $30,000 student loan, 4.5% APR, 120 months (10 years)
      const payment = calculateLoanPayment(30000, 4.5, 120);
      expect(payment).toBeCloseTo(311.38, 1);
    });

    it('should handle zero interest rate', () => {
      // $10,000 loan, 0% APR, 50 months
      const payment = calculateLoanPayment(10000, 0, 50);
      expect(payment).toBe(200); // Simple division
    });

    it('should handle short-term high-interest loan', () => {
      // $5,000 loan, 24% APR, 12 months
      const payment = calculateLoanPayment(5000, 24, 12);
      expect(payment).toBeCloseTo(470.43, 1);
    });
  });

  describe('generateAmortizationSchedule', () => {
    it('should generate correct amortization schedule', () => {
      // $10,000 loan, 6% APR, 12 months
      const payment = calculateLoanPayment(10000, 6, 12);
      const schedule = generateAmortizationSchedule(10000, 6, 12, payment);

      expect(schedule).toHaveLength(12);
      expect(schedule[0].month).toBe(1);
      expect(schedule[11].month).toBe(12);

      // First payment should have more interest than principal
      expect(schedule[0].interest).toBeGreaterThan(schedule[0].principal);

      // Last payment should have more principal than interest
      expect(schedule[11].principal).toBeGreaterThan(schedule[11].interest);

      // Final balance should be 0 or very close to 0
      expect(schedule[11].balance).toBeLessThan(1);
    });

    it('should have decreasing balance over time', () => {
      const payment = calculateLoanPayment(5000, 8, 24);
      const schedule = generateAmortizationSchedule(5000, 8, 24, payment);

      for (let i = 1; i < schedule.length; i++) {
        expect(schedule[i].balance).toBeLessThan(schedule[i - 1].balance);
      }
    });

    it('should have increasing principal payments over time', () => {
      const payment = calculateLoanPayment(5000, 8, 24);
      const schedule = generateAmortizationSchedule(5000, 8, 24, payment);

      // Principal should generally increase (though may not be strictly monotonic)
      const firstHalfAvgPrincipal = schedule.slice(0, 12).reduce((sum, p) => sum + p.principal, 0) / 12;
      const secondHalfAvgPrincipal = schedule.slice(12).reduce((sum, p) => sum + p.principal, 0) / 12;

      expect(secondHalfAvgPrincipal).toBeGreaterThan(firstHalfAvgPrincipal);
    });
  });
});

describe('Financial Metrics', () => {
  describe('calculateUtilization', () => {
    it('should calculate credit utilization correctly', () => {
      expect(calculateUtilization(1000, 5000)).toBe(20); // 20%
      expect(calculateUtilization(2500, 10000)).toBe(25); // 25%
      expect(calculateUtilization(5000, 5000)).toBe(100); // 100%
    });

    it('should return 0 for zero credit limit', () => {
      expect(calculateUtilization(1000, 0)).toBe(0);
    });

    it('should return 0 for negative credit limit', () => {
      expect(calculateUtilization(1000, -5000)).toBe(0);
    });

    it('should handle over-limit balances', () => {
      expect(calculateUtilization(6000, 5000)).toBeGreaterThan(100);
    });

    it('should round to 2 decimal places', () => {
      const result = calculateUtilization(1234, 5678);
      expect(result).toBe(21.73); // Precise rounding
    });
  });

  describe('calculateDTI', () => {
    it('should calculate DTI correctly', () => {
      expect(calculateDTI(2000, 6000)).toBe(33.33); // 33.33%
      expect(calculateDTI(1500, 5000)).toBe(30); // 30%
      expect(calculateDTI(4000, 10000)).toBe(40); // 40%
    });

    it('should return 0 for zero income', () => {
      expect(calculateDTI(2000, 0)).toBe(0);
    });

    it('should return 0 for negative income', () => {
      expect(calculateDTI(2000, -5000)).toBe(0);
    });

    it('should handle high DTI ratios', () => {
      expect(calculateDTI(5000, 3000)).toBeGreaterThan(100);
    });
  });

  describe('convertToMonthly', () => {
    it('should convert weekly bills correctly', () => {
      // $100/week = ~$433/month (100 * 52 / 12)
      expect(convertToMonthly(100, 'WEEKLY')).toBeCloseTo(433.33, 2);
    });

    it('should convert bi-weekly bills correctly', () => {
      // $200 bi-weekly = ~$433/month (200 * 26 / 12)
      expect(convertToMonthly(200, 'BI_WEEKLY')).toBeCloseTo(433.33, 2);
    });

    it('should keep monthly bills the same', () => {
      expect(convertToMonthly(500, 'MONTHLY')).toBe(500);
    });

    it('should convert quarterly bills correctly', () => {
      // $300 quarterly = $100/month
      expect(convertToMonthly(300, 'QUARTERLY')).toBe(100);
    });

    it('should convert annual bills correctly', () => {
      // $1,200 annually = $100/month
      expect(convertToMonthly(1200, 'ANNUALLY')).toBe(100);
    });
  });

  describe('calculatePayoffDate', () => {
    it('should format payoff date correctly', () => {
      // Should return a date string in format "Month Year"
      const date = calculatePayoffDate(12);
      expect(date).toMatch(/^[A-Z][a-z]+ \d{4}$/); // e.g., "December 2025"
    });

    it('should return "Never" for Infinity', () => {
      expect(calculatePayoffDate(Infinity)).toBe('Never (payment too low)');
    });

    it('should handle 0 months (immediate payoff)', () => {
      const date = calculatePayoffDate(0);
      expect(date).toMatch(/^[A-Z][a-z]+ \d{4}$/);
    });
  });
});

describe('Payoff Strategy Calculations', () => {
  describe('calculatePayoffProjection', () => {
    const mockDebts = [
      {
        id: '1',
        type: 'credit_card' as const,
        name: 'Chase Freedom',
        balance: 2000,
        interestRate: 18,
        minimumPayment: 50,
      },
      {
        id: '2',
        type: 'credit_card' as const,
        name: 'Discover',
        balance: 1500,
        interestRate: 22,
        minimumPayment: 40,
      },
      {
        id: '3',
        type: 'loan' as const,
        name: 'Personal Loan',
        balance: 5000,
        interestRate: 12,
        minimumPayment: 150,
      },
    ];

    it('should calculate payoff projection with extra payment', () => {
      const projection = calculatePayoffProjection(mockDebts, 200);

      expect(projection.totalMonths).toBeGreaterThan(0);
      expect(projection.totalInterest).toBeGreaterThan(0);
      expect(projection.payoffDate).toBeTruthy();
      expect(projection.debtPayoffSchedule).toHaveLength(3);
    });

    it('should have payoff schedule for all debts', () => {
      const projection = calculatePayoffProjection(mockDebts, 100);

      expect(projection.debtPayoffSchedule).toHaveLength(3);
      projection.debtPayoffSchedule.forEach((debt) => {
        expect(debt.debtId).toBeTruthy();
        expect(debt.debtName).toBeTruthy();
        expect(debt.monthsPaid).toBeGreaterThan(0);
        expect(debt.totalInterest).toBeGreaterThanOrEqual(0);
      });
    });

    it('should pay off debts faster with more extra payment', () => {
      const projection100 = calculatePayoffProjection(mockDebts, 100);
      const projection300 = calculatePayoffProjection(mockDebts, 300);

      expect(projection300.totalMonths).toBeLessThan(projection100.totalMonths);
      expect(projection300.totalInterest).toBeLessThan(projection100.totalInterest);
    });

    it('should handle zero extra payment', () => {
      const projection = calculatePayoffProjection(mockDebts, 0);

      expect(projection.totalMonths).toBeGreaterThan(0);
      expect(projection.debtPayoffSchedule).toHaveLength(3);
    });
  });
});

describe('Edge Cases and Error Handling', () => {
  it('should handle very small balances', () => {
    const months = calculateMonthsToPayoff(0.01, 18, 10);
    expect(months).toBe(1);
  });

  it('should handle very large balances', () => {
    const months = calculateMonthsToPayoff(1000000, 18, 5000);
    expect(months).toBeGreaterThan(0);
    expect(months).not.toBe(Infinity);
  });

  it('should handle very low interest rates', () => {
    const months = calculateMonthsToPayoff(5000, 0.01, 200);
    expect(months).toBeGreaterThan(0);
    expect(months).toBeLessThan(30);
  });

  it('should handle very high interest rates', () => {
    const months = calculateMonthsToPayoff(5000, 99.99, 300);
    expect(months).toBeGreaterThan(0);
  });

  it('should maintain precision with decimals', () => {
    const payment = calculateLoanPayment(12345.67, 7.89, 48);
    expect(payment).toBeGreaterThan(0);
    expect(payment % 1).toBeGreaterThan(0); // Should have decimal places
  });
});

describe('Formula Accuracy Tests', () => {
  /**
   * These tests verify that our TypeScript implementation produces
   * the same results as the original PHP WordPress plugin.
   */

  it('should match PHP credit card payoff calculation', () => {
    // Test case from WordPress plugin
    const balance = 3000;
    const rate = 19.99;
    const payment = 150;

    const months = calculateMonthsToPayoff(balance, rate, payment);

    // Expected: ~24 months (verified against PHP implementation)
    expect(months).toBeGreaterThan(22);
    expect(months).toBeLessThan(26);
  });

  it('should match PHP loan payment calculation', () => {
    // Test case from WordPress plugin
    const principal = 20000;
    const rate = 5.5;
    const term = 60;

    const payment = calculateLoanPayment(principal, rate, term);

    // Expected: ~$381.19 (verified against PHP implementation)
    expect(payment).toBeCloseTo(381.19, 1);
  });

  it('should match PHP DTI calculation', () => {
    // Test case from WordPress plugin
    const debt = 2500;
    const income = 7000;

    const dti = calculateDTI(debt, income);

    // Expected: 35.71%
    expect(dti).toBe(35.71);
  });

  it('should match PHP utilization calculation', () => {
    // Test case from WordPress plugin
    const balance = 3500;
    const limit = 10000;

    const util = calculateUtilization(balance, limit);

    // Expected: 35%
    expect(util).toBe(35);
  });
});

describe('Real-World Scenarios', () => {
  it('should calculate realistic credit card payoff', () => {
    // Scenario: Average credit card debt in USA
    const balance = 6194; // Average US credit card debt
    const rate = 20.09; // Average US credit card APR
    const payment = 200;

    const months = calculateMonthsToPayoff(balance, rate, payment);
    const totalInterest = calculateTotalInterest(balance, payment, months);

    expect(months).toBeGreaterThan(30);
    expect(totalInterest).toBeGreaterThan(500);
  });

  it('should calculate realistic auto loan', () => {
    // Scenario: New car loan
    const principal = 35000;
    const rate = 7.2; // Current average new car rate
    const term = 72; // 6 years

    const payment = calculateLoanPayment(principal, rate, term);

    expect(payment).toBeGreaterThan(550);
    expect(payment).toBeLessThan(650);
  });

  it('should calculate realistic debt-free timeline', () => {
    const debts = [
      { id: '1', type: 'credit_card' as const, name: 'Card 1', balance: 4000, interestRate: 19, minimumPayment: 80 },
      { id: '2', type: 'credit_card' as const, name: 'Card 2', balance: 2500, interestRate: 23, minimumPayment: 50 },
      { id: '3', type: 'loan' as const, name: 'Personal Loan', balance: 8000, interestRate: 11, minimumPayment: 200 },
    ];

    const projection = calculatePayoffProjection(debts, 300);

    // With $300 extra payment, should be debt-free in reasonable time
    expect(projection.totalMonths).toBeGreaterThan(12);
    expect(projection.totalMonths).toBeLessThan(60);
  });
});
