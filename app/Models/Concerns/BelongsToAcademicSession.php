<?php

namespace App\Models\Concerns;

use App\Models\AcademicSession;
use App\Services\AcademicSessionService;

trait BelongsToAcademicSession
{
    public static function bootBelongsToAcademicSession(): void
    {
        static::addGlobalScope('academic_session', function ($builder) {
            if (AcademicSessionService::isBypassingScope()) {
                return;
            }

            $sessionId = AcademicSessionService::scopeSessionId();
            if ($sessionId) {
                $builder->where($builder->getModel()->getTable().'.session_id', $sessionId);
            }
        });

        static::creating(function ($model) {
            if (empty($model->session_id)) {
                $model->session_id = AcademicSessionService::assignmentSessionId();
            }
        });
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class, 'session_id');
    }
}
