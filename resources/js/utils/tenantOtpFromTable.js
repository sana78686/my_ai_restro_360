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
      console.log('your otp (from table):', data.debug_table_otp)
    } else {
      console.log('your otp (from table):', '(none in database)')
    }
  } catch (err) {
    const status = err.response?.status
    console.log('your otp (from table):', `(unavailable${status ? ` — HTTP ${status}` : ''})`)
  }
}
