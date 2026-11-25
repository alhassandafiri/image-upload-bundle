## Installation

1. Add the repository to your application's `composer.json`:

   ```json
   "repositories": [
       { "type": "vcs", "url": "https://github.com/alhassandafiri/image-upload-bundle" }
   ]
   ```

2. Install the bundle:

   ```bash
   composer require imho/image-upload-bundle:dev-master
   ```

3. If it isn't already listed, register it in `config/bundles.php`:

   ```php
   Imho\ImageUploadBundle\ImageUploadBundle::class => ['all' => true],
   ```

## Configuration

Optional. Create `config/packages/image_upload.yaml`:

```yaml
image_upload:
    upload_dir: '%kernel.project_dir%/public/uploads'
    max_size: '2M'
    mime_types: ['image/jpeg', 'image/png', 'image/webp']
```

## Usage

```php
$builder->add('avatarFile', ImageUploadType::class, ['required' => true]);

$filename = $imageUploader->upload($form->get('avatarFile')->getData(), 'avatars');
$imageUploader->remove($oldFilename, 'avatars');
```

## Security notes

- Filenames are random, so client filenames are never trusted.
- SVG is excluded by default because SVG files can contain scripts.
- Folder names are validated which prevents path traversal.