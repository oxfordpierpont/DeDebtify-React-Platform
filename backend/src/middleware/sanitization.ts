/**
 * Input Sanitization Middleware
 *
 * Sanitizes user input to prevent XSS and injection attacks.
 */

import { Request, Response, NextFunction } from 'express';

/**
 * Sanitize a string value
 * Removes potentially dangerous characters and HTML tags
 */
function sanitizeString(value: string): string {
  if (typeof value !== 'string') return value;

  // Remove HTML tags
  let sanitized = value.replace(/<[^>]*>/g, '');

  // Trim whitespace
  sanitized = sanitized.trim();

  // Remove null bytes
  sanitized = sanitized.replace(/\0/g, '');

  // Limit length to prevent DoS
  if (sanitized.length > 10000) {
    sanitized = sanitized.substring(0, 10000);
  }

  return sanitized;
}

/**
 * Recursively sanitize an object
 */
function sanitizeObject(obj: any): any {
  if (obj === null || obj === undefined) {
    return obj;
  }

  if (typeof obj === 'string') {
    return sanitizeString(obj);
  }

  if (Array.isArray(obj)) {
    return obj.map(sanitizeObject);
  }

  if (typeof obj === 'object') {
    const sanitized: any = {};
    for (const key in obj) {
      if (obj.hasOwnProperty(key)) {
        sanitized[key] = sanitizeObject(obj[key]);
      }
    }
    return sanitized;
  }

  return obj;
}

/**
 * Middleware to sanitize request body, query, and params
 */
export function sanitizeInput(req: Request, _res: Response, next: NextFunction) {
  try {
    // Sanitize body
    if (req.body) {
      req.body = sanitizeObject(req.body);
    }

    // Sanitize query parameters
    if (req.query) {
      req.query = sanitizeObject(req.query);
    }

    // Sanitize URL parameters
    if (req.params) {
      req.params = sanitizeObject(req.params);
    }

    next();
  } catch (error) {
    next(error);
  }
}

/**
 * Validate that string doesn't contain SQL injection patterns
 * Note: Prisma ORM already prevents SQL injection, but this adds extra layer
 */
export function preventSQLInjection(value: string): boolean {
  if (typeof value !== 'string') return true;

  const sqlPatterns = [
    /(\b(SELECT|INSERT|UPDATE|DELETE|DROP|CREATE|ALTER|EXEC|EXECUTE)\b)/i,
    /(--|;|\/\*|\*\/)/,
    /(\bOR\b|\bAND\b).*?=.*?=/i,
  ];

  return !sqlPatterns.some(pattern => pattern.test(value));
}

/**
 * Middleware to check for SQL injection attempts
 */
export function sqlInjectionCheck(req: Request, res: Response, next: NextFunction): void {
  try {
    const checkObject = (obj: any): boolean => {
      if (typeof obj === 'string') {
        return preventSQLInjection(obj);
      }
      if (Array.isArray(obj)) {
        return obj.every(checkObject);
      }
      if (typeof obj === 'object' && obj !== null) {
        return Object.values(obj).every(checkObject);
      }
      return true;
    };

    const isSafe =
      checkObject(req.body) &&
      checkObject(req.query) &&
      checkObject(req.params);

    if (!isSafe) {
      res.status(400).json({
        success: false,
        error: 'Invalid input detected',
        statusCode: 400,
      });
      return;
    }

    next();
  } catch (error) {
    next(error);
  }
}
