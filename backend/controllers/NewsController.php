<?php

namespace backend\controllers;


use backend\models\News;
use backend\models\NewsSearch;
use backend\models\Users;
use eMail;
use phpDocumentor\Reflection\Types\This;
use yii\bootstrap\Modal;
use yii\debug\models\search\Debug;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use Yii;

/**
 * NewsController implements the CRUD actions for News model.
 */
class NewsController extends Controller
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
                        'actions' => ['index', 'create', 'update', 'delete', 'view', 'approve', 'reject'],
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
     * Lists all News models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single News model.
     * @param int $newsid Newsid
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($newsid)
    {
        return $this->render('view', [
            'model' => $this->findModel($newsid),
        ]);
    }



    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new News();

        $model->statusidfk = News::WAITING_APPROVAL;
        $model->writeridfk = $_SESSION["user"];

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->prepareToSave() && $model->save() ) {

                $model->image = UploadedFile::getInstance($model, 'image');
                if(!empty($model->image)){
                    $model->uploadImage();
                }

                News::createMail($model);

                $model->save();

                $_SESSION["success"] = "Salvataggio avvenuto con successo";

                if (!empty($_POST["save-add"])) {
                    return $this->redirect(["create"]);
                }

                //Yii::$app->session->setFlash("success","Salvataggio avvenuto con successo");
                //Yii::$app->session->getFlash("success");



                return $this->redirect(['view', 'newsid' => $model->newsid]);
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
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $newsid Newsid
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($newsid)
    {

        $model = $this->findModel($newsid);

        $model->statusidfk = News::WAITING_APPROVAL;

        $old_img = $model->image;

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->prepareToSave() && $model->save() ) {

                $model->image = UploadedFile::getInstance($model, 'image');
                if(!empty($model->image)){
                    $model->uploadImage();
                } else {
                    $model->image = $old_img;
                }

                News::createMail($model);

                $model->save();

                $_SESSION["success"] = "Salvataggio avvenuto con successo";

                if (!empty($_POST["save-add"])) {
                    return $this->redirect(["create"]);
                }



                return $this->redirect(['view', 'newsid' => $model->newsid]);
            }
        }



        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $newsid Newsid
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($newsid)
    {
        $this->findModel($newsid)->delete();

        return $this->redirect(['index']);
    }

    public function actionApprove($newsid)
    {

        $model = $this->findModel($newsid);
        $model->statusidfk = News::STATUS_PUBLIC;
        $model->supvisoridfk = $_SESSION['user'];


        $receiver = Users::getAll(['searchWriter' => $model->writeridfk])[0]->email;
        $subject = "La tua news è stata pubblicata!";
        $content = "La tua news '".$model->title."' è stata pubblicata da ".Users::getArrayForSelect()[$model->supvisoridfk];

        eMail::sendMail($receiver, $subject, $content);


        $model->save();
        $this->redirect(['view', 'newsid' => $newsid]);
    }

    public function actionReject($newsid)
    {
        $model = $this->findModel($newsid);
        $model->statusidfk = 1;

        $receiver = Users::getAll(['searchWriter' => $model->writeridfk])[0]->email;
        $subject = "La tua news è stata rifiutata.";
        $content = "La tua news è stata rifiutata da ".Yii::$app->user->identity->email;
        $content .= '<br>'."Apporta le modifiche consigliate e chiedi nuovamente l'approvazione: ".'<br><br>'.$_POST['RejectForm']['reject_motive'];

        eMail::sendMail($receiver, $subject, $content);

        $model->save();
        $this->redirect(['view', 'newsid' => $newsid]);
    }




    /**
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $newsid Newsid
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($newsid)
    {
        if (($model = News::findOne(['newsid' => $newsid])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
