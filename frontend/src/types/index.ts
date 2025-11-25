// User types
export interface User {
  id: string;
  email: string;
  firstName?: string;
  lastName?: string;
  createdAt: string;
  updatedAt: string;
}

// Auth types
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

// Debt types - will be expanded in Task 2
export interface CreditCard {
  id: string;
  name: string;
  balance: number;
  creditLimit: number;
  interestRate: number;
  minimumPayment: number;
  dueDay: number;
  userId: string;
  createdAt: string;
  updatedAt: string;
}

export interface Loan {
  id: string;
  name: string;
  balance: number;
  originalAmount: number;
  interestRate: number;
  monthlyPayment: number;
  remainingMonths: number;
  userId: string;
  createdAt: string;
  updatedAt: string;
}

// TODO: Add types for Mortgages, Bills, Goals, Snapshots, etc.
