<?php

namespace wdmg\users\controllers;

use Yii;
use wdmg\users\models\Users;
use wdmg\users\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ]
        ];

        // If auth manager not configured use default access control
        if (!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();

        if (Yii::$app->user->can('admin'))
            $model->scenario = Users::USR_UPDATE_OR_CREATE_PASSWD;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if ($model->save()) {

                // Log activity
                $this->module->logActivity(
                    'User `' . $model->username . '` with ID `' . $model->id . '` has been successfully added.',
                    $this->uniqueId . ":" . $this->action->id,
                    'success',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app/modules/users', 'New user has been successfully added!')
                );
            } else {

                // Log activity
                $this->module->logActivity(
                    'An error occurred while add the user: ' . $model->username,
                    $this->uniqueId . ":" . $this->action->id,
                    'danger',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app/modules/users', 'An error occurred while add the new user.')
                );
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->id == Yii::$app->user->id || Yii::$app->user->can('admin'))
            $model->scenario = Users::USR_UPDATE_OR_CREATE_PASSWD;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {

                // Log activity
                $this->module->logActivity(
                    'User `' . $model->username . '` with ID `' . $model->id . '` has been successfully updated.',
                    $this->uniqueId . ":" . $this->action->id,
                    'success',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t('app/modules/users', 'User has been successfully updated!')
                );
            } else {

                // Log activity
                $this->module->logActivity(
                    'An error occurred while updating the user: ' . $model->username,
                    $this->uniqueId . ":" . $this->action->id,
                    'danger',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t('app/modules/users', 'An error occurred while updating the user.')
                );
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $rest = false)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        }

		if ($rest)
            throw new NotFoundHttpException(Yii::t('app/modules/users', 'The requested user does not exist.'));
		else
            throw new NotFoundHttpException(Yii::t('app/modules/users', 'The requested page does not exist.'));
    }
}
