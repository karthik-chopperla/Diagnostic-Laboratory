<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active', 'phone', 'department_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isChiefPathologist(): bool
    {
        return in_array($this->role, ['Chief Pathologist', 'Admin', 'Super Admin']);
    }

    public function isTechnician(): bool
    {
        return in_array($this->role, ['Lab Technician', 'Technician']);
    }

    public function isFrontDesk(): bool
    {
        return in_array($this->role, ['Front Desk & Billing', 'Front Desk', 'Receptionist']);
    }

    /**
     * Determine if user has permission to access a specific navigation route/module.
     */
    public function canAccessRoute(?string $routeName): bool
    {
        if (! $routeName || $routeName === 'logout') {
            return true;
        }

        if ($this->isChiefPathologist()) {
            return true;
        }

        // Lab Technician: Clinical testing, worklist, results, equipment, reagents, QC, critical alerts, sample processing
        if ($this->isTechnician()) {
            $allowed = [
                'dashboard',
                'search',
                'samples.index',
                'samples.store',
                'samples.status',
                'lab.departments',
                'lab.worklist',
                'lab.worklist.update',
                'lab.results',
                'lab.results.detail',
                'lab.results.save',
                'lab.reports',
                'lab.reports.show',
                'lab.critical',
                'lab.critical.ack',
                'catalog.tests',
                'catalog.tests.store',
                'ops.equipment',
                'ops.equipment.store',
                'ops.equipment.log',
                'ops.inventory',
                'ops.inventory.store',
                'ops.inventory.adjust',
                'ops.qc',
                'ops.qc.store',
            ];

            return in_array($routeName, $allowed);
        }

        // Front Desk & Billing: Patient registration, appointments, invoices, phlebotomy dispatch, CRM, doctor/patient portals
        if ($this->isFrontDesk()) {
            $allowed = [
                'dashboard',
                'search',
                'patients.index',
                'patients.store',
                'patients.show',
                'patients.documents',
                'patients.merge',
                'appointments.index',
                'appointments.store',
                'appointments.status',
                'billing.index',
                'billing.store',
                'billing.show',
                'billing.payments',
                'samples.index',
                'samples.store',
                'samples.status',
                'phlebotomy.index',
                'phlebotomy.store',
                'phlebotomy.status',
                'lab.reports',
                'lab.reports.show',
                'lab.reports.deliver',
                'catalog.packages',
                'catalog.packages.store',
                'network.doctors',
                'network.doctors.store',
                'network.corporates',
                'network.corporates.store',
                'portal.doctor',
                'portal.patient',
                'admin.crm',
                'admin.crm.leads',
                'admin.crm.leads.update',
                'admin.crm.campaigns',
                'admin.crm.reminders',
                'admin.crm.reminders.send',
                'admin.crm.tickets',
                'admin.crm.tickets.update',
            ];

            return in_array($routeName, $allowed);
        }

        return false;
    }
}
