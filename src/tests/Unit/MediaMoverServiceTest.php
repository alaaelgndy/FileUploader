<?php

namespace Elgndy\FileUploader\Tests\Unit;

use Exception;
use Tests\TestCase;
use Elgndy\FileUploader\Models\Media;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Elgndy\FileUploader\Tests\Traits\FileFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Elgndy\FileUploader\Services\MediaMoverService;
use Elgndy\FileUploader\Tests\Traits\CreateTableInDb;
use Elgndy\FileUploader\Services\MediaUploaderService;
use Elgndy\FileUploader\Tests\Traits\RemoveCreatedFiles;
use Elgndy\FileUploader\Tests\Traits\InaccessibleMethodsInvoker;
use Elgndy\FileUploader\Tests\Models\ModelImplementsFileUploaderInterface;

class MediaMoverServiceTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use FileFaker;
    use InaccessibleMethodsInvoker;
    use CreateTableInDb;
    use RemoveCreatedFiles;

    private $mediaMoverService;
    
    private $mediaUploaderService;

    protected function setUp(): void
    {
        $this->mediaMoverService = app()->make(MediaMoverService::class);
        $this->mediaUploaderService = app()->make(MediaUploaderService::class);
        parent::setUp();
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $this->createTableInDB('elgndy_mediaa');
    }

    /** @test */
    public function it_moves_the_temp_file_to_the_real_path()
    {
        $generatedData = $this->generateRequiredData();
        $validated = $this->mediaMoverService->validateBeforeMove($generatedData);

        $movedTo = $validated->move();

        $this->assertTrue(Storage::exists($movedTo));
    }

    /** @test */
    public function it_saves_the_media_in_database()
    {
        $generatedData = $this->generateRequiredData();
        $validated = $this->mediaMoverService->validateBeforeMove($generatedData);

        $saved = $validated->saveInDb();

        $this->assertEquals(Media::count(), 1);
        $this->assertEquals($saved->model_type, get_class($generatedData['model']));
        $this->assertEquals($saved->model_id, $generatedData['model']->id);
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

    /** @test */
    public function it_extracts_media_type_from_temp_path()
    {
        $generatedData = $this->generateRequiredData();
        $validated = $this->mediaMoverService->validateBeforeMove($generatedData);

        $mediaType = $this->invokeMethod(
            $validated,
            'getMediaTypeFromTempPath',
            []
        );

        $this->assertEquals($mediaType, explode('/', $generatedData['tempMedia'])[2]);
    }

    /** @test */
    public function it_generates_full_real_path()
    {
        $generatedData = $this->generateRequiredData();
        $validated = $this->mediaMoverService->validateBeforeMove($generatedData);

        $generatedFullRealPath = $this->invokeMethod(
            $validated,
            'generateTheFullRealPath',
            []
        );

        $tempPathInArray = explode('/', $generatedData['tempMedia']);

        $expectedRealPath = $generatedData['model']->getTable() . '/';
        $expectedRealPath .= $generatedData['model']->id . '/';
        $expectedRealPath .=   $tempPathInArray[2] . '/';
        $expectedRealPath .= end($tempPathInArray);

        $this->assertEquals($generatedFullRealPath, $expectedRealPath);
    }

    /** @test */
    public function it_can_extract_media_name_from_temp_path()
    {
        $generatedData = $this->generateRequiredData();
        $validated = $this->mediaMoverService->validateBeforeMove($generatedData);

        $mediaName = $this->invokeMethod(
            $validated,
            'getMediaNameFromTempPath',
            []
        );
        $expectedMediaName = explode('/', $generatedData['tempMedia']);

        $this->assertEquals(end($expectedMediaName), $mediaName);
    }

    private function generateRequiredData()
    {
        return [
            'tempMedia' => $this->generateTempMedia(),
            'model' => ModelImplementsFileUploaderInterface::create([]),
        ];
    }

    private function generateTempMedia()
    {
        $validated = $this->mediaUploaderService->validatePassedDataForTempMedia([
            'model' => 'ModelImplementsFileUploaderInterface',
            'media' => $this->fileFaker(),
            'mediaType' => 'images',
        ]);

        return $validated->upload(config('elgndy_media.temp_path'));
    }
}