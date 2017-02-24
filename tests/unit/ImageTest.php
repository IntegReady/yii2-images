<?php

use legront\images\behaviors\ImageBehave;
use org\bovigo\vfs\vfsStream;
use yii\codeception\DbTestCase;
use yii\codeception\TestCase;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\BaseFileHelper;

class ImageTest extends TestCase
{
    protected $dbConnection;
    private   $model;

    public static function setUpBeforeClass()
    {
        if (!extension_loaded('pdo') || !extension_loaded('pdo_sqlite')) {
            static::markTestSkipped('PDO and SQLite extensions are required.');
        }
    }

    public function testGetUrl()
    {

        $this->model->attachImage(__DIR__ . '/data/testPicture.jpg');
        $this->model->attachImage(__DIR__ . '/data/testPicture.jpg', true);
        $this->model->attachImage(__DIR__ . '/data/testPicture.jpg');

        $img = $this->model->getImage();

        $ext = pathinfo($img->getPathToOrigin(), PATHINFO_EXTENSION);

        $baseUrl = '/app/web/index-test.php/ricoStore/images/image-by-item-and-alias?item=ActiveRecordImage1&dirtyAlias=';
        $this->assertEquals($img->getUrl(), $baseUrl . $img->urlAlias . '.' . $ext);

        $this->assertEquals($img->getUrl('x100'), $baseUrl . $img->urlAlias . '_x100.' . $ext);
        $this->assertEquals($img->getUrl('100x400'),
            $baseUrl . $img->urlAlias . '_100x400.' . $ext);
    }

    public function testGetPath()
    {
        $this->model->attachImage(__DIR__ . '/data/testPicture.jpg');
        $image = $this->model->getImage();

        //var_dump($image->getPath());die;

        $expectedFile = Yii::getAlias('@app') . '/tests/unit/data/imgCache/ActiveRecordImages/ActiveRecordImage1/' .
            $image->urlAlias . '.jpg';
        $this->assertEquals($image->getPath(),
            $expectedFile);

        $this->assertTrue(file_exists($expectedFile));

        $expectedFile = Yii::getAlias('@app') . '/tests/unit/data/imgCache/ActiveRecordImages/ActiveRecordImage1/' .
            $image->urlAlias . '_200x100.jpg';
        $this->assertEquals($image->getPath('200x100'),
            $expectedFile);

        $this->assertTrue(file_exists($expectedFile));
    }

    // tests

    protected function setUp()
    {
        parent::setUp();
        $config = ArrayHelper::merge(require(Yii::getAlias($this->appConfig)), [
            'components' => [
                'db' => [
                    'class' => '\yii\db\Connection',
                    'dsn'   => 'sqlite::memory:',
                ],
            ],
            'modules'    => [
                'ricoStore' => [
                    'class' => 'legront\images\Module',

                ],
            ],
        ]);

        $this->mockApplication($config);

        $columns = [
            'id'   => 'pk',
            'name' => 'string',
        ];
        Yii::$app->getDb()->createCommand()->createTable('test_image_behavior', $columns)->execute();

        $columns = [
            'id'        => 'pk',
            'filePath'  => 'VARCHAR(400) NOT NULL',
            'itemId'    => 'int(20) NOT NULL',
            'isMain'    => 'int(1)',
            'modelName' => 'VARCHAR(150) NOT NULL',
            'urlAlias'  => 'VARCHAR(400) NOT NULL',
        ];
        Yii::$app->getDb()->createCommand()->createTable('image', $columns)->execute();

        /*vfsStream::setup('root');
        vfsStream::setup('root/Cache');*/
        $module = Yii::$app->getModule('ricoStore');
        //$module->imagesStorePath = vfsStream::url('root/Store');
        //$module->imagesCachePath = vfsStream::url('root/Cache');
        $module->imagesStorePath = Yii::getAlias('@app') . '/tests/unit/data/imgStore';
        $module->imagesCachePath = Yii::getAlias('@app') . '/tests/unit/data/imgCache';

        $this->model       = new ActiveRecordImage();
        $this->model->name = 'testName';
        $this->model->save();
    }

    protected function tearDown()
    {

        BaseFileHelper::removeDirectory(Yii::getAlias('@app') . '/tests/unit/data/imgStore');
        BaseFileHelper::removeDirectory(Yii::getAlias('@app') . '/tests/unit/data/imgCache');
    }
}

class ActiveRecordImage extends ActiveRecord
{
    public static function tableName()
    {
        return 'test_image_behavior';
    }

    public function behaviors()
    {
        return [
            ImageBehave::className(),
        ];
    }

    public function getStorePath()
    {
        return vfsStream::setup('testImagesDir');
    }
}