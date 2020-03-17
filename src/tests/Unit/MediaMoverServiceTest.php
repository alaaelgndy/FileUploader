<?php

namespace Elgndy\FileUploader\Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Elgndy\FileUploader\Tests\Traits\FileFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Elgndy\FileUploader\Services\MediaMoverService;
use Elgndy\FileUploader\Services\MediaUploaderService;
use Elgndy\FileUploader\Tests\Traits\InaccessibleMethodsInvoker;
use Exception;

class MediaMoverServiceTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use FileFaker;
    use InaccessibleMethodsInvoker;

    private $mediaMoverService;
    
    private $mediaUploaderService;

    protected function setUp(): void
    {
        $this->mediaMoverService = app()->make(MediaMoverService::class);
        $this->mediaUploaderService = app()->make(MediaUploaderService::class);
        parent::setUp();
    }

    /** @test */
    public function it_checks_temp_media_existance()
    {
        $generatedTempMedia = $this->generateTempMedia();

        $check = $this->invokeMethod(
            $this->mediaMoverService,
            'checkTempMediaExistence',
            [$generatedTempMedia]
        );

        $this->assertTrue($check instanceof $this->mediaMoverService);
        Storage::delete($generatedTempMedia);
    }

    /** @test */
    public function it_throws_if_the_passed_media_is_not_exist()
    {
        $this->expectException(Exception::class);

        $this->invokeMethod(
            $this->mediaMoverService,
            'checkTempMediaExistence',
            [$this->faker->name]
        );
    }

    private function generateTempMedia()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');

        $validated = $this->mediaUploaderService->validatePassedDataForTempMedia([
            'model' => 'ModelImplementsFileUploaderInterface',
            'media' => $this->fileFaker(),
            'mediaType' => 'images',
        ]);

        return $validated->upload(config('elgndy_media.temp_path'));
    }
}