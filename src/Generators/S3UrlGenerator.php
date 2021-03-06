<?php

namespace CipeMotion\Medialibrary\Generators;

use RuntimeException;
use CipeMotion\Medialibrary\FileTypes;
use CipeMotion\Medialibrary\Entities\File;
use CipeMotion\Medialibrary\Entities\Transformation;

class S3UrlGenerator implements IUrlGenerator
{
    /**
     * The config.
     *
     * @var array
     */
    protected $config;

    /**
     * Instantiate the URL generator.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Get a URL to the resource.
     *
     * @param \CipeMotion\Medialibrary\Entities\File                $file
     * @param \CipeMotion\Medialibrary\Entities\Transformation|null $transformation
     * @param bool                                                  $fullPreview
     * @param bool                                                  $download
     *
     * @throws \Exception
     * @return string
     */
    public function getUrlForTransformation(File $file, Transformation $transformation = null, $fullPreview = false, $download = false): string
    {
        if ($download) {
            throw new RuntimeException('The S3 url generator does not support forced download urls. Use the S3PresignedUrlGenerator.');
        }

        $region = array_get($this->config, 'region');
        $bucket = array_get($this->config, 'bucket');

        $tranformationName = $transformation->name ?? 'upload';
        $extension         = $transformation->extension ?? $file->extension;

        if ($transformation === null && $fullPreview && $file->type !== FileTypes::TYPE_IMAGE) {
            $tranformationName = 'preview';
            $extension         = 'jpg';
        }

        return "https://s3.{$region}.amazonaws.com/{$bucket}/{$file->id}/{$tranformationName}.{$extension}";
    }
}
