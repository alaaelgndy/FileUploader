<?php

namespace Elgndy\FileUploader\Tests\Unit;

use Tests\TestCase;
use Elgndy\FileUploader\Models\Media;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Elgndy\FileUploader\FileUploaderManager;
use Illuminate\Foundation\Testing\WithFaker;
use Elgndy\FileUploader\Tests\Traits\FileFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Elgndy\FileUploader\Tests\Traits\CreateTableInDb;
use Elgndy\FileUploader\Events\UploadableModelHasCreated;
use Elgndy\FileUploader\Events\UploadableModelHasDeleted;
use Elgndy\FileUploader\Tests\Models\ModelImplementsFileUploaderInterface;
use Elgndy\FileUploader\Tests\Traits\RemoveCreatedFiles;

class FileUploaderManagerTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use FileFaker;
    use CreateTableInDb;
    use RemoveCreatedFiles;

    private $fileUploaderManager;

    protected function setUp(): void
    {
        $this->fileUploaderManager = app()->make(FileUploaderManager::class);
        parent::setUp();
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $this->createTableInDB('elgndy_mediaa');
    }

    /**
     * @test 
     */
    public function it_can_upload_file_in_the_temp_storage()
    {
        $data = $this->prepareDataForUploading();

        $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);

        $this->assertArrayHasKey('filePath', $returnedArray);
        $this->assertArrayHasKey('baseUrl', $returnedArray);
        $this->assertTrue(Storage::exists($returnedArray['filePath']));
    }

    /**
     * @test 
     */
    public function it_uploads_the_temp_file_in_a_righ_path()
    {
        $data = $this->prepareDataForUploading();

        $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);

        $tableName = (new ModelImplementsFileUploaderInterface())->getTable();
        $this->assertStringStartsWith("temp/{$tableName}/images/", $returnedArray['filePath']);
    }

    /**
     * @test 
     */
    public function it_can_store_the_temp_file_in_the_real_path()
    {
        $data = $this->prepareDataForUploading();

        $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);

        $newModelRecord = ModelImplementsFileUploaderInterface::create([]);

        $returnedAfterMove = $this->fileUploaderManager->storeTempMediaInRealPath(
            $newModelRecord,
            $returnedArray['filePath']
        );

        $this->assertInstanceOf(Media::class, $returnedAfterMove);
        $this->assertEquals(1, ModelImplementsFileUploaderInterface::count());
        $this->assertTrue(Storage::exists($returnedAfterMove->file_path));
    }

    /**
     * @test 
     */
    public function it_can_store_the_file_using_the_event_listener()
    {
        $data = $this->prepareDataForUploading();

        $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);

        $newModelRecord = ModelImplementsFileUploaderInterface::create([]);

        event(new UploadableModelHasCreated($newModelRecord, $returnedArray['filePath']));
        $this->assertEquals(1, ModelImplementsFileUploaderInterface::count());
    }

    /**
     * @test 
     */
    public function it_can_remove_media_folder_when_the_related_model_has_been_removed_using_event()
    {
        $newModelRecord = ModelImplementsFileUploaderInterface::create([]);
        $this->createMediaFactory(5, $newModelRecord);

        $countBeforeDelete = $newModelRecord->mediaCount();
        event(new UploadableModelHasDeleted($newModelRecord));
        $countAfterDelete = ModelImplementsFileUploaderInterface::find($newModelRecord->id)->mediaCount();

        $newModelRecord->delete();

        $this->assertFalse($countAfterDelete == $countBeforeDelete);
    }

    private function createMediaFactory($count, Model $newModelRecord)
    {
        for ($i = 0; $i < $count; $i++) {
            $data = $this->prepareDataForUploading();
            $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);
            event(new UploadableModelHasCreated($newModelRecord, $returnedArray['filePath']));
        }
    }


    private function prepareDataForUploading(): array
    {
        return [
            'model' => 'ModelImplementsFileUploaderInterface',
            'mediaType' => 'images',
            'media' => $this->fileFaker()
        ];
    }
}
