<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Models\AcademicYearModel;
use App\Models\AdmissionOfferingModel;
use App\Models\AdmissionPeriodModel;
use App\Models\ApplicationSettingModel;
use App\Models\BranchModel;
use App\Models\RegistrationDocumentModel;
use App\Models\RegistrationExamScoreModel;
use App\Models\RegistrationModel;
use App\Models\RegistrationSubjectGradeModel;
use App\Traits\DataTrait;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class AdmissionController extends BaseController
{
    protected int $actor = 0;

    use DataTrait;

    public function __construct()
    {
        $this->actor = session()->get('actor') ?? 0;
    }

    public function index(): string
    {
        $periodModel = new AdmissionPeriodModel();

        $admission_periods = [];
        $period_academic_year_map = [];
        foreach ($periodModel->findAll() as $p) {
            $admission_periods[$p->id] = $p->name;
            $period_academic_year_map[$p->id] = (int)$p->academic_year_id;
        }

        $branches = [];
        foreach (new BranchModel()->findAll() as $row) {
            $branches[$row->id] = $row->name;
        }

        $academic_years = [];
        foreach (new AcademicYearModel()->findAll() as $row) {
            $academic_years[$row->id] = $row->name;
        }

        $vars = [
            'title' => 'Pilihan Pendaftaran',
            'breadcrumb' => 'Pilihan Pendaftaran',
            'branches' => $branches,
            'academic_years' => $academic_years,
            'admission_periods' => $admission_periods,
            'period_academic_year_map' => $period_academic_year_map,
            'allow_multiple' => new ApplicationSettingModel()->isMultiRegistrationEnabled(),
        ];

        return view('students/admissions', $vars);
    }

    public function lists(): ResponseInterface
    {
        $branch_id = (int) $this->request->getVar('branch_id');
        $admission_period_id = (int) $this->request->getVar('admission_period_id');

        $applicationSetting = new ApplicationSettingModel();
        $allow_multiple = $applicationSetting->isMultiRegistrationEnabled();

        $admission_offering_ids = [];
        $admission_offering_id = 0;
        $submitted = false;

        $registrationModel = new RegistrationModel();
        $activeRegs = $registrationModel->findActiveByStudent($this->actor);

        foreach ($activeRegs as $reg) {
            if (!empty($reg->submitted)) {
                $submitted = true;
            }
            $oid = (int)($reg->admission_offering_id ?? 0);
            if ($oid > 0) {
                $admission_offering_ids[] = $oid;
            }
        }
        $admission_offering_ids = array_unique($admission_offering_ids);

        if (!empty($admission_offering_ids)) {
            $admission_offering_id = $admission_offering_ids[0];
        }

        $query = new AdmissionOfferingModel()->findPublicList($branch_id, $admission_period_id);

        $res = [
            'records' => $query->getResult(),
            'admission_offering_id' => $admission_offering_id,
            'admission_offering_ids' => $admission_offering_ids,
            'submitted' => $submitted,
            'allow_multiple' => $allow_multiple,
        ];

        return $this->response->setJSON($res);
    }

    /**
     * @throws ReflectionException
     */
    public function save(): ResponseInterface
    {
        $validation = service('validation');
        $validation->setRules([
            'admission_offering_id' => ['label' => 'Pembukaan Pendaftaran', 'rules' => 'required'],
        ]);

        if (!$validation->run((array) $this->request->getVar())) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data yang diisi belum lengkap.',
                'errors' => $validation->getErrors(),
            ]);
        }

        $admission_offering_id = (int) $this->request->getVar('admission_offering_id') ?? 0;
        $offeringModel = new AdmissionOfferingModel();
        $offering = $offeringModel->find($admission_offering_id);
        if (!$offering) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pembukaan pendaftaran tidak ditemukan.',
            ]);
        }

        $admission_period_id = (int)($offering->admission_period_id ?? 0);
        $admission_year = 0;
        $admission_fee = 0;

        if ($admission_period_id > 0) {
            $periodModel = new AdmissionPeriodModel();
            $period = $periodModel->find($admission_period_id);
            if ($period) {
                $admission_year = $periodModel->getAdmissionYearByPeriodId($admission_period_id);
                $admission_fee = (int)($period->registration_fee ?? 0);
            }
        }

        $unique_id = 'registration:' . $this->actor . ':' . $admission_year;
        $applicationSetting = new ApplicationSettingModel();
        $allow_multiple = $applicationSetting->isMultiRegistrationEnabled();
        $registrationModel = new RegistrationModel();

        if ($allow_multiple) {
            $existingData = $registrationModel->findActiveByOffering($this->actor, $admission_offering_id);

            if ($existingData) {
                return $this->response->setJSON([
                    'id' => $existingData->id,
                    'success' => true,
                    'message' => 'Already selected.',
                    'admission_offering_id' => $admission_offering_id,
                    'admission_offering_ids' => $registrationModel->getSelectedOfferingIds($this->actor),
                    'submitted' => false,
                    'allow_multiple' => true,
                ]);
            }

            $new_level = $offeringModel->getEducationLevel($admission_offering_id);
            $active_regs = $registrationModel->findActiveByStudent($this->actor);

            $max_registrations = $applicationSetting->getMaxRegistrations();
            if (count($active_regs) >= $max_registrations) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Anda sudah mencapai batas maksimal pendaftaran ($max_registrations program studi). Silakan selesaikan atau batalkan pendaftaran yang ada sebelum menambah yang baru.",
                ]);
            }

            $new_group = AdmissionOfferingModel::getLevelGroup($new_level);
            foreach ($active_regs as $reg) {
                $reg_level = $offeringModel->getEducationLevel((int)$reg->admission_offering_id);
                $reg_group = AdmissionOfferingModel::getLevelGroup($reg_level);
                if ($reg_level !== '' && $new_level !== '' && $reg_group !== $new_group) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => "Tidak dapat mendaftar ke jenjang yang berbeda. Anda sudah memiliki pendaftaran aktif di jenjang $reg_level. Hanya jenjang yang setara (SD/MI, SMP/MTs, SMA/MA/SMK) yang dapat dipilih bersamaan.",
                    ]);
                }
            }

            $submittedData = $registrationModel->findSubmittedByStudentSingle($this->actor);
            if ($submittedData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Anda sudah memiliki pendaftaran yang telah disubmit.',
                ]);
            }

            $actor = (int)session()->get('actor');
            $insertData = [
                'student_id' => $actor,
                'admission_year' => $admission_year,
                'admission_fee' => $admission_fee,
                'admission_offering_id' => $admission_offering_id,
                'unique_id' => $unique_id . ':' . $admission_offering_id,
                'created_by' => $actor,
            ];
            $insertId = $registrationModel->insert($insertData);

            return $this->response->setJSON([
                'id' => $insertId,
                'success' => $insertId > 0,
                'message' => $insertId > 0 ? 'Saved successfully.' : 'Error occurred.',
                'admission_offering_id' => $admission_offering_id,
                'admission_offering_ids' => $registrationModel->getSelectedOfferingIds($this->actor),
                'submitted' => false,
                'allow_multiple' => true,
            ]);
        }

        $existingData = $registrationModel->where('unique_id', $unique_id)->where('is_deleted', false)->first();

        if ($existingData && isset($existingData->submitted) && (int)$existingData->submitted === 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pendaftaran sudah disubmit dan tidak dapat diubah lagi!',
            ]);
        }

        $actor = (int)session()->get('actor');
        $updateData = [
            'student_id' => $actor,
            'admission_year' => $admission_year,
            'admission_fee' => $admission_fee,
            'admission_offering_id' => $admission_offering_id,
            'unique_id' => $unique_id,
            'created_by' => $actor,
        ];

        if ($existingData && isset($existingData->id) && $existingData->id > 0) {
            $updateData['updated_by'] = $actor;
            unset($updateData['created_by']);
            $registrationModel->update($existingData->id, $updateData);
            return $this->response->setJSON([
                'id' => $existingData->id,
                'success' => true,
                'message' => 'Updated successfully.',
                'admission_offering_id' => $admission_offering_id,
                'admission_offering_ids' => $admission_offering_id > 0 ? [$admission_offering_id] : [],
                'submitted' => (bool)($existingData->submitted ?? false),
                'allow_multiple' => false,
            ]);
        }

        $insertId = $registrationModel->insert($updateData);
        return $this->response->setJSON([
            'id' => $insertId,
            'success' => $insertId > 0,
            'message' => $insertId > 0 ? 'Saved successfully.' : 'Error occurred.',
            'admission_offering_id' => $insertId > 0 ? $admission_offering_id : 0,
            'admission_offering_ids' => $insertId > 0 ? [$admission_offering_id] : [],
            'submitted' => false,
            'allow_multiple' => false,
        ]);
    }

    /**
     * @throws ReflectionException
     */
    public function remove(): ResponseInterface
    {
        $admission_offering_id = (int) $this->request->getVar('admission_offering_id') ?? 0;
        if ($admission_offering_id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid offering ID.',
            ]);
        }

        $registrationModel = new RegistrationModel();
        $existingData = $registrationModel->findActiveByOffering($this->actor, $admission_offering_id);

        if (!$existingData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Registration not found.',
            ]);
        }

        $transfer_proof_verified = $existingData->transfer_proof_verified ?? false;
        if ((int)$transfer_proof_verified === 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pembayaran sudah diverifikasi admin. Tidak dapat membatalkan pendaftaran.',
            ]);
        }

        $registrationModel->update($existingData->id, [
            'admission_offering_id' => 0,
            'transfer_proof' => null,
            'transfer_proof_uploaded_at' => null,
            'transfer_proof_uploaded_by' => null,
        ]);
        new RegistrationSubjectGradeModel()->deleteByRegistrationId((int)$existingData->id);
        new RegistrationDocumentModel()->deleteByRegistrationId((int)$existingData->id);
        new RegistrationExamScoreModel()->deleteByRegistrationId((int)$existingData->id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Removed successfully.',
            'admission_offering_ids' => $registrationModel->getSelectedOfferingIds($this->actor),
        ]);
    }
}
