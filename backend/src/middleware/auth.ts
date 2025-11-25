import { Request, Response, NextFunction } from 'express';
import jwt from 'jsonwebtoken';
import { ApiError } from './errorHandler';

export interface JwtPayload {
  userId: string;
  email: string;
  type?: 'access' | 'refresh';
}

declare global {
  namespace Express {
    interface Request {
      user?: JwtPayload;
    }
  }
}

/**
 * Authentication middleware
 * Verifies JWT access token from Authorization header
 */
export const authenticate = (
  req: Request,
  _res: Response,
  next: NextFunction
) => {
  try {
    const authHeader = req.headers.authorization;

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      throw new ApiError(401, 'No token provided');
    }

    const token = authHeader.substring(7);

    if (!process.env.JWT_SECRET) {
      throw new Error('JWT_SECRET not configured');
    }

    const decoded = jwt.verify(token, process.env.JWT_SECRET) as JwtPayload;

    // Ensure it's an access token
    if (decoded.type && decoded.type !== 'access') {
      throw new ApiError(401, 'Invalid token type');
    }

    req.user = decoded;

    next();
  } catch (error) {
    if (error instanceof jwt.JsonWebTokenError) {
      next(new ApiError(401, 'Invalid token'));
    } else if (error instanceof jwt.TokenExpiredError) {
      next(new ApiError(401, 'Token expired'));
    } else {
      next(error);
    }
  }
};

/**
 * Generate access token
 * Short-lived token for API access (7 days default)
 */
export const generateToken = (payload: JwtPayload): string => {
  const secret = process.env.JWT_SECRET;

  if (!secret) {
    throw new Error('JWT_SECRET not configured');
  }

  return jwt.sign(
    { ...payload, type: 'access' },
    secret,
    { expiresIn: (process.env.JWT_EXPIRES_IN || '7d') as any }
  );
};

/**
 * Generate refresh token
 * Long-lived token for obtaining new access tokens (30 days default)
 */
export const generateRefreshToken = (payload: JwtPayload): string => {
  const secret = process.env.JWT_REFRESH_SECRET || process.env.JWT_SECRET;

  if (!secret) {
    throw new Error('JWT_REFRESH_SECRET not configured');
  }

  return jwt.sign(
    { ...payload, type: 'refresh' },
    secret,
    { expiresIn: (process.env.JWT_REFRESH_EXPIRES_IN || '30d') as any }
  );
};

/**
 * Verify refresh token
 */
export const verifyRefreshToken = (token: string): JwtPayload => {
  const secret = process.env.JWT_REFRESH_SECRET || process.env.JWT_SECRET;

  if (!secret) {
    throw new Error('JWT_REFRESH_SECRET not configured');
  }

  const decoded = jwt.verify(token, secret) as JwtPayload;

  // Ensure it's a refresh token
  if (decoded.type !== 'refresh') {
    throw new ApiError(401, 'Invalid token type');
  }

  return decoded;
};

/**
 * Validate password strength
 * Requirements:
 * - At least 8 characters
 * - Contains at least one uppercase letter
 * - Contains at least one lowercase letter
 * - Contains at least one number
 * - Contains at least one special character
 */
export const validatePasswordStrength = (password: string): { valid: boolean; message?: string } => {
  if (password.length < 8) {
    return { valid: false, message: 'Password must be at least 8 characters long' };
  }

  if (password.length > 128) {
    return { valid: false, message: 'Password must not exceed 128 characters' };
  }

  if (!/[A-Z]/.test(password)) {
    return { valid: false, message: 'Password must contain at least one uppercase letter' };
  }

  if (!/[a-z]/.test(password)) {
    return { valid: false, message: 'Password must contain at least one lowercase letter' };
  }

  if (!/[0-9]/.test(password)) {
    return { valid: false, message: 'Password must contain at least one number' };
  }

  if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
    return { valid: false, message: 'Password must contain at least one special character (!@#$%^&*(),.?":{}|<>)' };
  }

  // Check for common passwords
  const commonPasswords = [
    'password', 'password123', '12345678', 'qwerty', 'abc123',
    'password1', '123456789', '1234567890', 'letmein', 'welcome'
  ];

  if (commonPasswords.includes(password.toLowerCase())) {
    return { valid: false, message: 'Password is too common. Please choose a stronger password' };
  }

  return { valid: true };
};
