<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 24.08.2019, 06:28
 */

namespace Core\Utility;
use Exception;
use BackblazeB2\File;
use BackblazeB2\Client;
use GuzzleHttp\Exception\GuzzleException;
use BackblazeB2\Exceptions\NotFoundException;


class FileHandler
{
    private $client;

    /**
     * FileHandler constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->client = new Client($_SERVER['BACKBLAZE_ID'], $_SERVER['BACKBLAZE_MASTER']);
    }

    /**
     * @param array  $file
     * @param string $path
     *
     * @return string
     */
    public function upload(array $file, string $path): string
    {
        /** @var File $out_file */
        $out_file = $this->client->upload([
            'BucketName' => 'Omnibus',
            'FileName' => $path . '.' . explode('/', $file['type'])[1],
            'Body' => fopen($file['tmp_name'], 'rb')
        ]);

        return $out_file->getName();
    }

    /**
     * @param string $path
     *
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws Exception
     */
    public function delete(string $path): void
    {
        $this->client->deleteFile([
            'FileName' => $path,
            'BucketName' => 'Omnibus'
        ]);
    }
}
