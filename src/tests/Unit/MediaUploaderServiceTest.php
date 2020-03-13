<?php

namespace Elgndy\FileUploader\Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Foundation\Testing\WithFaker;
use Elgndy\FileUploader\Tests\Traits\FileFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Elgndy\FileUploader\Services\MediaUploaderService;
use Elgndy\FileUploader\Tests\Traits\InaccessibleMethodsInvoker;
use Elgndy\FileUploader\Tests\Models\ModelImplementsFileUploaderInterface;
use Exception;

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

        $this->assertIsObject($setModelObject);
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

    private function generateRequiredData(): array
    {
        return [
            'model' => 'ModelImplementsFileUploaderInterface',
            'media' => $this->fileFaker(),
            'mediaType' => 'images',
        ];
    }
}
