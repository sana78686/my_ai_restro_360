// Contact information utility
export const getContactInfo = () => {
  return {
    email: import.meta.env.VITE_CONTACT_EMAIL || 'info@apimstec.com',
    phone: import.meta.env.VITE_CONTACT_PHONE || '+1 234 567 8900',
    address: import.meta.env.VITE_CONTACT_ADDRESS || '123 Business Street, NY 10001',
    hours: import.meta.env.VITE_CONTACT_HOURS || 'Mon - Fri: 9:00 AM - 6:00 PM',
  }
}

export const contactEmail = import.meta.env.VITE_CONTACT_EMAIL || 'info@apimstec.com'
export const contactPhone = import.meta.env.VITE_CONTACT_PHONE || '+1 234 567 8900'
export const contactAddress = import.meta.env.VITE_CONTACT_ADDRESS || '123 Business Street, NY 10001'
export const contactHours = import.meta.env.VITE_CONTACT_HOURS || 'Mon - Fri: 9:00 AM - 6:00 PM'

