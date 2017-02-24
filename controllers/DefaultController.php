<?php

namespace legront\images\controllers;

use yii\web\Controller;

/**
 * Class DefaultController
 * @package legront\images\controllers
 */
class DefaultController extends Controller
{
    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
