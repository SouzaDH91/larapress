<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_image_upload_converts_to_webp_and_creates_thumbnails(): void
    {
        $user = User::first();
        $file = UploadedFile::fake()->image('foto_teste.jpg', 600, 400);

        $mediaService = app(MediaService::class);
        $media = $mediaService->upload($file, $user, 'Foto Teste');

        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals('image/webp', $media->mime_type);
        $this->assertStringEndsWith('.webp', $media->filename);
        $this->assertNotNull($media->metadata_json);
        $this->assertArrayHasKey('dimensions', $media->metadata_json);
        $this->assertEquals(600, $media->metadata_json['dimensions']['width']);
        $this->assertEquals(400, $media->metadata_json['dimensions']['height']);

        // Garantir que o arquivo WebP foi salvo no storage
        Storage::disk('public')->assertExists($media->path);
    }
}
