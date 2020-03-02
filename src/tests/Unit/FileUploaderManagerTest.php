<?php

namespace Elgndy\FileUploader\Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Elgndy\FileUploader\Models\Media;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Elgndy\FileUploader\FileUploaderManager;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Elgndy\FileUploader\Events\UploadableModelHasCreated;
use Elgndy\FileUploader\Events\UploadableModelHasDeleted;
use Elgndy\FileUploader\Tests\Models\ModelImpelementsFileUploaderInterface;

class FileUploaderManagerTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    private $fileUploaderManager;

    protected function setUp(): void
    {
        $this->fileUploaderManager = app()->make(FileUploaderManager::class);
        parent::setUp();
        $this->createTableInDB();
    }

    /**
     * @test 
     */
    public function it_can_upload_file_in_the_temp_storage()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $data = $this->prepareDataForUploading();

        $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);

        $this->assertArrayHasKey('filePath', $returnedArray);
        $this->assertArrayHasKey('baseUrl', $returnedArray);
    }

    /**
     * @test 
     */
    public function it_uploads_the_temp_file_in_a_righ_path()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $data = $this->prepareDataForUploading();

        $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);

        $tableName = (new ModelImpelementsFileUploaderInterface())->getTable();
        $this->assertStringStartsWith("temp/{$tableName}/images/", $returnedArray['filePath']);
    }

    /**
     * @test 
     */
    public function it_can_store_the_temp_file_in_the_real_path()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $data = $this->prepareDataForUploading();

        $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);

        $newModelRecord = ModelImpelementsFileUploaderInterface::create([]);

        $returnedAfterMove = $this->fileUploaderManager->storeTempMediaInRealPath(
            $newModelRecord,
            $returnedArray['filePath']
        );

        $this->assertInstanceOf(Media::class, $returnedAfterMove);
        $this->assertEquals(1, ModelImpelementsFileUploaderInterface::count());
        $this->assertTrue(Storage::exists($returnedAfterMove->file_path));
    }

    /**
     * @test 
     */
    public function it_can_store_the_file_using_the_event_listener()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $data = $this->prepareDataForUploading();

        $returnedArray = $this->fileUploaderManager->uploadTheTempFile($data);

        $newModelRecord = ModelImpelementsFileUploaderInterface::create([]);

        event(new UploadableModelHasCreated($newModelRecord, $returnedArray['filePath']));
        $this->assertEquals(1, ModelImpelementsFileUploaderInterface::count());
    }

    /**
     * @test 
     */
    public function it_can_remove_media_folder_when_the_related_model_has_been_removed_using_event()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $newModelRecord = ModelImpelementsFileUploaderInterface::create([]);
        $this->createMediaFactory(5, $newModelRecord);

        $countBeforeDelete = $newModelRecord->mediaCount();
        event(new UploadableModelHasDeleted($newModelRecord));
        $countAfterDelete = ModelImpelementsFileUploaderInterface::find($newModelRecord->id)->mediaCount();

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
            'model' => 'ModelImpelementsFileUploaderInterface',
            'mediaType' => 'images',
            'media' => $this->fileFaker()
        ];
    }

    private function fileFaker()
    {
        return UploadedFile::fake()->image(md5($this->faker->name) . '.png');
    }

    private function createTableInDB()
    {

        tap(
            $this->app['db']->connection()->getSchemaBuilder(), function ($schema) {
                $schema->create(
                    'elgndy_mediaa', function (Blueprint $table) {
                        $table->increments('id');
                        $table->timestamps();
                    }
                );
            }
        );
    }
}
