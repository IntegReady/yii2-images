<?php
/**
 * Created by PhpStorm.
 * User: kostanevazno
 * Date: 05.08.14
 * Time: 18:21
 *
 * TODO: check that placeholder is enable in module class
 * override methods
 */

namespace integready\images\models;

/**
 * TODO: check path to save and all image method for placeholder
 */

use Yii;

/**
 * Class PlaceHolder
 * @package integready\images\models
 *
 * @property bool|string $pathToOrigin
 * @property string $subDur
 * @property bool $main
 */
class PlaceHolder extends Image
{
    public $filePath = 'placeHolder.png';
    public $urlAlias = 'placeHolder';

    /*  public function getUrl($size = false){
          $url = $this->getModule()->placeHolderUrl;
          if(!$url){
              throw new \Exception('PlaceHolder image must have url setting!!!');
          }
          return $url;
      }*/

    /**
     * PlaceHolder constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->filePath = basename(Yii::getAlias($this->getModule()->placeHolderPath));
    }

    /**
     * @return bool|string
     * @throws \Exception
     */
    public function getPathToOrigin()
    {
        $url = Yii::getAlias($this->getModule()->placeHolderPath);
        if (!$url) {
            throw new \Exception('PlaceHolder image must have path setting!!!');
        }

        return $url;
    }

    /**
     * @return string
     */
    protected function getSubDur()
    {
        return 'placeHolder';
    }

    /**
     * @param bool $isMain
     *
     * @throws yii\base\Exception
     */
    public function setMain($isMain = true)
    {
        throw new yii\base\Exception('You must not set placeHolder as main image!!!');
    }
}
