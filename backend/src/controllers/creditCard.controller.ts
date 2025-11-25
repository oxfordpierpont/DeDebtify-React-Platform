/**
 * Credit Card Controller
 *
 * CRUD operations for credit cards.
 */

import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../lib/prisma';
import { ApiError } from '../middleware/errorHandler';

// ============================================
// VALIDATION SCHEMAS
// ============================================

const createCreditCardSchema = z.object({
  name: z.string().min(1, 'Name is required'),
  balance: z.number().nonnegative('Balance must be non-negative'),
  creditLimit: z.number().positive('Credit limit must be positive'),
  interestRate: z.number().nonnegative('Interest rate must be non-negative').max(100, 'Interest rate cannot exceed 100%'),
  minimumPayment: z.number().nonnegative('Minimum payment must be non-negative'),
  extraPayment: z.number().nonnegative('Extra payment must be non-negative').optional().default(0),
  dueDay: z.number().int().min(1).max(31).optional(),
  autoPay: z.boolean().optional().default(false),
});

const updateCreditCardSchema = createCreditCardSchema.partial();

// ============================================
// CREATE
// ============================================

/**
 * Create a new credit card
 *
 * POST /api/credit-cards
 */
export async function createCreditCard(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;

    if (!userId) {
      throw new ApiError(401, 'Not authenticated');
    }

    const data = createCreditCardSchema.parse(req.body);

    const creditCard = await prisma.creditCard.create({
      data: {
        ...data,
        userId,
      },
    });

    res.status(201).json({
      success: true,
      data: creditCard,
      message: 'Credit card created successfully',
    });
  } catch (error) {
    next(error);
  }
}

// ============================================
// GET ALL
// ============================================

/**
 * Get all credit cards for current user
 *
 * GET /api/credit-cards
 */
export async function getAllCreditCards(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;

    if (!userId) {
      throw new ApiError(401, 'Not authenticated');
    }

    const creditCards = await prisma.creditCard.findMany({
      where: { userId },
      orderBy: { createdAt: 'desc' },
    });

    res.json({
      success: true,
      data: creditCards,
    });
  } catch (error) {
    next(error);
  }
}

// ============================================
// GET ONE
// ============================================

/**
 * Get a single credit card by ID
 *
 * GET /api/credit-cards/:id
 */
export async function getCreditCardById(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;

    if (!userId) {
      throw new ApiError(401, 'Not authenticated');
    }

    const creditCard = await prisma.creditCard.findFirst({
      where: {
        id,
        userId,
      },
    });

    if (!creditCard) {
      throw new ApiError(404, 'Credit card not found');
    }

    res.json({
      success: true,
      data: creditCard,
    });
  } catch (error) {
    next(error);
  }
}

// ============================================
// UPDATE
// ============================================

/**
 * Update a credit card
 *
 * PATCH /api/credit-cards/:id
 */
export async function updateCreditCard(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;

    if (!userId) {
      throw new ApiError(401, 'Not authenticated');
    }

    const data = updateCreditCardSchema.parse(req.body);

    // Verify ownership
    const existing = await prisma.creditCard.findFirst({
      where: { id, userId },
    });

    if (!existing) {
      throw new ApiError(404, 'Credit card not found');
    }

    const creditCard = await prisma.creditCard.update({
      where: { id },
      data,
    });

    res.json({
      success: true,
      data: creditCard,
      message: 'Credit card updated successfully',
    });
  } catch (error) {
    next(error);
  }
}

// ============================================
// DELETE
// ============================================

/**
 * Delete a credit card
 *
 * DELETE /api/credit-cards/:id
 */
export async function deleteCreditCard(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;
    const { id } = req.params;

    if (!userId) {
      throw new ApiError(401, 'Not authenticated');
    }

    // Verify ownership
    const existing = await prisma.creditCard.findFirst({
      where: { id, userId },
    });

    if (!existing) {
      throw new ApiError(404, 'Credit card not found');
    }

    await prisma.creditCard.delete({
      where: { id },
    });

    res.json({
      success: true,
      message: 'Credit card deleted successfully',
    });
  } catch (error) {
    next(error);
  }
}
