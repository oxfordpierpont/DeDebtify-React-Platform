/**
 * Authentication Routes
 *
 * Routes for user registration, login, and profile management.
 */

import { Router } from 'express';
import * as authController from '../controllers/auth.controller';
import { authenticate } from '../middleware/auth';
import { authLimiter } from '../middleware/rateLimiter';

const router = Router();

// Public routes with strict rate limiting
router.post('/register', authLimiter, authController.register);
router.post('/login', authLimiter, authController.login);
router.post('/refresh', authLimiter, authController.refreshAccessToken);

// Protected routes
router.get('/me', authenticate, authController.getCurrentUser);
router.patch('/preferences', authenticate, authController.updatePreferences);

export default router;
