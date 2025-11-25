/**
 * Authentication Controller
 *
 * Handles user registration, login, and token management.
 */

import { Request, Response, NextFunction } from 'express';
import bcrypt from 'bcryptjs';
import { z } from 'zod';
import prisma from '../lib/prisma';
import { generateToken, generateRefreshToken, verifyRefreshToken, validatePasswordStrength } from '../middleware/auth';
import { ApiError } from '../middleware/errorHandler';

// ============================================
// VALIDATION SCHEMAS
// ============================================

const registerSchema = z.object({
  email: z.string().email('Invalid email address'),
  password: z.string().min(8, 'Password must be at least 8 characters'),
  firstName: z.string().optional(),
  lastName: z.string().optional(),
});

const loginSchema = z.object({
  email: z.string().email('Invalid email address'),
  password: z.string().min(1, 'Password is required'),
});

// ============================================
// REGISTER
// ============================================

/**
 * Register a new user
 *
 * POST /api/auth/register
 *
 * Body:
 * - email: string (required)
 * - password: string (required, min 8 chars)
 * - firstName: string (optional)
 * - lastName: string (optional)
 */
export async function register(req: Request, res: Response, next: NextFunction) {
  try {
    // Validate input
    const data = registerSchema.parse(req.body);

    // Validate password strength
    const passwordCheck = validatePasswordStrength(data.password);
    if (!passwordCheck.valid) {
      throw new ApiError(400, passwordCheck.message || 'Password does not meet requirements');
    }

    // Check if user already exists
    const existingUser = await prisma.user.findUnique({
      where: { email: data.email },
    });

    if (existingUser) {
      throw new ApiError(400, 'Email already registered');
    }

    // Hash password
    const hashedPassword = await bcrypt.hash(data.password, 10);

    // Create user
    const user = await prisma.user.create({
      data: {
        email: data.email,
        password: hashedPassword,
        firstName: data.firstName,
        lastName: data.lastName,
      },
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
        createdAt: true,
        updatedAt: true,
      },
    });

    // Generate access and refresh tokens
    const token = generateToken({
      userId: user.id,
      email: user.email,
    });

    const refreshToken = generateRefreshToken({
      userId: user.id,
      email: user.email,
    });

    res.status(201).json({
      success: true,
      data: {
        token,
        refreshToken,
        user,
      },
      message: 'User registered successfully',
    });
  } catch (error) {
    next(error);
  }
}

// ============================================
// LOGIN
// ============================================

/**
 * Login user
 *
 * POST /api/auth/login
 *
 * Body:
 * - email: string (required)
 * - password: string (required)
 */
export async function login(req: Request, res: Response, next: NextFunction) {
  try {
    // Validate input
    const data = loginSchema.parse(req.body);

    // Find user
    const user = await prisma.user.findUnique({
      where: { email: data.email },
    });

    if (!user) {
      throw new ApiError(401, 'Invalid email or password');
    }

    // Verify password
    const isPasswordValid = await bcrypt.compare(data.password, user.password);

    if (!isPasswordValid) {
      throw new ApiError(401, 'Invalid email or password');
    }

    // Generate access and refresh tokens
    const token = generateToken({
      userId: user.id,
      email: user.email,
    });

    const refreshToken = generateRefreshToken({
      userId: user.id,
      email: user.email,
    });

    // Return user without password
    const { password: _, ...userWithoutPassword } = user;

    res.json({
      success: true,
      data: {
        token,
        refreshToken,
        user: userWithoutPassword,
      },
      message: 'Login successful',
    });
  } catch (error) {
    next(error);
  }
}

// ============================================
// GET CURRENT USER
// ============================================

/**
 * Get current authenticated user
 *
 * GET /api/auth/me
 *
 * Headers:
 * - Authorization: Bearer <token>
 */
export async function getCurrentUser(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;

    if (!userId) {
      throw new ApiError(401, 'Not authenticated');
    }

    const user = await prisma.user.findUnique({
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
        notificationPreferences: true,
        createdAt: true,
        updatedAt: true,
      },
    });

    if (!user) {
      throw new ApiError(404, 'User not found');
    }

    res.json({
      success: true,
      data: user,
    });
  } catch (error) {
    next(error);
  }
}

// ============================================
// UPDATE USER PREFERENCES
// ============================================

const updatePreferencesSchema = z.object({
  firstName: z.string().optional(),
  lastName: z.string().optional(),
  monthlyIncome: z.number().positive().optional(),
  targetDebtFreeDate: z.string().datetime().optional(),
  preferredPayoffMethod: z.enum(['AVALANCHE', 'SNOWBALL', 'CUSTOM']).optional(),
  currency: z.string().optional(),
  timeZone: z.string().optional(),
  totalAssets: z.number().optional(),
  notificationPreferences: z.any().optional(),
});

/**
 * Update user preferences
 *
 * PATCH /api/auth/preferences
 *
 * Headers:
 * - Authorization: Bearer <token>
 *
 * Body: Any user preference fields to update
 */
export async function updatePreferences(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = (req as any).user?.userId;

    if (!userId) {
      throw new ApiError(401, 'Not authenticated');
    }

    // Validate input
    const data = updatePreferencesSchema.parse(req.body);

    // Update user
    const user = await prisma.user.update({
      where: { id: userId },
      data,
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
        notificationPreferences: true,
        createdAt: true,
        updatedAt: true,
      },
    });

    res.json({
      success: true,
      data: user,
      message: 'Preferences updated successfully',
    });
  } catch (error) {
    next(error);
  }
}

// ============================================
// REFRESH TOKEN
// ============================================

const refreshTokenSchema = z.object({
  refreshToken: z.string().min(1, 'Refresh token is required'),
});

/**
 * Refresh access token
 *
 * POST /api/auth/refresh
 *
 * Body:
 * - refreshToken: string (required)
 *
 * Returns new access token and refresh token
 */
export async function refreshAccessToken(req: Request, res: Response, next: NextFunction) {
  try {
    // Validate input
    const { refreshToken } = refreshTokenSchema.parse(req.body);

    // Verify refresh token
    const decoded = verifyRefreshToken(refreshToken);

    // Verify user still exists
    const user = await prisma.user.findUnique({
      where: { id: decoded.userId },
      select: { id: true, email: true },
    });

    if (!user) {
      throw new ApiError(401, 'User not found');
    }

    // Generate new tokens
    const newToken = generateToken({
      userId: user.id,
      email: user.email,
    });

    const newRefreshToken = generateRefreshToken({
      userId: user.id,
      email: user.email,
    });

    res.json({
      success: true,
      data: {
        token: newToken,
        refreshToken: newRefreshToken,
      },
      message: 'Token refreshed successfully',
    });
  } catch (error) {
    next(error);
  }
}
