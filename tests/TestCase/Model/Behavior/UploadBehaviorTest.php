<?php
namespace Xety\Cake3Upload\Test\TestCase\Model\Behavior;

use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class UploadBehaviorTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = ['plugin.Xety\Cake3Upload.users'];

    /**
     * setUp
     *
     * @return void
     */
    public function setUp()
    {
        $this->Model = TableRegistry::get('Users');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Model);
        TableRegistry::clear();

        $folder = new Folder(WWW_ROOT . 'upload');
        $folder->delete(WWW_ROOT . 'upload');
    }

    /**
     * test beforeSaveErrorNoFile
     *
     * @return void
     */
    public function testBeforeSaveErrorNoFile()
    {
        $file = [
            'name' => '',
            'tmp_name' => '',
            'error' => UPLOAD_ERR_NO_FILE,
            'type' => 'image/png',
            'size' => 201
        ];

        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':id'
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);

        $this->Model->save($entity);

        $this->assertFalse(file_exists(WWW_ROOT . 'upload' . DS . $entity->id . DS . $entity->id));
    }

    /**
     * test beforeSaveErrorNoPathConfig
     *
     * @return void
     */
    public function testBeforeSaveErrorNoPathConfig()
    {
        $file = [
            'name' => 'avatar.png',
            'tmp_name' => TMP . 'avatar.png',
            'error' => UPLOAD_ERR_OK,
            'type' => 'image/png',
            'size' => 201
        ];

        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => []
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);

        $this->setExpectedException('LogicException');
        $this->Model->save($entity);
    }

    /**
     * test testBeforeSaveUploadOk
     *
     * @return void
     */
    public function testBeforeSaveUploadOk()
    {
        $file = [
            'name' => 'avatar.png',
            'tmp_name' => TMP . 'avatar.png',
            'error' => UPLOAD_ERR_OK,
            'type' => 'image/png',
            'size' => 201
        ];

        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':id'
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);
        $result = $this->Model->save($entity);

        $this->assertTrue(file_exists(WWW_ROOT . 'upload' . DS . $entity->id . DS . $entity->id . '.png'));
        $this->assertEquals('upload' . DS . '1' . DS . '1.png', $result->avatar);
    }

    /**
     * test testBeforeSaveUploadOkWithPrefix
     *
     * @return void
     */
    public function testBeforeSaveUploadOkWithPrefix()
    {
        $file = [
            'name' => 'avatar.png',
            'tmp_name' => TMP . 'avatar.png',
            'error' => UPLOAD_ERR_OK,
            'type' => 'image/png',
            'size' => 201
        ];

        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':id',
                    'prefix' => '..' . DS . '..' . DS
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);
        $result = $this->Model->save($entity);

        $this->assertEquals('..' . DS . '..' . DS . 'upload' . DS . '1' . DS . '1.png', $result->avatar);
    }

    /**
     * test testBeforeSaveUploadOkWithOverwrite
     *
     * @return void
     */
    public function testBeforeSaveUploadOkWithOverwrite()
    {
        $file = [
            'name' => 'avatar.png',
            'tmp_name' => TMP . 'avatar.png',
            'error' => UPLOAD_ERR_OK,
            'type' => 'image/png',
            'size' => 201
        ];

        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':md5',
                    'overwrite' => true
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);
        $before = $this->Model->save($entity);

        $this->assertTrue(file_exists(WWW_ROOT . $before->avatar), 'The new file should be created.');

        $newEntity = $this->Model->get(1);
        $this->Model->patchEntity($newEntity, ['id' => 1, 'avatar_file' => $file]);
        $after = $this->Model->save($newEntity);

        $this->assertFalse(file_exists(WWW_ROOT . $before->avatar), 'The old file should be deleted when overwrite is true');
        $this->assertTrue(file_exists(WWW_ROOT . $after->avatar), 'The new file should be created.');

        $this->Model->removeBehavior('Upload');
        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':md5',
                    'overwrite' => false
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);
        $last = $this->Model->save($entity);

        $this->assertTrue(file_exists(WWW_ROOT . $after->avatar), 'The old file should not be deleted when overwrite is
        false.');
        $this->assertTrue(file_exists(WWW_ROOT . $last->avatar), 'The new file should be created.');
    }

    /**
     * test testBeforeSaveWithAvatarWithoutExtension
     *
     * @return void
     */
    public function testBeforeSaveWithAvatarWithoutExtension()
    {
        $file = [
            'name' => 'avatar',
            'tmp_name' => TMP . 'avatar.png',
            'error' => UPLOAD_ERR_OK,
            'type' => 'image/png',
            'size' => 201
        ];

        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':id'
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);

        $this->setExpectedException('ErrorException');
        $this->Model->save($entity);
    }

    /**
     * test testBeforeSaveUploadOkWithOverwrite
     *
     * @return void
     */
    public function testBeforeSaveUploadOkWithOverwriteAndDefaultFile()
    {
        $file = [
            'name' => 'avatar.png',
            'tmp_name' => TMP . 'avatar.png',
            'error' => UPLOAD_ERR_OK,
            'type' => 'image/png',
            'size' => 201
        ];

        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':md5'
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);
        $before = $this->Model->save($entity);

        $this->Model->removeBehavior('Upload');
        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':md5',
                    'overwrite' => true,
                    'defaultFile' => $before->avatar
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_file' => $file]);
        $entity->isNew(false);
        $after = $this->Model->save($entity);

        $this->assertTrue(file_exists(WWW_ROOT . $before->avatar), 'The old file should not be deleted because it\'s the
        defaultFile.');
        $this->assertTrue(file_exists(WWW_ROOT . $after->avatar), 'The new file should be created.');
    }

    /**
     * test testBeforeSaveUploadOkWithCustomSuffix
     *
     * @return void
     */
    public function testBeforeSaveUploadOkWithCustomSuffix()
    {
        $file = [
            'name' => 'avatar.png',
            'tmp_name' => TMP . 'avatar.png',
            'error' => UPLOAD_ERR_OK,
            'type' => 'image/png',
            'size' => 201
        ];

        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'suffix' => '_test',
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':id'
                ]
            ]
        ]);

        $entity = new Entity(['id' => 1, 'avatar_test' => $file]);
        $entity->isNew(false);
        $result = $this->Model->save($entity);

        $this->assertEquals('upload' . DS . '1' . DS . '1.png', $result->avatar, 'The avatar should be created because both
        suffix match.');

        $this->Model->removeBehavior('Upload');
        $this->Model->addBehavior('Xety/Cake3Upload.Upload', [
            'suffix' => '_testFail',
            'fields' => [
                'avatar' => [
                    'path' => 'upload' . DS . ':id' . DS . ':md5'
                ]
            ]
        ]);

        $entity = $this->Model->get(1);
        $this->Model->patchEntity($entity, ['id' => 1, 'avatar_file' => $file]);
        $result = $this->Model->save($entity);

        $this->assertEquals($entity->avatar, $result->avatar, 'The avatar field should not be changed because the suffix does
         not match with the field suffix.');
    }
}
