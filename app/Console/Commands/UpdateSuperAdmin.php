<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UpdateSuperAdmin extends Command
{
    protected $signature = 'admin:update-super
                            {--email= : New email address for the Super Admin}
                            {--current-email=superadmin@andosmusic.com : Current email to identify the account}';

    protected $description = 'Update the Super Admin account email and/or password securely';

    public function handle()
    {
        $currentEmail = $this->option('current-email');
        $newEmail = $this->option('email');

        // Input password secara interaktif dan tersembunyi via $this->secret()
        $newPassword = $this->secret('Masukkan password baru untuk Super Admin (kosongkan untuk melewati)');

        if (!empty($newPassword)) {
            if (strlen($newPassword) < 8) {
                $this->error('Password harus minimal 8 karakter.');
                return 1;
            }
            $confirmPassword = $this->secret('Konfirmasi password baru');
            if ($newPassword !== $confirmPassword) {
                $this->error('Konfirmasi password tidak cocok.');
                return 1;
            }
        }

        if (!$newEmail && empty($newPassword)) {
            $this->error('Anda harus memberikan setidaknya --email atau password baru.');
            return 1;
        }

        // Validasi format email baru jika disediakan
        if ($newEmail) {
            $validator = Validator::make(['email' => $newEmail], [
                'email' => ['required', 'email:rfc,dns'],
            ]);
            if ($validator->fails()) {
                $this->error('Format email baru tidak valid.');
                return 1;
            }
        }

        $superAdmin = User::where('email', $currentEmail)
            ->where('role', 'super_admin')
            ->first();

        if (!$superAdmin) {
            $this->error("Akun Super Admin dengan email '{$currentEmail}' tidak ditemukan.");
            return 1;
        }

        // Check for email conflict
        if ($newEmail && $newEmail !== $superAdmin->email) {
            $exists = User::where('email', $newEmail)->where('id', '!=', $superAdmin->id)->exists();
            if ($exists) {
                $this->error("Email '{$newEmail}' sudah digunakan oleh akun lain.");
                return 1;
            }
            $superAdmin->email = $newEmail;
            $this->info("Email diperbarui menjadi: {$newEmail}");
        }

        if (!empty($newPassword)) {
            $superAdmin->password = Hash::make($newPassword);
            $this->info('Password berhasil diperbarui.');
        }

        // Pertahankan role super_admin. JANGAN ubah status (biarkan seperti semula).
        // Tidak membuat akun baru, tidak menghapus data pengguna.
        $superAdmin->role = 'super_admin';
        $superAdmin->save();

        $this->info('✅ Akun Super Admin berhasil diperbarui.');
        $this->table(
            ['Field', 'Value'],
            [
                ['Name', $superAdmin->name],
                ['Email', $superAdmin->email],
                ['Role', $superAdmin->role],
                ['Status', $superAdmin->status],
            ]
        );

        return 0;
    }
}

