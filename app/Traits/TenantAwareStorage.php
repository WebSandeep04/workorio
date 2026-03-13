<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait TenantAwareStorage
{
    /**
     * Get a tenant-isolated storage path.
     *
     * @param string $path The base path (e.g., 'documents', 'profile_pictures')
     * @param int|null $tenantId The tenant ID (defaults to current session tenant)
     * @return string
     */
    protected function getTenantPath(string $path, $tenantId = null): string
    {
        $tenantId = $tenantId ?? session('tenant_id', 1);
        return "tenants/{$tenantId}/" . ltrim($path, '/');
    }

    /**
     * Store a file with tenant isolation and unique filename.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $basePath
     * @param string $disk
     * @return string|false
     */
    protected function storeTenantFile($file, string $basePath, string $disk = 'public')
    {
        $tenantId = session('tenant_id', 1);
        $originalName = $file->getClientOriginalName();
        
        // Generate a unique filename: timestamp + random + sanitized original name
        $sanitizedName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $filename = time() . '_' . Str::random(6) . '_' . $sanitizedName . '.' . $extension;

        $targetPath = $this->getTenantPath($basePath, $tenantId);

        return $file->storeAs($targetPath, $filename, $disk);
    }

    /**
     * Delete a file from tenant storage.
     *
     * @param string|null $path
     * @param string $disk
     * @return bool
     */
    protected function deleteTenantFile(?string $path, string $disk = 'public'): bool
    {
        if (!$path) return false;

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
