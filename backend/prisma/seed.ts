import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Seeding database...');

  // Create a test user
  const hashedPassword = await bcrypt.hash('password123', 10);

  const user = await prisma.user.create({
    data: {
      email: 'test@dedebtify.com',
      password: hashedPassword,
      firstName: 'Test',
      lastName: 'User',
      monthlyIncome: 5000,
    },
  });

  console.log('✅ Created test user:', user.email);

  // Create test credit cards
  const creditCard1 = await prisma.creditCard.create({
    data: {
      userId: user.id,
      name: 'Chase Freedom',
      balance: 2500,
      creditLimit: 5000,
      interestRate: 18.99,
      minimumPayment: 50,
      extraPayment: 100,
      dueDay: 15,
      autoPay: true,
      status: 'ACTIVE',
    },
  });

  const creditCard2 = await prisma.creditCard.create({
    data: {
      userId: user.id,
      name: 'Capital One Venture',
      balance: 1200,
      creditLimit: 10000,
      interestRate: 15.49,
      minimumPayment: 35,
      dueDay: 5,
      status: 'ACTIVE',
    },
  });

  console.log('✅ Created credit cards:', creditCard1.name, creditCard2.name);

  // Create test loans
  const loan1 = await prisma.loan.create({
    data: {
      userId: user.id,
      name: 'Honda Civic',
      loanType: 'AUTO',
      principal: 25000,
      currentBalance: 18500,
      interestRate: 4.5,
      termMonths: 60,
      monthlyPayment: 466,
      startDate: new Date('2022-01-01'),
    },
  });

  const loan2 = await prisma.loan.create({
    data: {
      userId: user.id,
      name: 'Personal Loan',
      loanType: 'PERSONAL',
      principal: 10000,
      currentBalance: 7200,
      interestRate: 9.99,
      termMonths: 48,
      monthlyPayment: 254,
      extraPayment: 50,
      startDate: new Date('2023-06-01'),
    },
  });

  console.log('✅ Created loans:', loan1.name, loan2.name);

  // Create test mortgage
  const mortgage = await prisma.mortgage.create({
    data: {
      userId: user.id,
      name: 'Main Home',
      propertyAddress: '123 Main Street, Anytown, USA',
      loanAmount: 350000,
      currentBalance: 342000,
      interestRate: 3.25,
      termYears: 30,
      monthlyPayment: 1520,
      extraPayment: 200,
      propertyTax: 4800,
      homeownersInsurance: 1200,
      pmi: 150,
      startDate: new Date('2021-03-01'),
    },
  });

  console.log('✅ Created mortgage:', mortgage.name);

  // Create test bills
  const bills = await prisma.bill.createMany({
    data: [
      {
        userId: user.id,
        name: 'Electric Bill',
        category: 'UTILITIES',
        amount: 120,
        frequency: 'MONTHLY',
        dueDay: 10,
        autoPay: true,
        isEssential: true,
      },
      {
        userId: user.id,
        name: 'Internet',
        category: 'UTILITIES',
        amount: 80,
        frequency: 'MONTHLY',
        dueDay: 1,
        autoPay: true,
        isEssential: true,
      },
      {
        userId: user.id,
        name: 'Netflix',
        category: 'SUBSCRIPTIONS',
        amount: 15.99,
        frequency: 'MONTHLY',
        isEssential: false,
      },
      {
        userId: user.id,
        name: 'Car Insurance',
        category: 'INSURANCE',
        amount: 150,
        frequency: 'MONTHLY',
        dueDay: 20,
        autoPay: true,
        isEssential: true,
      },
    ],
  });

  console.log('✅ Created bills:', bills.count, 'bills');

  // Create test goals
  const goal1 = await prisma.goal.create({
    data: {
      userId: user.id,
      name: 'Emergency Fund',
      goalType: 'EMERGENCY_FUND',
      targetAmount: 10000,
      currentAmount: 3500,
      monthlyContribution: 300,
      targetDate: new Date('2026-12-31'),
      priority: 'HIGH',
    },
  });

  const goal2 = await prisma.goal.create({
    data: {
      userId: user.id,
      name: 'Vacation Fund',
      goalType: 'SAVINGS',
      targetAmount: 5000,
      currentAmount: 1200,
      monthlyContribution: 150,
      priority: 'MEDIUM',
    },
  });

  console.log('✅ Created goals:', goal1.name, goal2.name);

  // Create test snapshot
  const snapshot = await prisma.snapshot.create({
    data: {
      userId: user.id,
      name: 'November 2025',
      snapshotDate: new Date('2025-11-01'),
      totalDebt: 372400,
      totalCreditCardDebt: 3700,
      totalLoanDebt: 25700,
      totalMortgageDebt: 342000,
      totalMonthlyPayments: 2490,
      totalMonthlyBills: 366,
      monthlyIncome: 5000,
      debtToIncomeRatio: 57.12,
      creditUtilization: 24.67,
    },
  });

  console.log('✅ Created snapshot:', snapshot.name);

  console.log('🎉 Seeding complete!');
}

main()
  .catch((e) => {
    console.error('❌ Error seeding database:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
