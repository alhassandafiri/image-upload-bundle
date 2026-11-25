<?php

namespace Imho\ImageUploadBundle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    public function __construct(private readonly string $uploadDir)
    {
    }

    /** Stores the file under <uploadDir>/<folder> with a random name and returns that name. */
    public function upload(UploadedFile $file, string $folder): string
    {
        $this->assertSafeFolder($folder);

        $filename = bin2hex(random_bytes(16)) . '.' . $file->guessExtension();
        $file->move($this->uploadDir . '/' . $folder, $filename);

        return $filename;
    }

    public function remove(?string $filename, string $folder): void
    {
        if ($filename) {
            $this->assertSafeFolder($folder);
            (new Filesystem())->remove($this->uploadDir . '/' . $folder . '/' . basename($filename));
        }
    }

    private function assertSafeFolder(string $folder): void
    {
        if (!preg_match('/^[a-z0-9_-]+$/i', $folder)) {
            throw new \InvalidArgumentException(sprintf('Invalid upload folder "%s".', $folder));
        }
    }
}