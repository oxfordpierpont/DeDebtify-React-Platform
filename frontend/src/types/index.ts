// ============================================
// USER TYPES
// ============================================

export type PayoffMethod = 'AVALANCHE' | 'SNOWBALL' | 'CUSTOM';

export interface User {
  id: string;
  email: string;
  firstName?: string;
  lastName?: string;

  // Financial preferences
  monthlyIncome?: number;
  targetDebtFreeDate?: string;
  preferredPayoffMethod: PayoffMethod;
  currency: string;
  timeZone: string;

  // Notification preferences (JSON)
  notificationPreferences?: any;

  // Asset tracking
  totalAssets?: number;

  createdAt: string;
  updatedAt: string;
}

// ============================================
// AUTH TYPES
// ============================================

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  email: string;
  password: string;
  firstName?: string;
  lastName?: string;
}

export interface AuthResponse {
  token: string;
  user: User;
}

// ============================================
// CREDIT CARD TYPES
// ============================================

export type CreditCardStatus = 'ACTIVE' | 'PAID_OFF' | 'CLOSED';

export interface CreditCard {
  id: string;
  userId: string;
  name: string;
  balance: number;
  creditLimit: number;
  interestRate: number;
  minimumPayment: number;
  extraPayment: number;
  dueDay?: number;
  autoPay: boolean;
  status: CreditCardStatus;
  plaidAccountId?: string;
  createdAt: string;
  updatedAt: string;
}

export interface CreateCreditCardData {
  name: string;
  balance: number;
  creditLimit: number;
  interestRate: number;
  minimumPayment: number;
  extraPayment?: number;
  dueDay?: number;
  autoPay?: boolean;
}

// ============================================
// LOAN TYPES
// ============================================

export type LoanType = 'PERSONAL' | 'AUTO' | 'STUDENT' | 'OTHER';

export interface Loan {
  id: string;
  userId: string;
  name: string;
  loanType: LoanType;
  principal: number;
  currentBalance: number;
  interestRate: number;
  termMonths: number;
  monthlyPayment: number;
  extraPayment: number;
  startDate: string;
  plaidAccountId?: string;
  createdAt: string;
  updatedAt: string;
}

export interface CreateLoanData {
  name: string;
  loanType: LoanType;
  principal: number;
  currentBalance: number;
  interestRate: number;
  termMonths: number;
  monthlyPayment: number;
  extraPayment?: number;
  startDate: string;
}

// ============================================
// MORTGAGE TYPES
// ============================================

export interface Mortgage {
  id: string;
  userId: string;
  name: string;
  propertyAddress?: string;
  loanAmount: number;
  currentBalance: number;
  interestRate: number;
  termYears: number;
  monthlyPayment: number;
  extraPayment: number;
  propertyTax?: number;
  homeownersInsurance?: number;
  pmi?: number;
  startDate: string;
  plaidAccountId?: string;
  createdAt: string;
  updatedAt: string;
}

export interface CreateMortgageData {
  name: string;
  propertyAddress?: string;
  loanAmount: number;
  currentBalance: number;
  interestRate: number;
  termYears: number;
  monthlyPayment: number;
  extraPayment?: number;
  propertyTax?: number;
  homeownersInsurance?: number;
  pmi?: number;
  startDate: string;
}

// ============================================
// BILL TYPES
// ============================================

export type BillCategory =
  | 'HOUSING'
  | 'TRANSPORTATION'
  | 'UTILITIES'
  | 'FOOD'
  | 'HEALTHCARE'
  | 'INSURANCE'
  | 'ENTERTAINMENT'
  | 'SUBSCRIPTIONS'
  | 'OTHER';

export type BillFrequency = 'WEEKLY' | 'BI_WEEKLY' | 'MONTHLY' | 'QUARTERLY' | 'ANNUALLY';

export interface Bill {
  id: string;
  userId: string;
  name: string;
  category: BillCategory;
  amount: number;
  frequency: BillFrequency;
  dueDay?: number;
  autoPay: boolean;
  isEssential: boolean;
  createdAt: string;
  updatedAt: string;
}

export interface CreateBillData {
  name: string;
  category: BillCategory;
  amount: number;
  frequency: BillFrequency;
  dueDay?: number;
  autoPay?: boolean;
  isEssential?: boolean;
}

// ============================================
// GOAL TYPES
// ============================================

export type GoalType =
  | 'SAVINGS'
  | 'EMERGENCY_FUND'
  | 'DEBT_PAYOFF'
  | 'INVESTMENT'
  | 'PURCHASE'
  | 'OTHER';

export type GoalPriority = 'LOW' | 'MEDIUM' | 'HIGH';

export interface Goal {
  id: string;
  userId: string;
  name: string;
  goalType: GoalType;
  targetAmount: number;
  currentAmount: number;
  monthlyContribution?: number;
  targetDate?: string;
  priority: GoalPriority;
  createdAt: string;
  updatedAt: string;
}

export interface CreateGoalData {
  name: string;
  goalType: GoalType;
  targetAmount: number;
  currentAmount?: number;
  monthlyContribution?: number;
  targetDate?: string;
  priority?: GoalPriority;
}

// ============================================
// SNAPSHOT TYPES
// ============================================

export interface Snapshot {
  id: string;
  userId: string;
  name: string;
  snapshotDate: string;
  totalDebt: number;
  totalCreditCardDebt: number;
  totalLoanDebt: number;
  totalMortgageDebt: number;
  totalMonthlyPayments: number;
  totalMonthlyBills: number;
  monthlyIncome?: number;
  debtToIncomeRatio?: number;
  creditUtilization?: number;
  totalAssets?: number;
  netWorth?: number;
  createdAt: string;
  updatedAt: string;
}

export interface CreateSnapshotData {
  name: string;
  snapshotDate: string;
}

// ============================================
// PLAID TYPES
// ============================================

export type PlaidItemStatus = 'ACTIVE' | 'DISCONNECTED' | 'ERROR';

export interface PlaidItem {
  id: string;
  userId: string;
  itemId: string;
  institutionId?: string;
  institutionName?: string;
  status: PlaidItemStatus;
  connectedAt: string;
  lastSyncAt?: string;
  createdAt: string;
  updatedAt: string;
}

export interface PlaidAccount {
  id: string;
  plaidItemId: string;
  accountId: string;
  accountName: string;
  officialName?: string;
  accountType: string;
  accountSubtype?: string;
  currentBalance?: number;
  availableBalance?: number;
  limit?: number;
  mask?: string;
  currency: string;
  lastSyncAt?: string;
  createdAt: string;
  updatedAt: string;
}

export interface PlaidLinkTokenResponse {
  linkToken: string;
  expiration: string;
}

export interface PlaidExchangeTokenData {
  publicToken: string;
}

// ============================================
// DEBT ORDER TYPES (for custom payoff order)
// ============================================

export interface DebtOrder {
  id: string;
  userId: string;
  debtId: string;
  debtType: 'credit_card' | 'loan' | 'mortgage';
  priority: number;
  createdAt: string;
  updatedAt: string;
}

export interface CreateDebtOrderData {
  debtId: string;
  debtType: 'credit_card' | 'loan' | 'mortgage';
  priority: number;
}

// ============================================
// DASHBOARD TYPES
// ============================================

export interface DashboardData {
  user: User;
  summary: {
    totalDebt: number;
    totalCreditCardDebt: number;
    totalLoanDebt: number;
    totalMortgageDebt: number;
    totalMonthlyPayments: number;
    totalMonthlyBills: number;
    debtToIncomeRatio?: number;
    creditUtilization?: number;
  };
  creditCards: CreditCard[];
  loans: Loan[];
  mortgages: Mortgage[];
  bills: Bill[];
  goals: Goal[];
  recentSnapshots: Snapshot[];
}

// ============================================
// CALCULATION TYPES
// ============================================

export interface PayoffCalculation {
  months: number;
  totalInterest: number;
  totalPaid: number;
  payoffDate: string;
}

export interface DebtPayoffStrategy {
  method: 'avalanche' | 'snowball';
  order: Array<{
    id: string;
    type: 'credit_card' | 'loan' | 'mortgage';
    name: string;
    balance: number;
    interestRate: number;
    priority: number;
  }>;
  projectedPayoffMonths: number;
  totalInterestSaved: number;
}

export interface LoanPaymentCalculation {
  monthlyPayment: number;
  totalPayment: number;
  totalInterest: number;
  amortizationSchedule?: Array<{
    month: number;
    payment: number;
    principal: number;
    interest: number;
    balance: number;
  }>;
}

// ============================================
// API RESPONSE TYPES
// ============================================

export interface ApiResponse<T> {
  success: boolean;
  data?: T;
  error?: string;
  message?: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  pagination: {
    page: number;
    pageSize: number;
    total: number;
    totalPages: number;
  };
}

// ============================================
// FORM TYPES
// ============================================

export type UpdateCreditCardData = Partial<CreateCreditCardData>;
export type UpdateLoanData = Partial<CreateLoanData>;
export type UpdateMortgageData = Partial<CreateMortgageData>;
export type UpdateBillData = Partial<CreateBillData>;
export type UpdateGoalData = Partial<CreateGoalData>;

// ============================================
// UTILITY TYPES
// ============================================

export interface SelectOption {
  value: string;
  label: string;
}

export interface DateRange {
  startDate: string;
  endDate: string;
}

export interface ChartDataPoint {
  date: string;
  value: number;
  label?: string;
}
