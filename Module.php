<?php

namespace integready\images;

use integready\images\models\Image;
use integready\images\models\PlaceHolder;
use Yii;
use yii\helpers\Inflector;

/**
 * Class Module
 * @package integready\images
 *
 * @property bool|string $storePath
 * @property bool|string $cachePath
 * @property null|\integready\images\models\PlaceHolder $placeHolder
 */
class Module extends \yii\base\Module
{
    public $imagesStorePath     = '@app/web/store';
    public $imagesCachePath     = '@app/web/imgCache';
    public $graphicsLibrary     = 'GD';
    public $controllerNamespace = 'integready\images\controllers';
    public $placeHolderPath;
    public $waterMark           = false;
    public $className;
    public $imageDomain         = false;

    /**
     * @param string $item
     * @param string $dirtyAlias
     *
     * @return array|PlaceHolder|null|yii\db\ActiveRecord
     */
    public function getImage($item, $dirtyAlias)
    {
        //Get params
        $params = $data = $this->parseImageAlias($dirtyAlias);

        $alias = $params['alias'];

        $itemId    = preg_replace('/[^0-9]+/', '', $item);
        $modelName = preg_replace('/[0-9]+/', '', $item);

        //Lets get image
        if (empty($this->className)) {
            $imageQuery = Image::find();
        } else {
            /** @var Image $class */
            $class      = $this->className;
            $imageQuery = $class::find();
        }
        $image = $imageQuery
            ->where([
                'modelName' => $modelName,
                'itemId'    => $itemId,
                'urlAlias'  => $alias,
            ])
            /*     ->where('modelName = :modelName AND itemId = :itemId AND urlAlias = :alias',
                     [
                         ':modelName' => $modelName,
                         ':itemId' => $itemId,
                         ':alias' => $alias
                     ])*/
            ->one();
        if (!$image) {
            return $this->getPlaceHolder();
        }

        return $image;
    }

    /**
     * @param string $parameterized
     *
     * @return array
     */
    public function parseImageAlias($parameterized)
    {
        $params = explode('_', $parameterized);

        if (count($params) == 1) {
            $alias = $params[0];
            $size  = null;
        } elseif (count($params) == 2) {
            $alias = $params[0];
            $size  = $this->parseSize($params[1]);
            if (!$size) {
                $alias = null;
            }
        } else {
            $alias = null;
            $size  = null;
        }

        return ['alias' => $alias, 'size' => $size];
    }

    /**
     *
     * Parses size string
     * For instance: 400x400, 400x, x400
     *
     * @param string $notParsedSize
     *
     * @return array|null
     * @throws \Exception
     */
    public function parseSize($notParsedSize)
    {
        $sizeParts = explode('x', $notParsedSize);
        $part1     = (isset($sizeParts[0]) and $sizeParts[0] != '');
        $part2     = (isset($sizeParts[1]) and $sizeParts[1] != '');
        if ($part1 && $part2) {
            if (intval($sizeParts[0]) > 0
                &&
                intval($sizeParts[1]) > 0
            ) {
                $size = [
                    'width'  => intval($sizeParts[0]),
                    'height' => intval($sizeParts[1]),
                ];
            } else {
                $size = null;
            }
        } elseif ($part1 && !$part2) {
            $size = [
                'width'  => intval($sizeParts[0]),
                'height' => null,
            ];
        } elseif (!$part1 && $part2) {
            $size = [
                'width'  => null,
                'height' => intval($sizeParts[1]),
            ];
        } else {
            throw new \Exception('Something bad with size, sorry!');
        }

        return $size;
    }

    /**
     * @return PlaceHolder|null
     */
    public function getPlaceHolder()
    {
        if ($this->placeHolderPath) {
            return new PlaceHolder();
        } else {
            return null;
        }
    }

    /**
     * @return bool|string
     */
    public function getStorePath()
    {
        return Yii::getAlias($this->imagesStorePath);
    }

    /**
     * @return bool|string
     */
    public function getCachePath()
    {
        return Yii::getAlias($this->imagesCachePath);
    }

    /**
     * @return bool|string
     */
    public function getImageDomain()
    {
        return Yii::getAlias($this->imageDomain);
    }

    /**
     * @param object|null $model
     *
     * @return string
     */
    public function getModelSubDir($model)
    {
        $modelName = $this->getShortClass($model);
        $modelDir  = Inflector::pluralize($modelName) . '/' . $modelName . $model->id;

        return $modelDir;
    }

    /**
     * @param object|null $obj
     *
     * @return string
     */
    public function getShortClass($obj)
    {
        $className = get_class($obj);

        if (preg_match('@\\\\([\w]+)$@', $className, $matches)) {
            $className = $matches[1];
        }

        return $className;
    }

    public function init()
    {
        parent::init();
        if (!$this->imagesStorePath
            or
            !$this->imagesCachePath
            or
            $this->imagesStorePath == '@app'
            or
            $this->imagesCachePath == '@app'
        ) {
            throw new \Exception('Setup imagesStorePath and imagesCachePath images module properties!!!');
        }
        // custom initialization code goes here
    }
}
