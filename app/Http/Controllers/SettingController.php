<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    /**
     * Display user profile settings.
     */
    public function profile()
    {
        $user = auth()->user();
        return view('settings.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],

            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->name = $request->name;


        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return redirect()->route('settings.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Display password change form.
     */
    public function password()
    {
        return view('settings.password');
    }

    /**
     * Update user password.
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('settings.password')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Display application settings.
     */
    public function application()
    {
        // Get application settings from database or config
        $settings = [
            'app_name' => config('app.name', 'SLVMS'),
            'app_description' => config('app.description', 'Sistem Laporan Piutang dan Manajemen Debitur'),
            'app_logo' => config('app.logo'),
            'app_favicon' => config('app.favicon'),
            'currency' => config('app.currency', 'IDR'),
            'date_format' => config('app.date_format', 'd-m-Y'),
            'time_format' => config('app.time_format', 'H:i'),
        ];

        return view('settings.application', compact('settings'));
    }

    /**
     * Update application settings.
     */
    public function updateApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'required|string|max:255',
            'app_description' => 'nullable|string|max:500',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'app_favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
            'currency' => 'required|string|max:10',
            'date_format' => 'required|string|max:20',
            'time_format' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update application settings in config or database
        // This is a simplified example. In a real application, you might want to store these in a database table
        $settings = [
            'app.name' => $request->app_name,
            'app.description' => $request->app_description,
            'app.currency' => $request->currency,
            'app.date_format' => $request->date_format,
            'app.time_format' => $request->time_format,
        ];

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $logoPath = $request->file('app_logo')->store('app', 'public');
            $settings['app.logo'] = $logoPath;
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            $faviconPath = $request->file('app_favicon')->store('app', 'public');
            $settings['app.favicon'] = $faviconPath;
        }

        // Save settings to config file or database
        // This is a simplified example. In a real application, you might want to use a config writer or database
        foreach ($settings as $key => $value) {
            config([$key => $value]);
        }

        return redirect()->route('settings.application')
            ->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }

    /**
     * Display backup settings.
     */
    public function backup()
    {
        // Get list of backup files
        $disk = Storage::disk('local');
        $files = $disk->files('backups');

        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'filename' => basename($file),
                'path' => $file,
                'size' => $this->formatFileSize($disk->size($file)),
                'last_modified' => date('Y-m-d H:i:s', $disk->lastModified($file)),
            ];
        }

        return view('settings.backup', compact('backups'));
    }

    /**
     * Create a new backup.
     */
    public function createBackup(Request $request)
    {
        // Check if mysqldump is available
        $mysqldumpPath = shell_exec('where mysqldump');
        if (empty($mysqldumpPath)) {
            return redirect()->route('settings.backup')
                ->with('error', 'mysqldump command not found. Please make sure it is installed and in your system\'s PATH.');
        }

        $filename = 'backup_' . date('Y_m_d_His') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Create backup directory if it doesn't exist
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            escapeshellarg(env('DB_USERNAME')),
            escapeshellarg(env('DB_PASSWORD')),
            escapeshellarg(env('DB_HOST')),
            escapeshellarg(env('DB_DATABASE')),
            escapeshellarg($path)
        );

        $returnVar = 0;
        $output = [];
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return redirect()->route('settings.backup')
                ->with('error', 'Failed to create backup. Error: ' . implode("\n", $output));
        }

        if (!file_exists($path) || filesize($path) === 0) {
            return redirect()->route('settings.backup')
                ->with('error', 'Failed to create backup file or the backup file is empty.');
        }

        return redirect()->route('settings.backup')
            ->with('success', 'Backup berhasil dibuat.');
    }

    /**
     * Download a backup file.
     */
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path);
    }

    /**
     * Delete a backup file.
     */
    public function deleteBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (file_exists($path)) {
            unlink($path);
        }

        return redirect()->route('settings.backup')
            ->with('success', 'Backup berhasil dihapus.');
    }

    /**
     * Format file size.
     */
    private function formatFileSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . ' ' . $units[$i];
    }
}
