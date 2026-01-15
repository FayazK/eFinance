import axios from 'axios';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';

interface DropdownOption {
    id: number;
    name: string;
    [key: string]: unknown;
}

const useDropdown = (type: string, params: object = {}, id: number | null = null) => {
    const [options, setOptions] = useState<DropdownOption[]>([]);
    const [loading, setLoading] = useState(false);

    // Track previous params to detect when to clear options
    const prevParamsKey = useRef<string>('');

    // Stabilize params in dependencies to avoid infinite re-renders
    const paramsKey = useMemo(() => JSON.stringify(params || {}), [params]);

    const fetchOptions = useCallback(
        async (search = '') => {
            setLoading(true);
            try {
                const response = await axios.get('/dropdown', {
                    params: {
                        type,
                        search,
                        id,
                        ...params,
                    },
                });
                setOptions(response.data);
            } catch (error) {
                console.error('Failed to fetch dropdown options', error);
            }
            setLoading(false);
        },
        [type, id, paramsKey],
    );

    useEffect(() => {
        // Clear options when params change (e.g., country changed, so clear state options)
        if (prevParamsKey.current !== '' && prevParamsKey.current !== paramsKey) {
            setOptions([]);
        }
        prevParamsKey.current = paramsKey;
        fetchOptions();
    }, [fetchOptions, paramsKey]);

    return { options, loading, fetchOptions };
};

export default useDropdown;
