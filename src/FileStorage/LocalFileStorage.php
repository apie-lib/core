<?php
namespace Apie\Core\FileStorage;

use Apie\Core\Identifiers\KebabCaseSlug;
use Apie\Core\ValueObjects\Utils;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use WeakMap;

final class LocalFileStorage implements PsrAwareStorageInterface, UploadedFileAwareStorageInterface
{
    private string $path;

    /**
     * @var WeakMap<UploadedFileInterface, string> $mappedPaths
     */
    private WeakMap $mappedPaths;

    /**
     * @param array<string, string> $options
     */
    public function __construct(array $options)
    {
        $this->mappedPaths = new WeakMap();
        $this->path = rtrim(Utils::toString($options['path']), '\\/') . DIRECTORY_SEPARATOR;
    }

    public function createNewUpload(
        UploadedFileInterface $fileUpload,
        string $className = StoredFile::class
    ): StoredFile {
        $storagePath =
            uniqid(KebabCaseSlug::fromClass($className)->toNative(), true)
            . ltrim($this->normalizePath($fileUpload->getClientFilename()));
        $target = fopen($this->path . $storagePath, 'w+');
        if ($fileUpload instanceof StoredFile) {
            stream_copy_to_stream($fileUpload->getStream()->detach(), $target);
        } else {
            fputs($target, $fileUpload->getStream()->__toString());
        }
        fclose($target);

        return $className::createFromLocalFile(
            $this->path . $storagePath,
            $fileUpload->getClientFilename()
        )->markBeingStored($this, $storagePath);
    }

    public function getProxy(
        string $storagePath,
        string $className = StoredFile::class
    ): StoredFile {
        return $className::createFromStorage(
            $this,
            $storagePath
        );
    }

    public function loadFromStorage(
        string $storagePath,
        string $className = StoredFile::class
    ): StoredFile {
        $path = ltrim($this->normalizePath($storagePath), '\\/');
        $fullPath = $this->path . $path;
        return StoredFile::createFromLocalFile(
            $fullPath
        )->markBeingStored($this, $storagePath);
    }

    public function psrToPath(UploadedFileInterface $uploadedFile): string
    {
        return $this->mappedPaths[$uploadedFile];
    }

    private function normalizePath(string $path): string
    {
        $patterns =['~/{2,}~', '~/(\./)+~', '~([^/\.]+/(?R)*\.{2,}/)~', '~\.\./~'];
        $replacements = ['/', '/', '', ''];
        return preg_replace($patterns, $replacements, $path);
    }

    public function pathToPsr(string $path): UploadedFileInterface
    {
        $path = ltrim($this->normalizePath($path), '\\/');
        $fullPath = $this->path . $path;
        $factory = new Psr17Factory();
        $result = $factory->createUploadedFile(
            $factory->createStreamFromFile($fullPath),
            null,
            UPLOAD_ERR_OK,
            basename($fullPath),
            MimeTypes::getDefault()->guessMimeType($fullPath),
        );
        $this->mappedPaths[$result] = $path;
        return $result;
    }

    public function uploadedFileToPath(UploadedFile $uploadedFile): string
    {
        $fullPath = $uploadedFile->getPathname();
        if (!str_starts_with($fullPath, $this->path)) {
            throw new \LogicException('I do not know this uploaded file as full path is unknown, full path: ' . $fullPath . ' path given: ' .  $this->path);
        }
        return substr($fullPath, strlen($this->path));
    }

    public function pathToUploadedFile(string $path): UploadedFile
    {
        $path = ltrim($this->normalizePath($path), '\\/');
        $fullPath = $this->path . $path;
        return new UploadedFile(
            $fullPath,
            basename($fullPath),
            MimeTypes::getDefault()->guessMimeType($fullPath),
            null,
            true
        );
    }
}
