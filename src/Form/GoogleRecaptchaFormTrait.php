<?php

namespace User\Form;

use Cake\Core\Configure;
use Cake\Form\Schema;
use Cake\Validation\Validator;
use GoogleRecaptcha\Lib\Recaptcha2;

trait GoogleRecaptchaFormTrait
{
    protected function _buildRecaptchaSchema(Schema $schema): Schema
    {
        if (Configure::read('User.Recaptcha.enabled')) {
            $schema->addField('captcha', [
                'required' => false
            ]);
            $schema->addField('g-recaptcha-response', [
                'required' => false
            ]);
        }
        return $schema;
    }

    /**
     * @param \Cake\Validation\Validator $validator The validator instance
     * @return \Cake\Validation\Validator
     */
    protected function validationRecaptcha(Validator $validator): Validator
    {
        if (Configure::read('User.Recaptcha.enabled')) {
            $validator->add('captcha', 'valid_recaptcha2', [
                'rule' => function ($value, array $context) {
                    $googleRecaptchaResponse = $context['data']['g-recaptcha-response'] ?? null;
                    if (!$googleRecaptchaResponse) {
                        return __d('user', 'Captcha validation failed');
                    }

                    $secretKey = Configure::read('GoogleRecaptcha.secretKey', '');
                    if (!Recaptcha2::verify($secretKey, $googleRecaptchaResponse)) {
                        return __d('user', 'Captcha verification failed');
                    }

                    return true;
                }
            ]);
        }

        return $validator;
    }

//    /**
//     * @param \Cake\Validation\Validator $validator The validator instance
//     * @return \Cake\Validation\Validator
//     */
//    protected function validationRecaptcha(Validator $validator)
//    {
//        $validator
//            ->requirePresence('g-recaptcha-response')
//            ->notEmptyString('g-recaptcha-response', __d('user', 'Are you human?'))
//            ->add('g-recaptcha-response', 'recaptcha', [
//                'rule' => 'checkRecaptcha',
//                'provider' => 'form',
//                'message' => __d('user', 'Invalid captcha'),
//            ]);
//
//        return $validator;
//    }
//
//    /**
//     * Google Recaptcha Validation Rule
//     *
//     * @param mixed $value Check value
//     * @param mixed $context Check context
//     * @return bool|string
//     */
//    public function checkRecaptcha($value, $context)
//    {
//        try {
//            if (!Recaptcha2::verify(Configure::read('GoogleRecaptcha.secretKey'), $value)) {
//                return __d('user', 'Captcha verification failed');
//            }
//        } catch (\Exception $ex) {
//            return __d('user', 'Unable to verify reCAPTCHA. Please try again later');
//        }
//
//        return true;
//    }

}