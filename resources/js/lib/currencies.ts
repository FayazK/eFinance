export interface Currency {
    code: string;
    name: string;
    symbol: string;
}

export const SUPPORTED_CURRENCIES: Currency[] = [
    {
        code: 'USD',
        name: 'US Dollar',
        symbol: '$',
    },
    {
        code: 'PKR',
        name: 'Pakistani Rupee',
        symbol: 'Rs',
    },
    {
        code: 'EUR',
        name: 'Euro',
        symbol: '€',
    },
    {
        code: 'GBP',
        name: 'British Pound',
        symbol: '£',
    },
    {
        code: 'AED',
        name: 'UAE Dirham',
        symbol: 'د.إ',
    },
];

export const getCurrencyByCode = (code: string): Currency | undefined => {
    return SUPPORTED_CURRENCIES.find((currency) => currency.code === code);
};

export const getCurrencySymbol = (code: string): string => {
    return getCurrencyByCode(code)?.symbol || code;
};

export const getCurrencyName = (code: string): string => {
    return getCurrencyByCode(code)?.name || code;
};
