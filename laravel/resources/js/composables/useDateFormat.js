export function useDateFormat() {
  const formatBirthdate = (dateString, locale = 'en-US') => {
    if (!dateString) return '';

    // Handle full ISO timestamps (e.g., 1995-08-24T14:30:00Z) vs date-only strings
    const normalizedDate = dateString.includes('T') 
      ? new Date(dateString) 
      : new Date(`${dateString}T00:00:00`);

    if (isNaN(normalizedDate)) return 'Invalid Date';

    return new Intl.DateTimeFormat(locale, {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    }).format(normalizedDate);
  };

  return { formatBirthdate };
}
