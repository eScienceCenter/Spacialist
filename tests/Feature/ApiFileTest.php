<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use App\EntityFile;
use App\File;
use App\User;

class ApiFileTest extends TestCase
{
    private static $testFiles = [
        'text1.txt',
        'text1.2.txt',
        'text2.txt',
        'text3.txt',
        'office_file.docx',
        'spacialist_screenshot.png',
        'spacialist_screenshot_thumb.jpg',
        'test_img_edin.jpg',
        'test_img_edin_thumb.jpg',
        'test_archive.zip',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $path = storage_path() . "/framework/testing/";
        foreach(self::$testFiles as $f) {
            $filehandle = fopen("$path$f", 'r');
            Storage::put(
                $f,
                $filehandle
            );
            fclose($filehandle);
        }
    }

    // Testing GET requests

    /**
     * Test retrieving uploaded file (id=4).
     *
     * @return void
     */
    public function testGetPngFileEndpoint()
    {
        $file = File::find(4);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/4');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'modified',
            'modified_unix',
            'created',
            'created_unix',
            'size',
            'cameraname',
            'exif',
            'thumb',
            'copyright',
            'description',
            'mime_type',
            'user_id',
            'created_at',
            'updated_at',
            'url',
            'thumb_url',
            'category',
            'entities',
            'tags'
        ]);

        $name = 'spacialist_screenshot.png';
        $response->assertJson([
            'id' => 4,
            'name' => $name,
            'modified' => '2019-03-08 13:13:11',
            'modified_unix' => Storage::lastModified($name),
            'created' => '2019-03-08 13:13:11',
            'created_unix' => strtotime('2019-03-08 13:13:11'),
            'size' => Storage::size($name),
            'cameraname' => null,
            'exif' => null,
            'thumb' => 'spacialist_screenshot_thumb.jpg',
            'copyright' => null,
            'description' => null,
            'mime_type' => 'image/png',
            'user_id' => 1,
            'created_at' => '2019-03-08T13:13:11.000000Z',
            'updated_at' => '2019-03-08T13:13:11.000000Z',
            'url' => "/storage/spacialist_screenshot.png",
            'thumb_url' => "/storage/spacialist_screenshot_thumb.jpg",
            'category' => 'image',
            'entities' => [
                ['id' => 3]
            ],
            'tags' => []
        ]);
    }

    /**
     * Test retrieving uploaded file (id=5).
     *
     * @return void
     */
    public function testGetJpgFileEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/5');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'modified',
            'modified_unix',
            'created',
            'created_unix',
            'size',
            'cameraname',
            'exif',
            'thumb',
            'copyright',
            'description',
            'mime_type',
            'user_id',
            'created_at',
            'updated_at',
            'url',
            'thumb_url',
            'category',
            'entities',
            'tags'
        ]);

        $name = 'test_img_edin.jpg';
        $response->assertJson([
            'id' => 5,
            'name' => $name,
            'modified' => '2019-03-08 13:13:11',
            'modified_unix' => Storage::lastModified($name),
            'created' => '2017-06-18 19:46:37',
            'created_unix' => strtotime('2017-06-18 19:46:37'),
            'size' => Storage::size($name),
            'cameraname' => 'Canon EOS 650D (Canon)',
            'exif' => [

            ],
            'thumb' => 'test_img_edin_thumb.jpg',
            'copyright' => 'Vinzenz Rosenkranz (CC BY-NC-SA 2.0)',
            'description' => 'Edinburgh Castle',
            'mime_type' => 'image/jpeg',
            'user_id' => 1,
            'created_at' => '2019-03-08T13:13:11.000000Z',
            'updated_at' => '2019-03-08T13:13:12.000000Z',
            'url' => "/storage/test_img_edin.jpg",
            'thumb_url' => "/storage/test_img_edin_thumb.jpg",
            'category' => 'image',
            'entities' => [
                ['id' => 1]
            ],
            'tags' => []
        ]);
    }

    /**
     * Test getting tree-like content of an archive file (id=6).
     *
     * @return void
     */
    public function testGetArchiveFileListEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/6/archive/list');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'isDirectory',
                'path',
                'compressedSize',
                'uncompressedSize',
                'modificationTime',
                'isCompressed',
                'cleanFilename'
            ]
        ]);

        $response->assertJson([
            [
                'children' => [
                    [
                        'isDirectory' => false,
                        'path' => 'folder/folder_text1.txt',
                        'compressedSize' => 45,
                        'uncompressedSize' => 46,
                        'isCompressed' => true,
                        'cleanFilename' => 'folder_text1.txt'
                    ]
                ],
                'isDirectory' => true,
                'path' => 'folder/',
                'compressedSize' => 0,
                'uncompressedSize' => 0,
                'modificationTime' => 0,
                'isCompressed' => false,
                'cleanFilename' => 'folder'
            ],
            [
                'isDirectory' => false,
                'path' => 'test_img_edin.jpg',
                'compressedSize' => 3391570,
                'uncompressedSize' => 3407381,
                'isCompressed' => true,
                'cleanFilename' => 'test_img_edin.jpg'
            ],
            [
                'isDirectory' => false,
                'path' => 'text2.txt',
                'compressedSize' => 38,
                'uncompressedSize' => 36,
                'isCompressed' => true,
                'cleanFilename' => 'text2.txt'
            ],
            [
                'isDirectory' => false,
                'path' => 'text3.txt',
                'compressedSize' => 39,
                'uncompressedSize' => 40,
                'isCompressed' => true,
                'cleanFilename' => 'text3.txt'
            ],
        ]);
    }

    /**
     * Test getting content of a file inside an archive (id=6).
     *
     * @return void
     */
    public function testGetArchivedFileEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/6/archive/download?p=folder/folder_text1.txt');

        $response->assertStatus(200);
        $this->assertEquals(\base64_encode("This is a test file inside a folder. Awesome!\n"), $response->getContent());
    }

    /**
     * Test getting the content of a word document as html (id=7).
     *
     * @return void
     */
    public function testGetDocAsHtmlEndpoint()
    {
        $file = File::find(5);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/7/as_html');

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'A test .docx file created in LibreOffice!',
            $response->getContent()
        );
    }

    /**
     * Test count of entities linked to a file (id=2) and non-linked file (id=1).
     *
     * @return void
     */
    public function testLinkCountEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/5/link_count');

        $response->assertStatus(200);
        $response->assertSimilarJson([1]);

        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/1/link_count');

        $response->assertStatus(200);
        $response->assertSimilarJson([0]);
    }

    /**
     * Test getting files of children of an entity (id=1) and (id=2).
     *
     * @return void
     */
    public function testSubFilesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/1/sub_files');

        $response->assertStatus(200);
        $response->assertJsonCount(1);
        $response->assertSimilarJson([
            [
                'id' => 3,
                'name' => 'text1.txt',
                'modified' => '2019-03-08 13:13:11',
                'modified_unix' => Storage::lastModified('text1.txt'),
                'created' => '2019-03-08 13:13:11',
                'created_unix' => 1552050791,
                'cameraname' => null,
                'thumb' => null,
                'copyright' => null,
                'description' => null,
                'mime_type' => 'text/plain',
                'user_id' => 1,
                'created_at' => '2019-03-08T13:13:11.000000Z',
                'updated_at' => '2019-03-08T13:13:11.000000Z',
                'category' => 'text',
                'size' => Storage::size('text1.txt'),
                'exif' => null,
                'tags' => [],
                'url' => '/storage/text1.txt',
            ]
        ]);

        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/2/sub_files');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'modified',
                'modified_unix',
                'created',
                'created_unix',
                'cameraname',
                'thumb',
                'copyright',
                'description',
                'mime_type',
                'user_id',
                'created_at',
                'updated_at',
                'category',
                'size',
                'exif',
                'tags',
                'url',
            ]
        ]);
    }

    /**
     * Test getting supported file categories.
     *
     * @return void
     */
    public function testFileCategoriesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/filter/category');

        $response->assertStatus(200);
        $response->assertSimilarJson([
            ['key' => 'image', 'label' => 'Image'],
            ['key' => 'audio', 'label' => 'Audio File'],
            ['key' => 'video', 'label' => 'Video File'],
            ['key' => 'pdf', 'label' => 'PDF'],
            ['key' => 'xml', 'label' => 'XML'],
            ['key' => 'html', 'label' => 'HTML'],
            ['key' => '3d', 'label' => '3D File'],
            ['key' => 'dicom', 'label' => 'DICOM File'],
            ['key' => 'archive', 'label' => 'Archive'],
            ['key' => 'text', 'label' => 'Text File'],
            ['key' => 'document', 'label' => 'Office Documents'],
            ['key' => 'spreadsheet', 'label' => 'Spreadsheets'],
            ['key' => 'presentation', 'label' => 'Presentation Files'],
        ]);
    }

    /**
     * Test getting stored camera names.
     *
     * @return void
     */
    public function testFileCamerasEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/filter/camera');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertSimilarJson([
            'Canon EOS 650D (Canon)',
            'Null',
        ]);
    }

    /**
     * Test getting stored created dates of files.
     *
     * @return void
     */
    public function testFileDatesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/filter/date');

        $response->assertStatus(200);
        $response->assertJsonCount(4);
        $response->assertSimilarJson([
            ['is' => 'date', 'value' => '2017-06-18'],
            ['is' => 'date', 'value' => '2019-03-08'],
            ['is' => 'year', 'value' => '2017'],
            ['is' => 'year', 'value' => '2019'],
        ]);
    }

    /**
     * Test getting available tags.
     *
     * @return void
     */
    public function testGetTagsEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/tags');

        $response->assertStatus(200);
        $response->assertJsonCount(14);
        $response->assertJson([
            ['id' => 15],
            ['id' => 16],
            ['id' => 17],
            ['id' => 18],
            ['id' => 19],
            ['id' => 20],
            ['id' => 23],
            ['id' => 24],
            ['id' => 25],
            ['id' => 26],
            ['id' => 27],
            ['id' => 28],
            ['id' => 43],
            ['id' => 47],
        ]);
    }

    // Testing POST requests

    /**
     * Test file upload.
     *
     * @return void
     */
    public function testFileUploadEndpoint()
    {
        $file = UploadedFile::fake()->image('spacialist_screenshot.png', 350, 100);

        $desc = $this->faker->text();
        $cr = $this->faker->text();

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/new', [
                'file' => $file,
                'description' => $desc,
                'copyright' => $cr,
                'tags' => '[19, 20]',
            ]);

        $uplFile = File::with('tags')->latest()->first();

        Storage::assertExists('spacialist_screenshot.0.png');
        Storage::assertExists('spacialist_screenshot_thumb.0.jpg');

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'modified',
            'created',
            'cameraname',
            'exif',
            'thumb',
            'copyright',
            'description',
            'mime_type',
            'user_id',
            'created_at',
            'updated_at',
            'category'
        ]);

        $response->assertSimilarJson([
            'id' => $uplFile->id,
            'name' => 'spacialist_screenshot.0.png',
            'modified' => $uplFile->modified,
            'created' => $uplFile->created,
            'cameraname' => null,
            'exif' => null,
            'thumb' => 'spacialist_screenshot_thumb.0.jpg',
            'copyright' => $cr,
            'description' => $desc,
            'mime_type' => 'image/png',
            'user_id' => 1,
            'created_at' => $uplFile->created_at->toJSON(),
            'updated_at' => $uplFile->updated_at->toJSON(),
            'category' => 'image'
        ]);

        $this->assertArraySubset([
            ['id' => 19],
            ['id' => 20],
        ], $uplFile->tags->toArray());
    }

    /**
     * Test jpg file upload with exif data.
     *
     * @return void
     */
    public function testJpgFileUploadEndpoint()
    {
        $path = storage_path() . "/framework/testing/test_img_edin.jpg";
        $file = new UploadedFile($path, 'test_img_edin.jpg', 'image/jpeg', null, true);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/new', [
                'file' => $file,
            ]);

        $uplFile = File::latest()->first();

        Storage::assertExists('test_img_edin.0.jpg');
        Storage::assertExists('test_img_edin_thumb.0.jpg');

        $response->assertStatus(201);
        $response->assertJson([
            'id' => $uplFile->id,
            'name' => 'test_img_edin.0.jpg',
            'cameraname' => 'Canon EOS 650D (Canon)',
            'exif' => [],
            'thumb' => 'test_img_edin_thumb.0.jpg',
            'mime_type' => 'image/jpeg',
            'user_id' => 1,
            'category' => 'image'
        ]);
        $content = $response->decodeResponseJson();
        $exif = $content['exif'];
        $this->assertEquals('Canon', $exif['Make']);
        $this->assertEquals('Canon EOS 650D', $exif['Model']);
        $this->assertEquals('Exif Version 2.3', $exif['Exif']['ExifVersion']);
        $this->assertEquals('f/7.0', $exif['Exif']['ApertureValue']);
    }

    /**
     * Test file upload with existing file with name.
     *
     * @return void
     */
    public function testFileUploadExistingEndpoint()
    {
        $path = storage_path() . "/framework/testing/text1.2.txt";
        $file = new UploadedFile($path, 'text1.2.txt', 'none/defined', null, true);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/new', [
                'file' => $file,
            ]);

        $uplFile = File::latest()->first();

        Storage::assertExists('text1.2.txt');
        Storage::assertExists('text1.3.txt');

        $response->assertStatus(201);
        $response->assertJson([
            'id' => $uplFile->id,
            'name' => 'text1.3.txt',
            'exif' => null,
            'mime_type' => 'text/plain',
            'user_id' => 1,
            'category' => 'text'
        ]);
        $this->assertTrue($uplFile->isText());
    }

    /**
     * Test getting all files.
     *
     * @return void
     */
    public function testGetAllFilesUnfilteredEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'current_page',
            'data',
            'first_page_url',
            'last_page',
            'last_page_url',
            'next_page_url',
            'prev_page_url',
            'from',
            'to',
            'total',
            'per_page',
            'path',
        ]);
        $response->assertJson([
            'current_page' => 1,
            'data' => [
                ['name' => 'text3.txt'],
                ['name' => 'text2.txt'],
                ['name' => 'text1.txt'],
                ['name' => 'spacialist_screenshot.png'],
                ['name' => 'test_img_edin.jpg'],
                ['name' => 'test_archive.zip'],
                ['name' => 'office_file.docx'],
            ],
            'first_page_url' => '/file?page=1',
            'last_page' => 1,
            'last_page_url' => '/file?page=1',
            'next_page_url' => null,
            'prev_page_url' => null,
            'from' => 1,
            'to' => 7,
            'total' => 7,
            'per_page' => 15,
            'path' => '/file'
        ]);
    }

    /**
     * Test getting all files by categories.
     *
     * @return void
     */
    public function testGetAllFilesByCategoryEndpoint()
    {
        $categories = [
            'image' => [
                ['name' => 'spacialist_screenshot.png'],
                ['name' => 'test_img_edin.jpg'],
            ],
            'audio' => [],
            'video' => [],
            'pdf' => [],
            'xml' => [],
            'html' => [],
            '3d' => [],
            'dicom' => [],
            'archive' => [
                ['name' => 'test_archive.zip'],
            ],
            'text' => [
                ['name' => 'text3.txt'],
                ['name' => 'text2.txt'],
                ['name' => 'text1.txt'],
            ],
            'document' => [
                ['name' => 'office_file.docx'],
            ],
            'spreadsheet' => [],
            'presentation' => [],
        ];

        foreach($categories as $c => $data) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->post('/api/v1/file', [
                    'filters' => [
                        'categories' => [
                            $c
                        ]
                    ]
                ]);

            $response->assertStatus(200);
            $response->assertJson([
                'data' => $data
            ]);
            $this->assertEquals(count($data), count($response->decodeResponseJson()['data']));

            $this->refreshToken($response);
        }
    }

    /**
     * Test getting all files by camera (canon eos 650d) and (nikon d700).
     *
     * @return void
     */
    public function testGetAllFilesByCameraEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file', [
                'filters' => [
                    'cameras' => [
                        'Canon EOS 650D (Canon)'
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'test_img_edin.jpg'],
            ]
        ]);

        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file', [
                'filters' => [
                    'cameras' => [
                        'Nikon D700 (Nikon)'
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $content = $response->decodeResponseJson();
        $this->assertEquals(0, count($content['data']));
    }

    /**
     * Test getting all files by year.
     *
     * @return void
     */
    public function testGetAllFilesByYearEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file', [
                'filters' => [
                    'dates' => [
                        ['is' => 'year', 'value' => '2017']
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'test_img_edin.jpg'],
            ]
        ]);

        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file', [
                'filters' => [
                    'dates' => [
                        ['is' => 'year', 'value' => '2018']
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $content = $response->decodeResponseJson();
        $this->assertEquals(0, count($content['data']));
    }

    /**
     * Test getting all files by date.
     *
     * @return void
     */
    public function testGetAllFilesByDateEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file', [
                'filters' => [
                    'dates' => [
                        ['is' => 'date', 'value' => '2017-06-18']
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'test_img_edin.jpg'],
            ]
        ]);
    }

    /**
     * Test getting all files by tags.
     *
     * @return void
     */
    public function testGetAllFilesByTagsEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file', [
                'filters' => [
                    'tags' => [
                        ['id' => 19],
                        ['id' => 20],
                    ]
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'text2.txt'],
                ['name' => 'test_img_edin.jpg'],
            ]
        ]);
    }

    /**
     * Test getting all unlinked files.
     *
     * @return void
     */
    public function testGetUnlinkedFilesUnfilteredEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/unlinked');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'text3.txt'],
                ['name' => 'office_file.docx'],
            ]
        ]);
        $content = $response->decodeResponseJson();
        $this->assertEquals(2, count($content['data']));
    }

    /**
     * Test getting all files linked to an entity (id=3) and to another (id=2) including files of sub-entities.
     *
     * @return void
     */
    public function testGetLinkedFilesUnfilteredEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/linked/3');

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'spacialist_screenshot.png'],
                ['name' => 'test_archive.zip'],
            ]
        ]);
        $content = $response->decodeResponseJson();
        $this->assertEquals(2, count($content['data']));

        $this->refreshToken($response);

        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/linked/2', [
                'filters' => [
                    'sub_entities' => true
                ]
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                ['name' => 'text2.txt'],
                ['name' => 'text1.txt'],
                ['name' => 'spacialist_screenshot.png'],
                ['name' => 'test_archive.zip'],
            ]
        ]);
        $content = $response->decodeResponseJson();
        $this->assertEquals(4, count($content['data']));
    }

    /**
     * Test patching content of a file (id=3).
     *
     * @return void
     */
    public function testPatchFileContentEndpoint()
    {
        // Paste content of file text2.txt into text1.txt
        $name = 'text2.txt';
        $path = storage_path() . "/framework/testing/$name";
        $file = new UploadedFile($path, $name, 'text/plain', null, true);
        $fileContent = Storage::get('text1.txt');
        $this->assertEquals("This is test file #1 for Spacialist.\n", $fileContent);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/3/patch', [
                'file' => $file
            ]);

        $fileContent = Storage::get('text1.txt');
        $this->assertEquals("The second test file for Spacialist\n", $fileContent);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'modified',
            'created',
            'cameraname',
            'thumb',
            'copyright',
            'description',
            'mime_type',
            'user_id',
            'created_at',
            'updated_at',
        ]);
        $response->assertJson([
            'id' => 3,
            'name' => 'text1.txt',
            'cameraname' => null,
            'thumb' => null,
            'copyright' => null,
            'description' => null,
            'mime_type' => 'text/plain',
            'user_id' => 1,
        ]);

        $this->refreshToken($response);

        // Paste content of file test_img_edin.jpg into text1.txt
        $name = 'test_img_edin.jpg';
        $path = storage_path() . "/framework/testing/$name";
        $file = new UploadedFile($path, $name, 'image/jpeg', null, true);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/3/patch', [
                'file' => $file
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => 3,
            'name' => 'text1.txt',
            'cameraname' => null,
            'thumb' => null,
            'copyright' => null,
            'description' => null,
            'mime_type' => 'image/jpeg',
            'user_id' => 1,
        ]);
    }

    /**
     * Test exporting files (ids=1, 2, 3).
     *
     * @return void
     */
    public function testExportingFilesEndpoint()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->post('/api/v1/file/export', [
                'files' => [1, 2, 3, 5]
            ]);

        $response->assertStatus(200);

        // Create temp file from base64 response and open it using build-in
        // ZipArchive
        $content = $response->getContent();
        $path = \tempnam(\sys_get_temp_dir(), 'test');
        file_put_contents($path, base64_decode($content));
        $za = new \ZipArchive();
        $za->open($path);

        $this->assertEquals(4, $za->numFiles);

        // Get all names from the archive and test it against names of the files (1, 2, 3)
        $names = [];
        for($i=0; $i<$za->numFiles; $i++) {
            $stat = $za->statIndex($i);
            $names[] = $stat['name'];
        }

        $this->assertEquals([
            'text3.txt',
            'text2.txt',
            'text1.txt',
            'test_img_edin.jpg',
        ], $names);
    }

    // Testing PATCH requests

    /**
     * Test patching a property (description and name) of a file (id=1)
     *
     * @return void
     */
    public function testPatchPropertyEndpoint()
    {
        $file = File::find(1);
        $this->assertEquals(null, $file->description);
        $this->assertEquals('text3.txt', $file->name);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/file/1/property', [
                'description' => 'This is just a test file.',
                'name' => 'test_file.txt'
            ]);

        $response->assertStatus(200);
        $file = File::find(1);
        $this->assertEquals('This is just a test file.', $file->description);
        $this->assertEquals('test_file.txt', $file->name);
    }

    /**
     * Test patching a property (description and name) of a file (id=1)
     *
     * @return void
     */
    public function testPatchPropertyImageNameEndpoint()
    {
        $file = File::find(5);
        $this->assertEquals('Edinburgh Castle', $file->description);
        $this->assertEquals('Vinzenz Rosenkranz (CC BY-NC-SA 2.0)', $file->copyright);
        $this->assertEquals('test_img_edin.jpg', $file->name);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/file/5/property', [
                'name' => 'edinburgh_castle.jpg'
            ]);

        $response->assertStatus(200);
        $file = File::find(5);
        $this->assertEquals('Edinburgh Castle', $file->description);
        $this->assertEquals('Vinzenz Rosenkranz (CC BY-NC-SA 2.0)', $file->copyright);
        $this->assertEquals('edinburgh_castle.jpg', $file->name);
        $this->assertEquals('edinburgh_castle_thumb.jpg', $file->thumb);
        Storage::assertExists('edinburgh_castle.jpg');
        Storage::assertExists('edinburgh_castle_thumb.jpg');
    }

    /**
     * Test patching tags of a file (id=2)
     *
     * @return void
     */
    public function testPatchTagEndpoint()
    {
        $file = File::with('tags')->find(2);
        $this->assertEquals(3, count($file->tags));
        $this->assertEquals('text2.txt', $file->name);
        $this->assertEquals(18, $file->tags[0]->id);
        $this->assertEquals(19, $file->tags[1]->id);
        $this->assertEquals(26, $file->tags[2]->id);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->patch('/api/v1/file/2/tag', [
                'tags' => [18, 19, 20, 25],
            ]);

        $response->assertStatus(204);
        $file = File::with('tags')->find(2);
        $this->assertEquals(4, count($file->tags));
        $this->assertEquals('text2.txt', $file->name);
        $this->assertEquals(18, $file->tags[0]->id);
        $this->assertEquals(19, $file->tags[1]->id);
        $this->assertEquals(20, $file->tags[2]->id);
        $this->assertEquals(25, $file->tags[3]->id);
    }

    // Testing PUT requests

    /**
     * Test linking a file (id=1) to an entity (id=2)
     *
     * @return void
     */
    public function testLinkToEntityEndpoint()
    {
        $this->assertTrue(!EntityFile::where('file_id', 1)->where('entity_id', 2)->exists());
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->put('/api/v1/file/1/link', [
                'entity_id' => 2
            ]);

        $response->assertStatus(204);
        $this->assertTrue(EntityFile::where('file_id', 1)->where('entity_id', 2)->exists());
    }

    // Testing DELETE requests

    /**
     * Test deleting a file (id=4)
     *
     * @return void
     */
    public function testDeleteFileEndpoint()
    {
        $linkCnt = EntityFile::count();
        $this->assertEquals(5, $linkCnt);
        $fileCount = File::count();
        $this->assertEquals(7, $fileCount);
        try {
            Storage::get('spacialist_screenshot.png');
            $this->assertTrue(true);
        } catch(FileNotFoundException $e) {
            $this->assertTrue(false);
        }
        try {
            Storage::get('spacialist_screenshot_thumb.jpg');
            $this->assertTrue(true);
        } catch(FileNotFoundException $e) {
            $this->assertTrue(false);
        }
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/file/4');

        $response->assertStatus(204);
        $linkCnt = EntityFile::count();
        $this->assertEquals(4, $linkCnt);
        $fileCount = File::count();
        $this->assertEquals(6, $fileCount);
        try {
            Storage::get('spacialist_screenshot.png');
            $this->assertTrue(false);
        } catch(FileNotFoundException $e) {
            $this->assertTrue(true);
        }
        try {
            Storage::get('spacialist_screenshot_thumb.jpg');
            $this->assertTrue(false);
        } catch(FileNotFoundException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test deleting a file link from (id=4) to entity (id=3)
     *
     * @return void
     */
    public function testUnlinkFileEndpoint()
    {
        $linkCnt = EntityFile::count();
        $this->assertEquals(5, $linkCnt);
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->delete('/api/v1/file/4/link/3');

        $response->assertStatus(204);
        $linkCnt = EntityFile::count();
        $this->assertEquals(4, $linkCnt);
    }

    // Testing exceptions and permissions

    /**
     *
     *
     * @return void
     */
    public function testPermissions()
    {
        User::first()->roles()->detach();

        $calls = [
            ['url' => '/1', 'error' => 'You do not have the permission to view a specific file', 'verb' => 'get'],
            ['url' => '/1/archive/list', 'error' => 'You do not have the permission to view a specific file', 'verb' => 'get'],
            ['url' => '/1/archive/download', 'error' => 'You do not have the permission to download parts of a zip file', 'verb' => 'get'],
            ['url' => '/1/as_html', 'error' => 'You do not have the permission to view a specific file as HTML', 'verb' => 'get'],
            ['url' => '/1/sub_files', 'error' => 'You do not have the permission to view successors of a specific file', 'verb' => 'get'],
            ['url' => '/1/link_count', 'error' => 'You do not have the permission to get number of links of a specific file', 'verb' => 'get'],
            ['url' => '/filter/category', 'error' => 'You do not have the permission to get the file categories', 'verb' => 'get'],
            ['url' => '/filter/camera', 'error' => 'You do not have the permission to get the camera names', 'verb' => 'get'],
            ['url' => '/filter/date', 'error' => 'You do not have the permission to get the file dates', 'verb' => 'get'],
            ['url' => '/tags', 'error' => 'You do not have the permission to get tags', 'verb' => 'get'],
            ['url' => '/', 'error' => 'You do not have the permission to view files', 'verb' => 'post'],
            ['url' => '/unlinked', 'error' => 'You do not have the permission to view files', 'verb' => 'post'],
            ['url' => '/linked/1', 'error' => 'You do not have the permission to view files', 'verb' => 'post'],
            ['url' => '/new', 'error' => 'You do not have the permission to upload files', 'verb' => 'post'],
            ['url' => '/1/patch', 'error' => 'You do not have the permission to edit a file\'s content', 'verb' => 'post'],
            ['url' => '/export', 'error' => 'You do not have the permission to export files', 'verb' => 'post'],
            ['url' => '/1/property', 'error' => 'You do not have the permission to modify file properties', 'verb' => 'patch'],
            ['url' => '/1/tag', 'error' => 'You do not have the permission to modify file properties', 'verb' => 'patch'],
            ['url' => '/1/link', 'error' => 'You do not have the permission to link files', 'verb' => 'put'],
            ['url' => '/1', 'error' => 'You do not have the permission to delete files', 'verb' => 'delete'],
            ['url' => '/1/link/1', 'error' => 'You do not have the permission to unlink files', 'verb' => 'delete'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/file' . $c['url']);

            $response->assertStatus(403);
            $response->assertSimilarJson([
                'error' => $c['error']
            ]);

            $this->refreshToken($response);
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testExceptions()
    {
        $calls = [
            ['url' => '/99', 'error' => 'This file does not exist', 'verb' => 'get'],
            ['url' => '/99/archive/list', 'error' => 'This file does not exist', 'verb' => 'get'],
            ['url' => '/99/archive/download', 'error' => 'This file does not exist', 'verb' => 'get'],
            ['url' => '/99/as_html', 'error' => 'This file does not exist', 'verb' => 'get'],
            ['url' => '/99/sub_files', 'error' => 'This entity does not exist', 'verb' => 'get'],
            ['url' => '/99/link_count', 'error' => 'This file does not exist', 'verb' => 'get'],
            ['url' => '/99/patch', 'error' => 'This file does not exist', 'verb' => 'post'],
            ['url' => '/99/property', 'error' => 'This file does not exist', 'verb' => 'patch'],
            ['url' => '/1/property', 'error' => 'There is already a file with this name', 'verb' => 'patch'],
            ['url' => '/99/tag', 'error' => 'This file does not exist', 'verb' => 'patch'],
            ['url' => '/99/link', 'error' => 'This file does not exist', 'verb' => 'put'],
            ['url' => '/99', 'error' => 'This file does not exist', 'verb' => 'delete'],
            ['url' => '/99/link/1', 'error' => 'This file does not exist', 'verb' => 'delete'],
            ['url' => '/1/link/99', 'error' => 'This entity does not exist', 'verb' => 'delete'],
        ];

        foreach($calls as $c) {
            $response = $this->withHeaders([
                    'Authorization' => "Bearer $this->token"
                ])
                ->json($c['verb'], '/api/v1/file' . $c['url'], [
                    'name' => 'text1.txt',
                    'p' => '/text1.txt',
                    'file' => UploadedFile::fake()->create('text4.txt'),
                    'entity_id' => 3,
                ]);

            $response->assertStatus(400);
            $response->assertSimilarJson([
                'error' => $c['error']
            ]);

            $this->refreshToken($response);
        }
    }

    /**
     *
     *
     * @return void
     */
    public function testUnsupportedToHtmlFormat()
    {
        $response = $this->withHeaders([
                'Authorization' => "Bearer $this->token"
            ])
            ->get('/api/v1/file/1/as_html');

        $response->assertStatus(200);
        $response->assertSimilarJson([
            'error' => 'HTML not supported for file type text/plain'
        ]);

    }
}
