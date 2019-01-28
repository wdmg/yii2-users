# Usecase

Here are some examples of controller actions for interacting with module models.

Login and logout action in controller:
 
        use wdmg\users\models\UsersSignin;
        ...
        
        public function actionLogin()
        {
            if (!Yii::$app->user->isGuest)
                return $this->goHome();
        
            $model = new UsersSignin();
            if ($model->load(Yii::$app->request->post())) {
                try {
        
                    if($model->login())
                        return $this->goBack();
        
                } catch (\DomainException $error) {
                    Yii::$app->session->setFlash('error', $error->getMessage());
                    return $this->goHome();
                }
            }
        
            return $this->render('login', [
                'model' => $model,
            ]);
        }
        
        public function actionLogout()
        {
            Yii::$app->user->logout();
            return $this->goHome();
        }

Signup and confirmation by email (if you set option `needConfirmation = true`) action in controller:
    
        use wdmg\users\models\UsersSignup;
        ...
        
        /**
         * Action to displays the user registration form.
         *
         * @return Response|string
         */
        public function actionSignup()
        {
            $model = new UsersSignup();
            $module = Yii::$app->getModule('users', false);
        
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
        
                try {
                    if ($user = $model->signup()) {
        
                        if ($module->options["signupConfirmation"]["needConfirmation"])
                            Yii::$app->session->setFlash('success', 'Check your email to confirm the registration.');
                        else
                            Yii::$app->getUser()->login($user);
        
                        return $this->goHome();
                    }
                } catch (\DomainException $error) {
                    Yii::$app->session->setFlash('error', $error->getMessage());
                    return $this->goHome();
                }
            }
        
            return $this->render('signup', [
                'model' => $model,
            ]);
        }
        
        /**
         * Action to checks the confirmation token from the letter and activates the user.
         *
         * @return Response|string
         */
        public function actionSignupConfirm($token)
        {
            $model = new UsersSignup();
            try {
                $model->userConfirmation($token);
                Yii::$app->session->setFlash('success', 'You have successfully confirm your registration.');
            } catch (\Exception $error) {
                Yii::$app->errorHandler->logException($error);
                Yii::$app->session->setFlash('error', $error->getMessage());
            }
        
            return $this->goHome();
        }


Password request action controller:

        use wdmg\users\models\UsersResetPassword;
        use wdmg\users\models\UsersPasswordRequest;
        ...
        
        /**
         * Action to display email form and checks the existence of the user at the email address and sends an email with a link to recover the password.
         *
         * @return Response|string
         */
        public function actionPasswordRequest()
        {
            $model = new UsersPasswordRequest();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->sendEmail()) {
                    Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
                    return $this->goHome();
                } else {
                    Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
                }
            }
            return $this->render('requestPassword', [
                'model' => $model,
            ]);
        }
        
        /**
         * Action to checks the password recovery token from the letter and displays the password change form.
         *
         * @return Response|string
         */
        public function actionResetPassword($token)
        {
            try {
                $model = new UsersResetPassword($token);
            } catch (InvalidArgumentException $e) {
                throw new BadRequestHttpException($e->getMessage());
            }
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
                Yii::$app->session->setFlash('success', 'New password saved.');
                return $this->goHome();
            }
            return $this->render('resetPassword', [
                'model' => $model,
            ]);
        }
