/**
 * Theme Loader
 * 
 * Dynamically loads the correct theme based on tenant settings.
 * Defaults to 'classic' if no theme is specified.
 */

const AVAILABLE_THEMES = ['classic', 'modern', 'minimal']
const DEFAULT_THEME = 'classic'

/**
 * Get the active theme slug from tenant settings or default
 */
export function getActiveTheme() {
    if (typeof window !== 'undefined' && window.TENANT_THEME) {
        const theme = String(window.TENANT_THEME).toLowerCase().trim()
        if (AVAILABLE_THEMES.includes(theme)) {
            return theme
        }
        console.warn(`[ThemeLoader] Unknown theme "${theme}", falling back to "${DEFAULT_THEME}"`)
    }
    return DEFAULT_THEME
}

/**
 * Check if we're on a tenant subdomain (not main domain)
 */
export function isTenantHost() {
    if (typeof window === 'undefined') return false
    const host = window.location.host
    const mainDomain = window.MAIN_DOMAIN
    return host && mainDomain && host !== mainDomain
}

/**
 * Dynamically import and mount the active theme
 * Called from app.js when on a tenant subdomain
 */
export async function loadTenantTheme(app) {
    const theme = getActiveTheme()
    
    console.info(`[ThemeLoader] Loading theme: ${theme}`)
    
    try {
        // Dynamic import based on theme slug
        // Vite will code-split these into separate chunks
        const themeModule = await import(`../../themes/${theme}/main.js`)
        
        if (typeof themeModule.initTheme === 'function') {
            await themeModule.initTheme(app)
        }
        
        return { theme, success: true }
    } catch (err) {
        console.error(`[ThemeLoader] Failed to load theme "${theme}":`, err)
        
        // Fallback to classic if the requested theme fails
        if (theme !== DEFAULT_THEME) {
            console.info(`[ThemeLoader] Falling back to "${DEFAULT_THEME}"`)
            try {
                const fallback = await import(`../../themes/${DEFAULT_THEME}/main.js`)
                if (typeof fallback.initTheme === 'function') {
                    await fallback.initTheme(app)
                }
                return { theme: DEFAULT_THEME, success: true, fallback: true }
            } catch (fallbackErr) {
                console.error(`[ThemeLoader] Fallback failed:`, fallbackErr)
            }
        }
        
        return { theme, success: false, error: err }
    }
}

export { AVAILABLE_THEMES, DEFAULT_THEME }
