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

### Installation
```
composer require alaaelgndy/file-uploader
```

### Usage
- configure your models namespace like (App\\)
- configure your temp path, the default is (temp/).
- update your models to implement (Elgndy\FileUploader\Contracts\FileUploaderInterface).
- use Uploadable trait in your uploadable models.
- implement this function getMediaTypesWithItsOwnValidationRules()
    - the keys are the mediaType
    - the values are the available extensions for this specefic mediaType.
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Elgndy\FileUploader\Contracts\FileUploaderInterface;
use Elgndy\FileUploader\Traits\Uploadable;

class User extends Model implements FileUploaderInterface
{
    use Uploadable;

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
- after these points now you can let the client side call our API anytime to create temp files.

#### how to relate the temp media to specific model record.
- first write your own business logic to create the record and after creating it, fire this event
(UploadableMediaHasCreated) and pass your created record which is type of Model, and the temp path. 

#### what if the related model has deleted.
- by using this event it will remove the relation from database and remove the folder from the FS.
(UploadableMediaHasDeleted) and pass your deleted record which is type of Model. that's it :).


### other helpful functions.
- the Uploadable trait has these functions.
    - media() returns collection of related media.
    - getMedia(...$types) returns collection of related media with specific types.
    - mediaCount(...$types) returns integer of the count rely on your passed types.
    - custom image attribute will get the first media always (take care of this point).


### the FS structure
- users/
    - userId/
        - images/
        - logos/
        - national_id/

## ToDo
- [ ] enhance the readme file.
- [ ] increase the unit test coverage.
- [ ] adding resizing files feature.
- [ ] add more example of use. 
- [ ] create custom exception.
- [ ] create command to clear the useless data in temp folder.
- [ ] add versioning and changelog file.
- [x] test it on laravel 5.6 or greater versions (using CI).
- [x] configure the upload route.
- [x] the ability of assign middlewares to the upload route.
