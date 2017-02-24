<?php

namespace integready\images\controllers;

use yii\web\Controller;

/**
 * Class DefaultController
 * @package integready\images\controllers
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
