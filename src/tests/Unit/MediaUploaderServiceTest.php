<?php

namespace Elgndy\FileUploader\Tests\Unit;

use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Elgndy\FileUploader\Tests\Traits\FileFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Elgndy\FileUploader\Services\MediaUploaderService;
use Elgndy\FileUploader\Tests\Traits\InaccessibleMethodsInvoker;
use Elgndy\FileUploader\Tests\Models\ModelImplementsFileUploaderInterface;

class MediaUploaderServiceTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use FileFaker;
    use InaccessibleMethodsInvoker;

    private $mediaUploaderService;

    protected function setUp(): void
    {
        $this->mediaUploaderService = app()->make(MediaUploaderService::class);
        parent::setUp();
    }

    /** @test */
    public function it_validates_the_passed_model_existance()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $data = $this->generateRequiredData();

        $setModelObject = $this->invokeMethod(
            $this->mediaUploaderService,
            'setModelObject',
            [$data['model']]
        );

        $this->assertTrue($setModelObject instanceof $this->mediaUploaderService);
    }

    /** @test */
    public function it_throws_if_passed_model_is_not_exist_in_the_models_namespace()
    {
        $data = $this->generateRequiredData();

        $this->expectException(Exception::class);

        $this->invokeMethod(
            $this->mediaUploaderService,
            'setModelObject',
            [$data['model']]
        );
    }

    /** @test */
    public function it_forces_the_used_models_to_impelement_FileUploaderInterface()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $data = $this->generateRequiredData();

        $this->invokeMethod(
            $this->mediaUploaderService,
            'setModelObject',
            [$data['model']]
        );

        $isTheModelReadyForUse = $this->invokeMethod(
            $this->mediaUploaderService,
            'isThisModelReadyForUse'
        );

        $this->assertIsObject($isTheModelReadyForUse);
    }

    public function it_throws_if_the_used_model_does_not_impelement_FileUplodaerInterface()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');

        $data = $this->generateRequiredData('ModelNotImplementsFileUploaderInterface');

        $this->invokeMethod(
            $this->mediaUploaderService,
            'setModelObject',
            [$data['model']]
        );

        $this->expectException(Exception::class);

        $this->invokeMethod(
            $this->mediaUploaderService,
            'isThisModelReadyForUse'
        );
    }

    /** @test */
    public function it_checks_if_the_passed_media_type_is_available_for_this_model()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');

        $data = $this->generateRequiredData();

        $this->invokeMethod(
            $this->mediaUploaderService,
            'setModelObject',
            [$data['model']]
        );

        $isPassedMediaTypeAcceptedForThisModel = $this->invokeMethod(
            $this->mediaUploaderService,
            'isPassedMediaTypeAcceptedForThisModel',
            [$data['mediaType']]
        );

        $this->assertTrue($isPassedMediaTypeAcceptedForThisModel instanceof $this->mediaUploaderService);
    }

    /** @test */
    public function it_throws_if_the_passed_media_type_is_not_available_for_this_model()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');

        $data = $this->generateRequiredData(null, 'profile_pictures');

        $this->invokeMethod(
            $this->mediaUploaderService,
            'setModelObject',
            [$data['model']]
        );

        $this->expectException(Exception::class);

        $this->invokeMethod(
            $this->mediaUploaderService,
            'isPassedMediaTypeAcceptedForThisModel',
            [$data['mediaType']]
        );
    }

    /** @test */
    public function it_checks_the_extension_availability_for_specific_media_type()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');

        $data = $this->generateRequiredData();

        $this->invokeMethod(
            $this->mediaUploaderService,
            'setModelObject',
            [$data['model']]
        );

        $extensionCheck = $this->invokeMethod(
            $this->mediaUploaderService,
            'isMediaExtensionValidForThisMediaType',
            [$data['media'], $data['mediaType']]
        );

        $this->assertTrue($extensionCheck instanceof $this->mediaUploaderService);
    }

    /** @test */
    public function it_throws_if_the_extension_is_not_valid_for_the_selected_media_type()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');

        $data = $this->generateRequiredData(null, null, 'pdf');

        $this->invokeMethod(
            $this->mediaUploaderService,
            'setModelObject',
            [$data['model']]
        );

        $this->expectException(Exception::class);

        $this->invokeMethod(
            $this->mediaUploaderService,
            'isMediaExtensionValidForThisMediaType',
            [$data['media'], $data['mediaType']]
        );
    }

    /** @test */
    public function it_generates_right_temp_path()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $data = $this->generateRequiredData();

        $validated = $this->mediaUploaderService->validatePassedDataForTempMedia($data);
        $generatedPath = $this->invokeMethod(
            $validated,
            'generateTempMediaPath',
            [config('elgndy_media.temp_path')]
        );
        $tableName = (new ModelImplementsFileUploaderInterface())->getTable();
        $mediaType = $data['mediaType'];

        $tempPathShouldBe = config('elgndy_media.temp_path') . $tableName . '/' . $mediaType;

        $this->assertEquals($generatedPath, $tempPathShouldBe);
    }

    /** @test */
    public function it_uploads_the_file_on_the_storage()
    {
        Config::set('elgndy_media.models_namespace', 'Elgndy\\FileUploader\\Tests\\Models\\');
        $data = $this->generateRequiredData();

        $validated = $this->mediaUploaderService->validatePassedDataForTempMedia($data);

        $uploaded = $validated->upload(config('elgndy_media.temp_path'));

        $this->assertTrue(Storage::exists($uploaded));

        Storage::delete($uploaded);
    }


    private function generateRequiredData(?string $model = null, ?string $mediaType = null, ?string $extension = null): array
    {
        return [
            'model' => $model ?? 'ModelImplementsFileUploaderInterface',
            'media' => $extension ? $this->fileFaker($extension) : $this->fileFaker(),
            'mediaType' => $mediaType ?? 'images',
        ];
    }
}
