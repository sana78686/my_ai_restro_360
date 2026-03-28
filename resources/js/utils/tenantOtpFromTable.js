/**
 * Logs the pending OTP from the tenant `users` table (via debug API) next to host/tenant checks.
 * Requires SHOW_OTP_IN_CONSOLE / route enabled on the server.
 */
export async function logTenantOtpFromTableIfPending() {
  const mainDomain = window.MAIN_DOMAIN
  const isTenant = window.location.host && mainDomain && window.location.host !== mainDomain
  if (!isTenant || !window.axios) {
    return
  }

  const email = localStorage.getItem('pending_verification_email')
  if (!email) {
    console.log('your otp (from table):', '(no pending verification email)')
    return
  }

    try {
    const { data } = await window.axios.post('/tenant/debug/pending-otp', { email })
    if (data?.debug_table_otp != null && data.debug_table_otp !== '') {
      console.log('your otp (tenant users table):', data.debug_table_otp)
    } else {
      console.log('your otp (tenant users table):', '(none in database)')
    }
    if (data?.debug_tenant_verification_token != null && data.debug_tenant_verification_token !== '') {
      console.log('account verification token (central tenants table):', data.debug_tenant_verification_token)
    } else {
      console.log('account verification token (central tenants table):', '(none)')
    }
  } catch (err) {
    const status = err.response?.status
    console.log('your otp (from table):', `(unavailable${status ? ` — HTTP ${status}` : ''})`)
  }
}
