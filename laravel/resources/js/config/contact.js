export const CONTACT_EMAIL = import.meta.env.VITE_CONTACT_EMAIL || 'fallback@example.com';

//  Export the ready-to-use mailto string directly
export const CONTACT_MAILTO = `mailto:${CONTACT_EMAIL}`;
