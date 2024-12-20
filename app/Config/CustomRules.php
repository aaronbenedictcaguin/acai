<?php

namespace Config;

use App\Models\AccountsModel;

class CustomRules
{
    /**
     * Checks if the inputted username or email exists in the database
     * @param mixed $value
     * @param mixed $params The field name to check. This is useful for when the rule is not in the field that needs to be checked
     * @param mixed $data
     * @return bool
     */
    public function name_email_exists($value, $params, $data) {
        $model = model(AccountsModel::class);

        $account = $model->getAccount($data[$params]);

        return $account !== null;
    }

    /**
     * Checks if the inputted email is unique
     * @param mixed $value
     * @return bool
     */
    public function unique_email($value) {
        $model = model(AccountsModel::class); // Initialise the model instance

        return $value === session()->get('email') || $model->getAccount($value) === null; // Return true if the email is the same as the current user's email or if the email is not found in the database
    }

    /**
     * Checks if the inputted username is unique
     * @param mixed $value
     * @return bool
     */
    public function unique_username($value) {
        $model = model(AccountsModel::class); // Initialise the model instance

        return $value === session()->get('username') || $model->getAccount($value) === null; // Return true if the username is the same as the current user's username or if the username is not found in the database
    }
    
    /**
     * Checks if the password is decent
     * @param mixed $value
     * @return bool
     */
    public function password_ok($value) {
        return (strlen($value) > 8 && preg_match("/^(?=.*\w)(?=.*\d)(?=.*\W)[\w\d\W]{8,}$/", $value)) || strlen($value) > 16;
    }

    /**
     * Checks if the password matches the current password of the inputted user (field name in $params). This is for login.
     * @param mixed $value
     * @param mixed $params
     * @param mixed $data
     * @return bool
     */
    public function correct_password($value, $params, $data) {
        $model = model(AccountsModel::class);

        if ($params === 'name-email') {
            $account = $model->getAccount($data[$params]);
        } else {
            $account = $model->getAccount(session()->get('username'));
        }

        return password_verify($value, $account['password']);
    }
}