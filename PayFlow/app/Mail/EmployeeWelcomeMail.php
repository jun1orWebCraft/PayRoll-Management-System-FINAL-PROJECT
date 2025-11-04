<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Employee;

class EmployeeWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Employee $employee;
    public string $plainPassword; // may be null if using reset link
    public string $qrCodeBase64;
    public ?string $resetUrl;

    /**
     * Create a new message instance.
     *
     * @param Employee $employee
     * @param string|null $plainPassword
     * @param string|null $qrCodeBase64
     * @param string|null $resetUrl
     */
    public function __construct(Employee $employee, ?string $plainPassword = null, ?string $qrCodeBase64 = null, ?string $resetUrl = null)
    {
        $this->employee = $employee;
        $this->plainPassword = $plainPassword;
        $this->qrCodeBase64 = $qrCodeBase64;
        $this->resetUrl = $resetUrl;
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
