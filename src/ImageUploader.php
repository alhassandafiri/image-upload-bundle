<?php

namespace Imho\ImageUploadBundle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Stores and removes uploaded image files under a configured base directory.
 *
 * Files are saved in a subfolder chosen by the caller (for example "avatars" or "covers") with a random name.
 */
class ImageUploader
{
    public function __construct(private readonly string $uploadDir)
    {
    }

    /**
     * Moves an uploaded file into <uploadDir>/<folder> under a random name and returns that name.
     *
     * The client's original filename is never used, and the extension is guessed from the file's content.
     *
     * @param string $folder subfolder name; letters, digits, hyphens and underscores only
     *
     * @throws \InvalidArgumentException if the folder name is unsafe
     */
    public function upload(UploadedFile $file, string $folder): string
    {
        $this->assertSafeFolder($folder);

        $filename = bin2hex(random_bytes(16)) . '.' . $file->guessExtension();
        $file->move($this->uploadDir . '/' . $folder, $filename);

        return $filename;
    }

    /**
     * Deletes a previously stored file. Does nothing if the filename is empty.
     * Only the file's base name is used, so a tampered value cannot reach outside the folder.
     */
    public function remove(?string $filename, string $folder): void
    {
        if ($filename) {
            $this->assertSafeFolder($folder);
            (new Filesystem())->remove($this->uploadDir . '/' . $folder . '/' . basename($filename));
        }
    }

    /**
     * Rejects folder names that could escape the upload directory (for example "../etc").
     */
    private function assertSafeFolder(string $folder): void
    {
        if (!preg_match('/^[a-z0-9_-]+$/i', $folder)) {
            throw new \InvalidArgumentException(sprintf('Invalid upload folder "%s".', $folder));
        }
    }
}