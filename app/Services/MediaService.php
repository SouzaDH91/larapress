<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

class MediaService
{
    protected array $disallowedExtensions = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
        'sh', 'bash', 'bat', 'cmd', 'exe', 'msi', 'bin',
        'cgi', 'pl', 'py', 'js', 'html', 'htm', 'htaccess'
    ];

    protected array $allowedMimes = [
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
        'application/pdf', 'text/plain',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'audio/mpeg', 'video/mp4'
    ];

    /**
     * Upload and safely store a file.
     */
    public function upload(UploadedFile $file, ?User $user = null, ?string $title = null, ?string $altText = null): Media
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (in_array($extension, $this->disallowedExtensions, true)) {
            throw new \InvalidArgumentException("Dangerous file extension detected: .{$extension}");
        }

        // Strict real MIME check via magic bytes
        $realMime = $file->getMimeType() ?? 'application/octet-stream';
        if (!in_array($realMime, $this->allowedMimes, true)) {
            throw new \InvalidArgumentException("MIME type not allowed: {$realMime}");
        }

        $userId = $user?->id ?? auth()->id();
        if (!$userId) {
            $defaultAdmin = User::first();
            $userId = $defaultAdmin?->id;
        }

        $uuid = (string) Str::uuid();
        $dateFolder = now()->format('Y/m');
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeOriginalName = Str::slug($originalFilename);

        $isImage = str_starts_with($realMime, 'image/') && $realMime !== 'image/svg+xml';
        $finalFilename = "{$uuid}-{$safeOriginalName}";

        $metadata = [];
        $disk = 'public';

        if ($isImage) {
            $finalFilename .= '.webp';
            $relativeDirectory = "uploads/{$dateFolder}";
            $fullStoragePath = Storage::disk($disk)->path($relativeDirectory);

            if (!is_dir($fullStoragePath)) {
                mkdir($fullStoragePath, 0755, true);
            }

            $manager = new ImageManager(new GdDriver());
            $image = $manager->decodePath($file->getRealPath());

            $width = $image->width();
            $height = $image->height();
            $metadata['dimensions'] = ['width' => $width, 'height' => $height];

            // Save WebP original
            $targetPath = "{$relativeDirectory}/{$finalFilename}";
            $encodedWebp = $image->encode(new WebpEncoder(85));
            $encodedWebp->save(Storage::disk($disk)->path($targetPath));

            // Generate thumbnails (Thumbnail: 150x150, Medium: 300x300, Large: 1024x1024)
            $sizes = [
                'thumb' => [150, 150],
                'medium' => [300, 300],
                'large' => [1024, 1024],
            ];

            $generatedThumbs = [];
            foreach ($sizes as $sizeName => [$w, $h]) {
                if ($width > $w || $height > $h) {
                    $thumbName = "{$uuid}-{$sizeName}.webp";
                    $thumbRelPath = "{$relativeDirectory}/{$thumbName}";
                    $thumbImg = clone $image;
                    $thumbImg->cover($w, $h);
                    $thumbEncoded = $thumbImg->encode(new WebpEncoder(80));
                    $thumbEncoded->save(Storage::disk($disk)->path($thumbRelPath));
                    $generatedThumbs[$sizeName] = $thumbRelPath;
                }
            }
            $metadata['sizes'] = $generatedThumbs;
            $fileSize = Storage::disk($disk)->size($targetPath);
            $mimeType = 'image/webp';
            $storageRelativePath = $targetPath;
        } else {
            $finalFilename .= ".{$extension}";
            $storageRelativePath = $file->storeAs("uploads/{$dateFolder}", $finalFilename, $disk);
            $fileSize = $file->getSize();
            $mimeType = $realMime;
        }

        return Media::create([
            'id' => $uuid,
            'user_id' => $userId,
            'filename' => $finalFilename,
            'disk' => $disk,
            'path' => $storageRelativePath,
            'mime_type' => $mimeType,
            'size' => $fileSize,
            'title' => $title ?? $originalFilename,
            'alt_text' => $altText ?? $originalFilename,
            'caption' => null,
            'metadata_json' => $metadata,
        ]);
    }
}
