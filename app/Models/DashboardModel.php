<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Database\BaseConnection;
use CodeIgniter\Model;
use Config\Database;

class DashboardModel extends Model
{
    protected BaseConnection $dbs;

    protected $table = 'registrations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'student_id', 'admission_offering_id', 'admission_period_id',
        'admission_year', 'registration_number', 'submitted',
        'selection_status', 'admission_fee', 'transfer_proof',
        'is_deleted',
    ];
    protected $returnType = 'object';

    /**
     * Initialize the model with a secondary database connection for raw queries.
     */
    public function __construct()
    {
        parent::__construct();
        $this->dbs = Database::connect();
    }

    /**
     * Get summary statistics for a given admission year.
     *
     * @param int $admission_year The admission year.
     * @return object Object with total, finalized, verified, and pending counts.
     */
    public function getSummaryStats(int $admission_year): object
    {
        $totalReg = $this->dbs->query('SELECT COUNT(*) AS total FROM registrations WHERE admission_year = ?', [$admission_year])->getRow()->total;
        $totalFinal = $this->dbs->query('SELECT COUNT(*) AS total FROM registrations WHERE admission_year = ? AND submitted = TRUE', [$admission_year])->getRow()->total;
        $totalVerified = $this->dbs->query('SELECT COUNT(*) AS total FROM registrations WHERE admission_year = ? AND transfer_proof_verified = TRUE', [$admission_year])->getRow()->total;
        $totalPending = $this->dbs->query('SELECT COUNT(*) AS total FROM registrations WHERE admission_year = ? AND transfer_proof_verified = FALSE AND transfer_proof_uploaded_at IS NOT NULL', [$admission_year])->getRow()->total;

        return (object)[
            'total' => (int)($totalReg ?? 0),
            'finalized' => (int)($totalFinal ?? 0),
            'verified' => (int)($totalVerified ?? 0),
            'pending' => (int)($totalPending ?? 0)
        ];
    }

    /**
     * Get all registrations created today.
     *
     * @return array The registration records with student name and status.
     */
    public function getRegistrationsToday(): array
    {
        return $this->dbs->query('
            SELECT r.registration_number,
                   u.full_name,
                   r.submitted
            FROM registrations r
            LEFT JOIN users u ON r.student_id = u.id
            WHERE DATE(r.created_at) = CURDATE()
            ORDER BY r.created_at DESC
        ')->getResult();
    }

    /**
     * Get registrations with pending (unverified) transfer payments.
     *
     * @param int $admission_year The admission year.
     * @return array The registration records awaiting payment verification.
     */
    public function getPendingPayments(int $admission_year): array
    {
        return $this->dbs->query('
            SELECT r.registration_number,
                   u.full_name,
                   r.transfer_proof_uploaded_at AS uploaded_at
            FROM registrations r
            LEFT JOIN users u ON r.student_id = u.id
            WHERE r.admission_year = ?
              AND r.transfer_proof_uploaded_at IS NOT NULL
              AND r.transfer_proof_verified = FALSE
            ORDER BY r.transfer_proof_uploaded_at ASC
        ', [$admission_year])->getResult();
    }

    /**
     * Get daily registration counts for the last 14 days.
     *
     * @return array Each entry contains date_label and total.
     */
    public function getRegistrationTrend(): array
    {
        return $this->dbs->query('
            SELECT days.dt AS date_label, COUNT(r.id) AS total
            FROM (
                SELECT CURDATE() - INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS dt
                FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
                CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
            ) AS days
            LEFT JOIN registrations r ON DATE(r.created_at) = days.dt AND r.is_deleted = FALSE
            WHERE days.dt BETWEEN CURDATE() - INTERVAL 13 DAY AND CURDATE()
            GROUP BY days.dt
            ORDER BY days.dt ASC
        ')->getResult();
    }

    /**
     * Get the most popular entry grades (majors) by registration count.
     *
     * @param int $admission_year The admission year.
     * @param int $limit          Maximum number of results to return.
     * @return array The result rows with name and total.
     */
    public function getPopularMajors(int $admission_year, int $limit = 5): array
    {
        return $this->dbs->query("
            SELECT COALESCE(e.name, 'Tidak Memilih') AS name, COUNT(r.id) as total
            FROM registrations r
            LEFT JOIN admission_offerings xoffering ON r.admission_offering_id = xoffering.id
            LEFT JOIN entry_grades e ON xoffering.entry_grade_id = e.id
            WHERE r.admission_year = ? AND r.is_deleted = FALSE
            GROUP BY e.name
            ORDER BY total DESC
            LIMIT ?
        ", [$admission_year, $limit])->getResult();
    }
}
