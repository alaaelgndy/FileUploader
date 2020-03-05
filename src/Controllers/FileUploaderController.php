<?php

namespace Elgndy\FileUploader\Controllers;

use App\Http\Controllers\Controller;
use Elgndy\FileUploader\FileUploaderManager;
use Elgndy\FileUploader\Requests\MoveTempFileRequest;
use Elgndy\FileUploader\Requests\CreateTempFileRequest;

class FileUploaderController extends Controller
{

    private $fileUploaderManager;

    public function __construct(FileUploaderManager $fum)
    {
        $this->fileUploaderManager = $fum;
    }

    public function store(CreateTempFileRequest $request)
    {
        $uploded = $this->fileUploaderManager->uploadTheTempFile($request->validated());

        return response()->json($uploded);
    }

    public function move(MoveTempFileRequest $request)
    {
        $model = config('elgndy_media.models_namespace') . $request->model;
        $model = $model::find($request->id);

        $stored = $this->fileUploaderManager->storeTempMediaInRealPath($model, $request->tempPath)
            ->toArray();

        return response()->json($stored);
    }
}
