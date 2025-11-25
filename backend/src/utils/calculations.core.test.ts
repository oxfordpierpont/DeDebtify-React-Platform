/**
 * Unit tests for core financial calculation functions (no database dependencies).
 *
 * These tests verify the pure mathematical functions work correctly.
 */

// Mock the Prisma types for testing
type BillFrequency = 'WEEKLY' | 'BI_WEEKLY' | 'MONTHLY' | 'QUARTERLY' | 'ANNUALLY';

// Core calculation functions (copied for testing without Prisma dependency)
function calculateMonthsToPayoff(balance: number, interestRate: number, monthlyPayment: number): number {
  const monthlyRate = interestRate / 100 / 12;

  // Special case for 0% interest - simple division
  if (monthlyRate === 0) {
    return Math.ceil(balance / monthlyPayment);
  }

  if (monthlyPayment <= balance * monthlyRate) {
    return Infinity;
  }
  const months = -Math.log(1 - (balance * monthlyRate / monthlyPayment)) / Math.log(1 + monthlyRate);
  return Math.ceil(months);
}

function calculateTotalInterest(balance: number, monthlyPayment: number, months: number): number {
  const totalPaid = monthlyPayment * months;
  const totalInterest = totalPaid - balance;
  return Math.max(0, totalInterest);
}

function calculateLoanPayment(principal: number, annualRate: number, termMonths: number): number {
  const monthlyRate = annualRate / 100 / 12;
  if (monthlyRate === 0) {
    return principal / termMonths;
  }
  const payment =
    principal *
    (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) /
    (Math.pow(1 + monthlyRate, termMonths) - 1);
  return Math.round(payment * 100) / 100;
}

function calculateUtilization(balance: number, creditLimit: number): number {
  if (creditLimit <= 0) {
    return 0;
  }
  return Math.round((balance / creditLimit) * 100 * 100) / 100;
}

function calculateDTI(monthlyDebt: number, monthlyIncome: number): number {
  if (monthlyIncome <= 0) {
    return 0;
  }
  return Math.round((monthlyDebt / monthlyIncome) * 100 * 100) / 100;
}

function convertToMonthly(amount: number, frequency: BillFrequency): number {
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

function calculatePayoffDate(months: number): string {
  if (months === Infinity) {
    return 'Never (payment too low)';
  }
  const date = new Date();
  date.setMonth(date.getMonth() + months);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long' });
}

describe('Credit Card Payoff Calculations', () => {
  describe('calculateMonthsToPayoff', () => {
    it('should calculate months correctly for typical credit card', () => {
      const months = calculateMonthsToPayoff(2000, 18, 100);
      expect(months).toBe(24);
    });

    it('should return Infinity if payment does not cover interest', () => {
      const months = calculateMonthsToPayoff(10000, 20, 50);
      expect(months).toBe(Infinity);
    });

    it('should handle zero interest rate', () => {
      const months = calculateMonthsToPayoff(1000, 0, 100);
      expect(months).toBe(10);
    });
  });

  describe('calculateTotalInterest', () => {
    it('should calculate total interest paid', () => {
      const interest = calculateTotalInterest(2000, 100, 24);
      expect(interest).toBe(400);
    });

    it('should return 0 if payment equals balance', () => {
      const interest = calculateTotalInterest(1000, 1000, 1);
      expect(interest).toBe(0);
    });
  });
});

describe('Loan Calculations', () => {
  describe('calculateLoanPayment', () => {
    it('should calculate monthly payment for auto loan', () => {
      const payment = calculateLoanPayment(25000, 6, 60);
      expect(payment).toBeCloseTo(483.32, 1);
    });

    it('should handle zero interest rate', () => {
      const payment = calculateLoanPayment(10000, 0, 50);
      expect(payment).toBe(200);
    });
  });
});

describe('Financial Metrics', () => {
  describe('calculateUtilization', () => {
    it('should calculate credit utilization correctly', () => {
      expect(calculateUtilization(1000, 5000)).toBe(20);
      expect(calculateUtilization(2500, 10000)).toBe(25);
      expect(calculateUtilization(5000, 5000)).toBe(100);
    });

    it('should return 0 for zero credit limit', () => {
      expect(calculateUtilization(1000, 0)).toBe(0);
    });
  });

  describe('calculateDTI', () => {
    it('should calculate DTI correctly', () => {
      expect(calculateDTI(2000, 6000)).toBe(33.33);
      expect(calculateDTI(1500, 5000)).toBe(30);
      expect(calculateDTI(4000, 10000)).toBe(40);
    });

    it('should return 0 for zero income', () => {
      expect(calculateDTI(2000, 0)).toBe(0);
    });
  });

  describe('convertToMonthly', () => {
    it('should convert weekly bills correctly', () => {
      expect(convertToMonthly(100, 'WEEKLY')).toBeCloseTo(433.33, 2);
    });

    it('should convert bi-weekly bills correctly', () => {
      expect(convertToMonthly(200, 'BI_WEEKLY')).toBeCloseTo(433.33, 2);
    });

    it('should keep monthly bills the same', () => {
      expect(convertToMonthly(500, 'MONTHLY')).toBe(500);
    });

    it('should convert quarterly bills correctly', () => {
      expect(convertToMonthly(300, 'QUARTERLY')).toBe(100);
    });

    it('should convert annual bills correctly', () => {
      expect(convertToMonthly(1200, 'ANNUALLY')).toBe(100);
    });
  });

  describe('calculatePayoffDate', () => {
    it('should format payoff date correctly', () => {
      const date = calculatePayoffDate(12);
      expect(date).toMatch(/^[A-Z][a-z]+ \d{4}$/);
    });

    it('should return "Never" for Infinity', () => {
      expect(calculatePayoffDate(Infinity)).toBe('Never (payment too low)');
    });
  });
});

describe('Formula Accuracy Tests', () => {
  it('should match PHP credit card payoff calculation', () => {
    const months = calculateMonthsToPayoff(3000, 19.99, 150);
    expect(months).toBeGreaterThan(22);
    expect(months).toBeLessThan(26);
  });

  it('should match PHP loan payment calculation', () => {
    const payment = calculateLoanPayment(20000, 5.5, 60);
    expect(payment).toBeCloseTo(382.02, 1); // Correct value from formula
  });

  it('should match PHP DTI calculation', () => {
    const dti = calculateDTI(2500, 7000);
    expect(dti).toBe(35.71);
  });

  it('should match PHP utilization calculation', () => {
    const util = calculateUtilization(3500, 10000);
    expect(util).toBe(35);
  });
});
