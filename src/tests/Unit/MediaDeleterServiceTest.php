<?php

namespace Elgndy\FileUploader\Tests\Unit;

use Exception;
use Tests\TestCase;
use Elgndy\FileUploader\Models\Media;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\WithFaker;
use Elgndy\FileUploader\Tests\Traits\FileFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Elgndy\FileUploader\Services\MediaMoverService;
use Elgndy\FileUploader\Services\MediaDeleterService;
use Elgndy\FileUploader\Services\MediaUploaderService;
use Elgndy\FileUploader\Tests\Traits\InaccessibleMethodsInvoker;
use Elgndy\FileUploader\Tests\Models\ModelImplementsFileUploaderInterface;
use Elgndy\FileUploader\Tests\Models\ModelNotImplementsFileUploaderInterface;

class MediaDeleterServiceTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use FileFaker;
    use InaccessibleMethodsInvoker;

    private $mediaMoverService;
    
    private $mediaUploaderService;

    private $mediaDeleterService;

    protected function setUp(): void
    {
        $this->mediaUploaderService = app()->make(MediaUploaderService::class);
        $this->mediaMoverService = app()->make(MediaMoverService::class);
        $this->mediaDeleterService = app()->make(MediaDeleterService::class);
        parent::setUp();
        $this->createTableInDB();
    }

    /** @test */
    public function it_validates_the_passed_model()
    {
        $validated = $this->mediaDeleterService->validateBeforeDelete(new ModelImplementsFileUploaderInterface);
        
        $this->assertTrue($validated instanceof $this->mediaDeleterService);
    }

    /** @test */
    public function it_throws_if_passed_model_does_not_impeletemnts_FileUploaderInterface()
    {
        $this->expectException(Exception::class);
        $this->mediaDeleterService->validateBeforeDelete(new ModelNotImplementsFileUploaderInterface);
    }

    /** @test */
    public function it_remove_the_folder_of_media_for_specific_row()
    {
        $model = ModelImplementsFileUploaderInterface::create([]);
        $uploadedAndMoved = $this->generateUploadedMedia($model);

        $this->assertTrue(Storage::exists($uploadedAndMoved));

        $validated = $this->mediaDeleterService->validateBeforeDelete($model);

        $this->assertTrue($validated->removeFolderFromFS());

        $this->assertFalse(Storage::exists($uploadedAndMoved));
    }

    /** @test */
    public function it_remove_the_rows_of_media_in_database()
    {
        $model = ModelImplementsFileUploaderInterface::create([]);
        $this->generateUploadedMedia($model);

        $validated = $this->mediaDeleterService->validateBeforeDelete($model);

        $beforeDelete = Media::count();
        $validated->deleteFromDb();
        $afterDelete = Media::count();
        $this->assertTrue($beforeDelete !== $afterDelete);
    }   

    private function generateUploadedMedia($model)
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        
        $data = [
            'tempMedia' => $this->generateTempMedia(),
            'model' => $model,
        ];

        $validated = $this->mediaMoverService->validateBeforeMove($data);
        $validated->saveInDb();
        return $validated->move();
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

    private function createTableInDB()
    {
        tap(
            $this->app['db']->connection()->getSchemaBuilder(),
            function ($schema) {
                if (!$schema->hasTable('elgndy_mediaa')) {
                    $schema->create(
                        'elgndy_mediaa',
                        function (Blueprint $table) {
                            $table->increments('id');
                            $table->timestamps();
                        }
                    );
                }
            }
        );
    }
}