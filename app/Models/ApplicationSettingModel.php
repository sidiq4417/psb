<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ApplicationSettingModel extends Model
{
    protected $table = 'application_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    /**
     * Retrieve the first application settings record.
     *
     * @return object The settings record, or an empty object if none exists.
     */
    public function getSetting(): object
    {
        $row = $this->find(1);
        return $row ?: (object)[];
    }

    /**
     * Check if multiple registration per student is enabled.
     *
     * @return bool True if multiple registration is allowed.
     */
    public function isMultiRegistrationEnabled(): bool
    {
        $row = $this->find(1);
        return (bool)($row->allow_multiple_registration ?? false);
    }

    /**
     * Get the maximum number of registrations allowed per student.
     *
     * @return int The maximum registration count (defaults to 3).
     */
    public function getMaxRegistrations(): int
    {
        $row = $this->find(1);
        $max = (int)($row->max_registrations ?? 3);
        return $max > 0 ? $max : 3;
    }
}
