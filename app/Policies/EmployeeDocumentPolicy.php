<?php

namespace App\Policies;

use App\Models\EmployeeDocument;
use App\Models\User;

class EmployeeDocumentPolicy
{
    /**
     * Anti-IDOR: Check whether user can view/preview the document.
     */
    public function view(User $user, EmployeeDocument $document): bool
    {
        if ($user->hasRole('hr')) {
            return true;
        }

        // Employee can only view their own document
        return $document->employee && $user->id === $document->employee->user_id;
    }

    /**
     * Anti-IDOR: Check whether user can download the document.
     */
    public function download(User $user, EmployeeDocument $document): bool
    {
        if ($user->hasRole('hr')) {
            return true;
        }

        return $document->employee && $user->id === $document->employee->user_id;
    }

    /**
     * Only HR can upload documents for employees.
     */
    public function upload(User $user): bool
    {
        return $user->hasRole('hr');
    }

    /**
     * Only HR can delete employee documents.
     */
    public function delete(User $user): bool
    {
        return $user->hasRole('hr');
    }
}
