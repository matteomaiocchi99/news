<?php

namespace backend\controllers;

use backend\models\Categories;
use backend\models\CategoriesSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * CategoriesController implements the CRUD actions for Categories model.
 */
class CategoriesController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                //Callback ci dice cosa fare se queste regole falliscono
                'denyCallback' => function($rule, $data) {
                    $this->redirect(['/site/index']);
                }
            ],
        ];
    }

    /**
     * Lists all Categories models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new CategoriesSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Categories model.
     * @param int $catid Catid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($catid)
    {
        return $this->render('view', [
            'model' => $this->findModel($catid),
        ]);
    }

    /**
     * Creates a new Categories model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Categories();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

                $model->photo = UploadedFile::getInstance($model, 'photo');
                if(!empty($model->photo)){
                    $model->uploadImage();
                }

                $model->save();

                $_SESSION["success"] = "Salvataggio avvenuto con successo";

                return $this->redirect(['view', 'catid' => $model->catid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        $model->scenario = "insert";

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Categories model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $catid Catid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($catid)
    {
        $model = $this->findModel($catid);

        $old_img = $model->photo;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {

                $model->photo = UploadedFile::getInstance($model, 'photo');
                if(!empty($model->photo)){
                    $model->uploadImage();
                } else {
                    $model->photo = $old_img;
                }

                $model->save();

                $_SESSION["success"] = "Salvataggio avvenuto con successo";

                return $this->redirect(['view', 'catid' => $model->catid]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Categories model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $catid Catid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($catid)
    {
        $this->findModel($catid)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Categories model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $catid Catid
     * @return Categories the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($catid)
    {
        if (($model = Categories::findOne(['catid' => $catid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
