<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * School-provisioned demo accounts in the STI Global City format.
 *  - Students: {lastname}.{student number without the "02000" prefix}@globalcity.sti.edu.ph
 *  - Staff:    {firstname}.{lastname}@globalcity.sti.edu.ph
 * All demo passwords: Demo@1234 (DEMO ONLY).
 * Liza Mendoza starts with a temporary password to demonstrate the forced password change.
 * Safe to run more than once: existing emails are skipped.
 */
class DemoUsersSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('Demo@1234');
        $domain   = '@globalcity.sti.edu.ph';

        $students = [
            ['Ana Ramos',      'ramos',    '02000356101', false],
            ['Juan Dela Cruz', 'delacruz', '02000111222', false],
            ['Maria Santos',   'santos',   '02000333444', false],
            ['Paolo Reyes',    'reyes',    '02000555666', false],
            ['Liza Mendoza',   'mendoza',  '02000777888', true], // temporary password demo
        ];

        $staff = [
            ['Carlo Villanueva', 'carlo.villanueva', 'teacher',      'EMP-1001'],
            ['Grace Lim',        'grace.lim',        'counselor',    'EMP-2001'],
            ['Rosa Bautista',    'rosa.bautista',    'clinic',       'EMP-3001'],
            ['Mark Aquino',      'mark.aquino',      'disciplinary', 'EMP-4001'],
            ['System Admin',     'guidancecare.admin', 'admin',      'EMP-9001'],
        ];

        $rows = [];

        foreach ($students as [$name, $last, $number, $temporary]) {
            $rows[] = [
                'name'                 => $name,
                'email'                => $last . '.' . substr($number, 5) . $domain,
                'role'                 => 'student',
                'student_number'       => $number,
                'employee_id'          => null,
                'must_change_password' => $temporary,
            ];
        }

        foreach ($staff as [$name, $local, $role, $employeeId]) {
            $rows[] = [
                'name'                 => $name,
                'email'                => $local . $domain,
                'role'                 => $role,
                'student_number'       => null,
                'employee_id'          => $employeeId,
                'must_change_password' => false,
            ];
        }

        foreach ($rows as $row) {
            if (DB::table('users')->where('email', $row['email'])->exists()) {
                continue;
            }

            DB::table('users')->insert($row + [
                'password'          => $password,
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
