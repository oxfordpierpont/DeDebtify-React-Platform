/**
 * Security Headers Configuration
 *
 * Configures Helmet middleware for enhanced security headers.
 */

import helmet from 'helmet';

/**
 * Helmet configuration with secure defaults
 */
export const helmetConfig = helmet({
  // Content Security Policy
  contentSecurityPolicy: {
    directives: {
      defaultSrc: ["'self'"],
      styleSrc: ["'self'", "'unsafe-inline'"],
      scriptSrc: ["'self'"],
      imgSrc: ["'self'", 'data:', 'https:'],
      connectSrc: ["'self'"],
      fontSrc: ["'self'"],
      objectSrc: ["'none'"],
      mediaSrc: ["'self'"],
      frameSrc: ["'none'"],
    },
  },

  // Strict Transport Security
  hsts: {
    maxAge: 31536000, // 1 year
    includeSubDomains: true,
    preload: true,
  },

  // Prevent clickjacking
  frameguard: {
    action: 'deny',
  },

  // Prevent MIME type sniffing
  noSniff: true,

  // XSS Protection
  xssFilter: true,

  // Hide powered-by header
  hidePoweredBy: true,

  // DNS Prefetch Control
  dnsPrefetchControl: {
    allow: false,
  },

  // Referrer Policy
  referrerPolicy: {
    policy: 'no-referrer',
  },
});
