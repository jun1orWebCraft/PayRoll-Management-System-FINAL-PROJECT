<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;

class EmployeeWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Employee $employee;
    public ?string $plainPassword; // may be null if using reset link
    public ?string $qrCodeBase64; // can be null
    public ?string $resetUrl;

    /**
     * Create a new message instance.
     *
     * @param Employee $employee
     * @param string|null $plainPassword
     * @param string|null $resetUrl
     */
    public function __construct(Employee $employee, ?string $plainPassword = null, ?string $resetUrl = null)
    {
        $this->employee = $employee;
        $this->plainPassword = $plainPassword;
        $this->resetUrl = $resetUrl;

        // Get QR code: supports base64 or file path
        if ($employee->QR_code) {
            if (str_starts_with($employee->QR_code, 'data:image')) {
                $this->qrCodeBase64 = $employee->QR_code;
            } elseif (Storage::exists($employee->QR_code)) {
                $this->qrCodeBase64 = 'data:image/png;base64,' . base64_encode(Storage::get($employee->QR_code));
            } else {
                $this->qrCodeBase64 = null;
            }
        } else {
            $this->qrCodeBase64 = null;
        }
    }

    public function build()
    {
        return $this->subject('Welcome to IDSC Portal')
                    ->markdown('emails.employee.welcome')
                    ->with([
                        'employee' => $this->employee,
                        'plainPassword' => $this->plainPassword,
                        'qrCodeBase64' => $this->qrCodeBase64,
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
}
