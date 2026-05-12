/**
 * AiRestro360 Shared Core
 * 
 * This module exports all shared utilities, composables, and stores
 * that themes can import without duplicating business logic.
 */

// Re-export i18n
export { default as i18n, setLanguage, applyDocumentLocale } from '../i18n'

// Re-export utilities
export { getAppName, getAppNameSync } from '../utils/appName'
export { getTurnstileSiteKey, isTurnstileEnabled } from '../utils/turnstile'

// Re-export composables
export { useCart } from '../composables/useCart'
export { useTenantBranches } from '../composables/useTenantBranches'
export { useSettingsSectionProgress } from '../composables/useSettingsSectionProgress'
