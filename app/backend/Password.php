<?php namespace backend;

class Password
{

    /**
     * Hashujemy haslo
     * @param $password
     * @return array
     */
    public static function getPasswordHash($password)
    {
        return md5($password);
//        return array('password' => md5($password), 'salt' => '');
//        $salt = mcrypt_create_iv(22, MCRYPT_DEV_URANDOM);
//        $options = [
//            'cost' => 11,
//        ];
//        return array('password' => password_hash($password, PASSWORD_DEFAULT), 'salt' => '');
    }

    /**
     * Weryfikujemy haslo z hashem bazy danych
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function isVerifyPassword($password, $hash)
    {
        return ($password === $hash);
//        return password_verify($password, $hash);
    }

    /**
     * Generujemy losowe haslo dla potrzeb odzyskiwania hasla
     * @return array
     */
    public static function getGeneratePassword()
    {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        $pass = substr(str_shuffle(str_repeat($alphabet,8)),0,8);
//        for ($i = 0; $i < 8; $i++)
//        {
//            $n = rand(0, count($alphabet) - 1);
//            $pass[$i] = $alphabet[$n];
//        }
//        $pass = join('', $pass);
        return array('password' => self::getPasswordHash($pass), 'passwordText' => $pass);
    }
}