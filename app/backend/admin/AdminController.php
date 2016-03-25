<?php namespace backend\admin;

//use Model;
use backend\DbFactory;
use webcitron\Subframe\Redirect;
use \webcitron\Subframe\Controller;
//use backend\classes\Session;

class AdminController extends Controller
{
    
    public static function GoAwayIfNotLogged () {
        $objSession = \backend\Session::getInstance('imagehost-admin');
        $numIsLogged = intval($objSession->getValue('admin_iamgehost_auth'));
        if (empty($numIsLogged) || $numIsLogged === 0) {
            Redirect::route('Login::login');
        }
        return parent::answer();
    }
    
    public static function all() {
        $db = DbFactory::getInstance();
        $stmt = $db->prepare('SELECT * FROM admins.account');
        $stmt->execute();
        $arrAdmins = $stmt->fetchAll();
        return parent::answer($arrAdmins);
    }
    
    public static function removeAccount($numId) {
        $db = DbFactory::getInstance();
        $stmt = $db->prepare('DELETE FROM admins.account WHERE id = :id');
        $stmt->execute(array(
            ':id' => $numId
        ));
        return parent::answer();
        
    }
    
    public static function getById($numId) {
        $db = DbFactory::getInstance();
        $stmt = $db->prepare('SELECT * FROM admins.account WHERE id = :id');
        $stmt->execute(array(
            ':id' => $numId
        ));
        $arrAdmin = $stmt->fetch();
        return parent::answer($arrAdmin);
    }
    
    public static function getByUsername($strUsername, $numIdException = 0) {
        $db = DbFactory::getInstance();
        $stmt = $db->prepare('SELECT * FROM admins.account WHERE username = :username AND id != :id');
        $stmt->execute(array(
            ':username' => $strUsername, 
            ':id' => $numIdException
        ));
        $arrAdmin = $stmt->fetch();
        return parent::answer($arrAdmin);
    }
    
    public static function getByEmail($strEmail, $numIdException = 0) {
        $db = DbFactory::getInstance();
        $strQ = "SELECT * FROM admins.account WHERE email = :email AND id != :id";
        $stmt = $db->prepare($strQ);
        $stmt->execute(array(
            ':email' => $strEmail, 
            ':id' => $numIdException
        ));
        $arrAdmin = $stmt->fetch();
        return parent::answer($arrAdmin);
    }
    
    
    public static function createAccount($strName, $strSurname, $strEmail, $strUsername, $strPasswordHash, $strRePasswordHash) {
        $arrErrors = array();
        
        if (empty($strName)) {
            $arrErrors['name'] = 'Nie podano imienia';
        }
        
        if (empty($strSurname)) {
            $arrErrors['surname'] = 'Nie podano nazwiska';
        }
        
        if (empty($strEmail)) {
            $arrErrors['email'] = 'Nie podano adresu e-mail';
        } else if (!filter_var($strEmail, FILTER_VALIDATE_EMAIL)) {
            $arrErrors['email'] = 'Niepoprawny format adresu e-mail';
        }
        
        if (empty($strUsername)) {
            $arrErrors['username'] = 'Nie podano nazwy użytkownika';
        }
        
        $boolPassPrevalid = true;
        if (empty($strPasswordHash) || $strPasswordHash === md5('')) {
            $arrErrors['password'] = 'Nie podano hasła';
            $boolPassPrevalid = false;
        }
        
        if (empty($strRePasswordHash) || $strRePasswordHash === md5('')) {
            $arrErrors['repassword'] = 'Nie potwierdzono hasła';
            $boolPassPrevalid = false;
        }
        
        if ($boolPassPrevalid === true && $strPasswordHash !== $strRePasswordHash) {
            $arrErrors['password'] = 'Podane hasła nie są jednakowe';
        }
        
        if (empty($arrReturn['arrErrors'])) {
            $arrOtherAdmin = self::getByUsername($strUsername);
            if (!empty($arrOtherAdmin['result'])) {
                $arrErrors['username'] = 'Ta nazwa użytkownika jest już używana';
            }
            
            $arrOtherAdmin = self::getByEmail($strEmail);
            if (!empty($arrOtherAdmin['result'])) {
                $arrErrors['email'] = 'Ten adres e-mail jest już używany';
            }
            
            if (empty($arrErrors)) {
                self::addAccount($strName, $strSurname, $strEmail, $strUsername, $strPasswordHash);
            }
        }
        
        return parent::answer(true, $arrErrors);
    }
    
    private static function addAccount($strName, $strSurname, $strEmail, $strUsername, $strPasswordHash) {
        
//        echo 'x'.$strName;
//        exit();
        $db = DbFactory::getInstance();
        $strQ = "INSERT INTO admins.account ( "
                    . "firstname, "
                    . "lastname, "
                    . "username, "
                    . "email, "
                    . "password"
                . " ) VALUES ("
                    . ":name, "
                    . ":surname, "
                    . ":username, "
                    . ":email, "
                    . ":password_hash"
                . ")";
        $stmt = $db->prepare($strQ);
        $stmt->execute(array(
            ':name' => $strName, 
            ':surname' => $strSurname, 
            ':username' => $strUsername, 
            ':email' => $strEmail, 
            ':password_hash' => $strPasswordHash
        ));
        return parent::answer(true);
    }
    
    public static function changeAccount($numId, $strName, $strSurname, $strEmail, $strUsername, $strPasswordHash, $strRePasswordHash) {
        $arrErrors = array();
        
        if ($strPasswordHash === md5('')) {
            $strPasswordHash = '';
        }
        if ($strRePasswordHash === md5('')) {
            $strRePasswordHash = '';
        }
        
        if (empty($strUsername)) {
            $arrErrors['username'] = 'Nie podano nazwy użytkownika';
        } else {
            $arrOtherAdmin = self::getByUsername($strUsername, $numId);
            if (!empty($arrOtherAdmin['result'])) {
                $arrErrors['username'] = 'Ta nazwa użytkownika jest już używana';
            }
        }
        
        if (!empty($strPasswordHash) && empty($strRePasswordHash)) {
            $arrErrors['repassword'] = 'Nie potwierdzono nowego hasła';
        } else if (empty($strPasswordHash) && !empty($strRePasswordHash)) {
            $arrErrors['password'] = 'Nie podano nowego hasła';
        } elseif ($strPasswordHash !== $strRePasswordHash) {
            $arrErrors['password'] = 'Podane hasła nie są jednakowe';
        }
        
        if (empty($strEmail)) {
            $arrErrors['email'] = 'Nie podano adresu e-mail';
        } else {
            $arrOtherAdmin = self::getByEmail($strEmail, $numId);
            if (!empty($arrOtherAdmin['result'])) {
                $arrErrors['email'] = 'Ten adres e-mail jest już używany';
            }
        }
        
        if (empty($arrErrors)) {
            $arrNewData = array(
                'email' => $strEmail, 
                'username' => $strUsername, 
                'name' => $strName, 
                'surname' => $strSurname
            );
            if (!empty($strPasswordHash)) {
                $arrNewData['password_hash'] = $strPasswordHash;
            }
            self::updateAccount($numId, $arrNewData);
        }
        
        return parent::answer(true, $arrErrors);
    }
    
    private static function updateAccount ($numId, $arrNewData) {
        $strQ = "UPDATE admins.account SET ";
        $arrValues = array();
        $arrValues[':id'] = $numId;
        $arrKeys = array();
        foreach ($arrNewData as $strField => $strValue) {
            $arrKeys[] = sprintf("%s = :%s", $strField, $strField);
            $arrValues[':'.$strField] = $strValue;
        }
        
        $strQ .= join(', ', $arrKeys)." WHERE id = :id";
        
        $db = DbFactory::getInstance();
        $stmt = $db->prepare($strQ);
        $stmt->execute($arrValues);
        return parent::answer(true);
    }
    
}