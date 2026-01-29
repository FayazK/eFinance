import { Activity } from '@/types';
import { useCallback, useEffect, useState } from 'react';

interface UseActivitiesOptions {
    subjectType: string;
    subjectId: number;
    enabled?: boolean;
}

interface UseActivitiesResult {
    activities: Activity[];
    isLoading: boolean;
    error: string | null;
    refetch: () => Promise<void>;
}

export function useActivities({ subjectType, subjectId, enabled = true }: UseActivitiesOptions): UseActivitiesResult {
    const [activities, setActivities] = useState<Activity[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchActivities = useCallback(async () => {
        if (!enabled || !subjectType || !subjectId) {
            return;
        }

        setIsLoading(true);
        setError(null);

        try {
            const response = await fetch(`/dashboard/activities/${subjectType}/${subjectId}`, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`Failed to fetch activities: ${response.statusText}`);
            }

            const data = await response.json();
            setActivities(data.data || []);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to fetch activities');
            setActivities([]);
        } finally {
            setIsLoading(false);
        }
    }, [subjectType, subjectId, enabled]);

    useEffect(() => {
        fetchActivities();
    }, [fetchActivities]);

    return {
        activities,
        isLoading,
        error,
        refetch: fetchActivities,
    };
}
