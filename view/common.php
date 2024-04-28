<?php

function render_header()
{
    ob_start();
    include_once dirname(__DIR__)."/view/layouts/header.php";
    $headerContent = ob_get_clean();
    return $headerContent;
}

function render_footer()
{
    ob_start();
    include_once dirname(__DIR__)."/view/layouts/footer.php";
    $footerContent = ob_get_clean();
    return $footerContent;
}

//-Abon

function getMethod()
{
    return strtolower($_SERVER['REQUEST_METHOD']);
}

function isGet()
{
    return getMethod() === 'get';
}

function isPost()
{
    return getMethod() === 'post';
}


function getBody()
{
    $data = [];
    if (isGet()) {
        foreach ($_GET as $key => $value) {
            $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
    }
    if (isPost()) {
        foreach ($_POST as $key => $value) {
            $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }
    }
    return $data;
}

function upload_profile_image()
{
    if (isset($_FILES['image'])){
        //there is 1 file to be uploaded
        $folder = '/uploads/';		//You MUST create a folder in your SERVER Directory
        $tmpfile = $_FILES['image']['tmp_name'];
        $filename = basename($_FILES['image']['name']);
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . $folder;	//the file inside your web root folder
        $target_file = $target_dir . $filename;

        $result_upload_file = move_uploaded_file($tmpfile, $target_file);
        if($result_upload_file) return $folder.$filename;
    }
    return;
}

function upload_question_image()
{
    var_dump($_FILES);
    if (isset($_FILES['image'])){
        //there is 1 file to be uploaded
        $folder = '/uploads/question/';		//You MUST create a folder in your SERVER Directory
        $tmpfile = $_FILES['image']['tmp_name'];
        $filename = basename($_FILES['image']['name']);
        $target_dir = $_SERVER['DOCUMENT_ROOT'] . $folder;	//the file inside your web root folder
        $target_file = $target_dir . $filename;

        $result_upload_file = move_uploaded_file($tmpfile, $target_file);
        if($result_upload_file) return $folder.$filename;
    }
    return;
}

function redirect($url) {
    return header("Location: $url");
}


class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function __destruct()
    {
        $this->removeFlashMessages();
    }

    private function removeFlashMessages()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}

function login($user)
{
    $session = new Session();
    $session->set('user', $user->id);
    $session->set('isAdmin', $user->isAdmin);
    $session->setFlash("signin-success", "You logined successfully with email=$user->email");
}

function logout()
{
    $session = new Session();
    $session->remove('user');
    $session->remove('isAdmin');
    $session->setFlash("signout-success", "You logged out successfully");
    redirect("/index.php");
}
?>