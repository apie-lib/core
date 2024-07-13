<?php
namespace Apie\Core\Enums;

enum UploadedFileStatus: string {
    case CreatedLocally = 'created-locally';
    case FromRequest = 'from-request';
    case StoredInStorage = 'stored';
}