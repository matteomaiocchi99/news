<?php

namespace backend\controllers;

use backend\models\Users;
use backend\models\UsersSearch;
use mysql_xdevapi\Result;
use Yii;
use yii\debug\models\timeline\Search;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete', 'view', 'validate', 'reactive', 'search'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                //Callback ci dice cosa fare se queste regole falliscono
                'denyCallback' => function ($rule, $data) {
                    $this->redirect(['/site/index']);
                }
            ],
        ];
    }

    /**
     * Lists all Users models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * Displays a single Users model.
     * @param int $userid Userid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($userid)
    {
        return $this->render('view', [
            'model' => $this->findModel($userid),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Users();

        if ($model->load(Yii::$app->request->post()) && $model->passwordToMd5($model->userid) && $model->save()) { //qui salviamo tutto quello che ci arriva nel $model

            $model->save();

            $_SESSION['success'] = 'Salvataggio avvenuto con successo';

            if (!empty($_POST["save-add"])) {
                return $this->redirect(["create"]);
            }

            return $this->redirect(['view', 'userid' => $model->userid]);
        }

        $model->scenario = 'insert';
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $userid Userid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($userid)
    {
        $model = $this->findModel($userid);

        if ($model->load(Yii::$app->request->post()) && $model->passwordToMd5($model->userid) && $model->save()) { //qui salviamo tutto quello che ci arriva nel $model

            $model->save();

            $_SESSION['success'] = 'Modifica avvenuto con successo';

            if (!empty($_POST["save-add"])) {
                return $this->redirect(["create"]);
            }

            return $this->redirect(['view', 'userid' => $model->userid]);
        }

        $model->passwd = '';

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $userid Userid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($userid)
    {
        $model = $this->findModel($userid);
        $model['status'] = 0;

        if ($model->passwordToMd5($model->userid) && $model->save()) { //qui salviamo tutto quello che ci arriva nel $model
            $model->save();
            $_SESSION['success'] = 'Utente disattivato';
            return $this->redirect(['view', 'userid' => $model->userid]);
        }

        return $this->redirect(['index']);
    }

    public function actionReactive($userid)
    {
        $model = $this->findModel($userid);
        $model['status'] = 1;

        if ($model->passwordToMd5($model->userid) && $model->save()) { //qui salviamo tutto quello che ci arriva nel $model
            $model->save();
            $_SESSION['success'] = 'Utente attivato';
            return $this->redirect(['view', 'userid' => $model->userid]);
        }

        return $this->redirect(['index']);
    }

    public function actionSearch()
    {

        //Yii::$app->response->format = Response::FORMAT_JSON;
        /*$results = Users::getAll(['dataTables' => 1]);
        $i = 0;

        foreach ($results as $result) {
            $array[$i] =
                    [
                        $this->findModel($result)->name,
                        $this->findModel($result)->surname,
                        $this->findModel($result)->email,
                        $this->findModel($result)->roleidfk,
                        $this->findModel($result)->status,
            ];
            $i++;
        }*/
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $userid Userid
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($userid)
    {
        if (($model = Users::findOne(['userid' => $userid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
