/**
 * Date utility functions for formatting and manipulation
 */

/**
 * Format a date string or Date object to a localized date string
 * @param dateInput - The date to format (string, Date, or null/undefined)
 * @param options - Intl.DateTimeFormat options
 * @returns Formatted date string or fallback
 */
export function formatDate(
  dateInput: string | Date | null | undefined,
  options: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  },
): string {
  if (!dateInput) return '-';

  try {
    const date =
      typeof dateInput === 'string' ? new Date(dateInput) : dateInput;

    if (!(date instanceof Date) || isNaN(date.getTime())) {
      return '-';
    }

    return new Intl.DateTimeFormat('en-US', options).format(date);
  } catch (error) {
    console.error('Error formatting date:', error);
    return '-';
  }
}

/**
 * Format a date string or Date object to a localized date and time string
 * @param dateInput - The date to format (string, Date, or null/undefined)
 * @param options - Intl.DateTimeFormat options
 * @returns Formatted datetime string or fallback
 */
export function formatDateTime(
  dateInput: string | Date | null | undefined,
  options: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  },
): string {
  if (!dateInput) return '-';

  try {
    const date =
      typeof dateInput === 'string' ? new Date(dateInput) : dateInput;

    if (!(date instanceof Date) || isNaN(date.getTime())) {
      return '-';
    }

    return new Intl.DateTimeFormat('en-US', options).format(date);
  } catch (error) {
    console.error('Error formatting datetime:', error);
    return '-';
  }
}

/**
 * Format a date for form input (YYYY-MM-DD format)
 * @param dateInput - The date to format
 * @returns Date string in YYYY-MM-DD format or empty string
 */
export function formatDateForInput(
  dateInput: string | Date | null | undefined,
): string {
  if (!dateInput) return '';

  try {
    const date =
      typeof dateInput === 'string' ? new Date(dateInput) : dateInput;

    if (!(date instanceof Date) || isNaN(date.getTime())) {
      return '';
    }

    return date.toISOString().split('T')[0];
  } catch (error) {
    console.error('Error formatting date for input:', error);
    return '';
  }
}

/**
 * Format a date for API submission (YYYY-MM-DD HH:mm:ss format)
 * @param dateInput - The date to format
 * @returns Date string in YYYY-MM-DD HH:mm:ss format or null
 */
export function formatDateForApi(
  dateInput: Date | null | undefined,
): string | null {
  if (
    !dateInput ||
    !(dateInput instanceof Date) ||
    isNaN(dateInput.getTime())
  ) {
    return null;
  }

  const pad = (num: number) => String(num).padStart(2, '0');

  const year = dateInput.getFullYear();
  const month = pad(dateInput.getMonth() + 1);
  const day = pad(dateInput.getDate());
  const hours = pad(dateInput.getHours());
  const minutes = pad(dateInput.getMinutes());
  const seconds = pad(dateInput.getSeconds());

  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

/**
 * Get relative time string (e.g., "2 days ago", "in 1 hour")
 * @param dateInput - The date to compare
 * @param baseDate - The base date to compare against (defaults to now)
 * @returns Relative time string
 */
export function getRelativeTime(
  dateInput: string | Date,
  baseDate: Date = new Date(),
): string {
  try {
    const date =
      typeof dateInput === 'string' ? new Date(dateInput) : dateInput;

    if (!(date instanceof Date) || isNaN(date.getTime())) {
      return '-';
    }

    const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' });
    const diffInSeconds = (date.getTime() - baseDate.getTime()) / 1000;

    const intervals = [
      { label: 'year', seconds: 31536000 },
      { label: 'month', seconds: 2592000 },
      { label: 'day', seconds: 86400 },
      { label: 'hour', seconds: 3600 },
      { label: 'minute', seconds: 60 },
      { label: 'second', seconds: 1 },
    ];

    for (const interval of intervals) {
      const count = Math.floor(Math.abs(diffInSeconds) / interval.seconds);
      if (count >= 1) {
        return rtf.format(
          diffInSeconds < 0 ? -count : count,
          interval.label as Intl.RelativeTimeFormatUnit,
        );
      }
    }

    return rtf.format(0, 'second');
  } catch (error) {
    console.error('Error getting relative time:', error);
    return '-';
  }
}

/**
 * Check if a date is today
 * @param dateInput - The date to check
 * @returns Whether the date is today
 */
export function isToday(dateInput: string | Date): boolean {
  try {
    const date =
      typeof dateInput === 'string' ? new Date(dateInput) : dateInput;

    if (!(date instanceof Date) || isNaN(date.getTime())) {
      return false;
    }

    const today = new Date();
    return date.toDateString() === today.toDateString();
  } catch (error) {
    return false;
  }
}

/**
 * Check if a date is in the past
 * @param dateInput - The date to check
 * @returns Whether the date is in the past
 */
export function isPast(dateInput: string | Date): boolean {
  try {
    const date =
      typeof dateInput === 'string' ? new Date(dateInput) : dateInput;

    if (!(date instanceof Date) || isNaN(date.getTime())) {
      return false;
    }

    return date < new Date();
  } catch (error) {
    return false;
  }
}
