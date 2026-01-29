<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ActivityService
{
    /**
     * Get activities for a subject model
     *
     * @param  Model  $subject  The model to get activities for
     * @param  int  $limit  Maximum number of activities to return
     */
    public function getActivitiesForSubject(Model $subject, int $limit = 50): Collection
    {
        return Activity::query()
            ->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey())
            ->with('causer')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by subject type and ID
     *
     * @param  string  $subjectType  The model class name (e.g., 'Invoice', 'Expense')
     * @param  int  $subjectId  The model ID
     * @param  int  $limit  Maximum number of activities to return
     */
    public function getActivitiesByTypeAndId(string $subjectType, int $subjectId, int $limit = 50): Collection
    {
        // Map short names to full class names
        $classMap = [
            'Invoice' => \App\Models\Invoice::class,
            'InvoicePayment' => \App\Models\InvoicePayment::class,
            'Expense' => \App\Models\Expense::class,
            'Transaction' => \App\Models\Transaction::class,
            'Payroll' => \App\Models\Payroll::class,
            'Distribution' => \App\Models\Distribution::class,
            'Transfer' => \App\Models\Transfer::class,
            'Account' => \App\Models\Account::class,
        ];

        $fullClassName = $classMap[$subjectType] ?? $subjectType;

        return Activity::query()
            ->where('subject_type', $fullClassName)
            ->where('subject_id', $subjectId)
            ->with('causer')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
