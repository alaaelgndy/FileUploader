## Media management package.

#### Why do you need this package?
- it can handle all of your needs to manage you application media.
- it provides an API to make life easy for the consumers (client side devs).
- it will help the client side devs to make an reusable component for the media uploads.
- if you are creating an back-end system and your whole APIs are using json, you still not have a need to use base64 for you media.

#### How it works?
- generate a temp path for the your uploaded file by using the exposed API for uploading media.
- upload the file in this temp path on the configured storage.
- return the base url of you storage and the temp path.
- so this file is exist in the temp path but till now nothing has used it (it's still temp file).
- but client side developers can preview it using the base url and the temp path.
- then you can relate this file to any model in you application.
- using this event (Elgndy\FileUploader\Events\UploadableModelHasCreated)

#### What is the temp path?
- the temp path is the place we use to hold the media in the early stage.
- it contains 4 parts.
1. the temp folder (you can configure it).
2. the related model.
3. the type of this media (you can custom your own types).
4. the file name. 

#### What is the real path?
- the real path is the final place for this file.
- it contains 4 parts.
1. the related model.
2. the related id.
3. the type of this media.
4. the filen name. 



#### Contract of the API.
- API for uploading
- URL /upload-media
- METHOD post.
- HEADER (content type: multipart/encrypted)
- BODY (model: string, mediaType: string, media: file)

### Usage
- configure your models namespace like (App\\)
- configure your temp path, the default is (temp/).
- update your models to impelement (Elgndy\FileUploader\Contracts\FileUploaderInterface).
- then configure the available media type for each model and the rules of the files extension.

```
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Elgndy\FileUploader\Contracts\FileUploaderInterface;

class User extends Model implements FileUploaderInterface
{
    public function getMediaTypesWithItsOwnValidationRules(): array
    {
        return [
            'images' => [
                'png',
                'jpg',
                'jpeg',
            ],
            'national_id' => [
                'pdf'
            ],
            'logos' => [
                'png'
            ] 
        ];
    }
}

```
