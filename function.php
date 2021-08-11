<?php

/*---------------------Регистрация---------------------*/
/**
get_user_by_email

Parameters: string - email.
Description: искать пользователя по email.
Return value: array.
 */
function get_user_by_email($email){
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");

//  готовлю запрос в БД
    $sql = "SELECT * FROM users WHERE email=:email";

//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);

//  выполняю запрос в БД
    $statement->execute(["email" => $email,]);

// возвращаю данные (объект) в виде ассоциативного массива и сохраняю в переменную.
    $user = $statement->fetch(PDO::FETCH_ASSOC);

// на выходе из функции возвращаю переменную
    return $user;
}

/**
set_flash_message

Parameters: string - name (ключ);
string - message.
Description: записать в сессию значение сообщения по ключу.
Return value: null.
 */
function set_flash_message($block_style,$message) {
//в глобальный объект SESSION (ассоциативный массив) записываю новые данные "ключ-значение".
    $_SESSION['block_style'] = $block_style;
    $_SESSION['message'] = $message;
}

/**
display_flash_message

Parameters: string - name.
Description: вывести сообщение.
Return value: null.
 */
function display_flash_message() {
    // условие, что если в глобальном массиве SESSION существует ключ со значением "name"
    if(isset($_SESSION['block_style'])) {
        // вывожу сообщение ввиде HTML с использованием классов Bootstrap
        echo "<div class=\"alert alert-{$_SESSION['block_style']} text-dark\" role=\"alert\">{$_SESSION['message']}</div>";
        // удаляю "ключ-значение" в глобально массиве SESSION по ключу "name" с использованием стандарной фукнции "unset"
        unset($_SESSION['block_style']);
        unset($_SESSION['message']);
    }
}


/**unset_flash_message

Parameters: string - name (ключ);
string - message.
Description: записать в сессию значение сообщения по ключу.
Return value: null.
 */
function unset_flash_message() {
//в глобальный объект SESSION (ассоциативный массив) записываю новые данные "ключ-значение".
    unset($_SESSION['block_style']);
    unset($_SESSION['message']);
}

/**
redirect_to

Parameters: string - path.
Description: перенаправить на другую страницу.
Return value: null.
 */
function redirect_to($path) {
//помещаю функцию header в свою с более понятным названием и более кратким написанием (без "Location")
    header("Location: {$path}");
    exit;
}

/**
add_user

Parameters: string - email;
string - password.
Description: добавить пользователя в БД.
Return value: int - (user_id).

 */
function add_user($email, $password){
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");
//  готовлю запрос в БД
    $sql = "INSERT INTO users (email, password) VALUES (:email, :password)";
//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);
//  выполняю запрос в БД
    $statement->execute([
        "email" => $email,
        "password" => password_hash($password,PASSWORD_DEFAULT),
    ]);
// на выходе из функции подключаюс к базе и возвращаю id добавленного пользователя
    return $pdo->lastInsertId();
}

/*---------------------Авторизация---------------------*/

/**
login

Parameters: string - email;
string - password.
Description: авторизовать пользователя.
Return value: boolean.
 */
function login($email, $password) {
//получаем по email все данные пользователя  в виде ассоциативного массива из базы и помещаем его в переменную
    $user_from_db = get_user_by_email($email);
//получаем пароль из базы данных в виде хеша и помещаем его в переменную
    $password_from_db = $user_from_db ['password'];
//при помощи стандартной функции PHP сравниванием пароль, который ввел пользователь при авторизации и пароль,
// который хранится в базе
    $result = password_verify($password, $password_from_db);
    return $result;
}

/**
set_user_in_session_by_email

Parameters: string - email.
Description: записать данные пользователя в сессию.
Return value: null.
 */
function set_user_in_session_by_email($email) {
    //получаем по email все данные пользователя  в виде ассоциативного массива из базы и помещаем его в переменную
    $user_from_db = get_user_by_email($email);

    //получаем id из базы данных и помещаем его в переменную
    $user_id = $user_from_db['id'];

    //записывает user_id и email в глобальный массив SESSION
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_email'] = $email;
}

/**
unset_user_in_session

Parameters: хранятся в сессии.
Description: удалить данные пользователя из сессии.
Return value: null.
 */
function unset_user_in_session() {
    //удаляю "ключ-значение" в глобально массиве SESSION по ключу с использованием стандарной фукнции "unset"
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
}

/*---------------------Список пользователей---------------------*/

/**
is_not_logged_in

Parameters: хранятся в сессии.
Description: вернуть true, если ползователеь не залогинен.
Return value: boolean.
 */
function is_not_logged_in() {
    $result = !isset($_SESSION['user_id']);
    return $result;
}

/**
user_role

Parameters: нет.
Description: определить роль пользователя.
Return value: string.
 */
function user_role($email) {
//получаем по email все данные пользователя  в виде ассоциативного массива из базы и помещаем его в переменную
    $user_from_db = get_user_by_email($email);

//получаем роль пользователя из базы данных и помещаем его в переменную
    $user_role = $user_from_db["role"];
    return $user_role;
}

/**
is_admin

Parameters: $user_role.
Description: определить является ли пользователь администратором.
Return value: boolean.
 */
function is_admin($email){
    //получаем по email все данные пользователя  в виде ассоциативного массива из базы и помещаем его в переменную
    $user_from_db = get_user_by_email($email);

    if($user_from_db["role"] === "admin"){
        return "true";
    } else {
        return "false";
    }
}

/**
display_create_user_button

Parameters: нет.
Description: вывести кнопку добавления пользователей для админа.
Return value: null.
 */
function display_create_user_button_for_admin($email) {
    //если админ
    if(is_admin($email) === "true") {
        // вывожу кнопку ввиде HTML
        echo "<a class=\"btn btn-success\" href=\"create_user.php\">Добавить</a>";
    }
}

/**
get_all_users

Parameters: нет.
Description: получить id, логин и пароль всех пользователей.
Return value:array.
 */
function get_all_users(){
    //  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");

    //  готовлю запрос в БД
    $sql = "SELECT * FROM users";
//    $sql = "SELECT * FROM users,general_info_about_users,media_about_users,social_about_users WHERE id=28";
//    $sql = "SELECT * FROM users,general_info_about_users WHERE id=28";



    //  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);

    //  выполняю запрос в БД
    $statement->execute();

    // возвращаю данные (объект) в виде ассоциативного массива и сохраняю в переменную.
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);

    // на выходе из функции возвращаю переменную
    return $users;

//    print_r($statement);
//    print_r($users[2]["id"]);
//    echo '<hr/>';
//    die;
};

/**
get_user_general_info

Parameters: нет.
Description: получить имя, организацию и телефоны всех пользователей.
Return value: array.
 */
function get_user_general_info($user_id){
    // подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");

    // готовлю запрос в БД
    $sql = "SELECT * FROM general_info_about_user WHERE user_id = :user_id";

    // подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);

    // выполняю запрос в БД
    $statement->execute(["user_id" => $user_id]);

    // возвращаю данные (объект) в виде ассоциативного массива и сохраняю в переменную.
    $users_general_info = $statement->fetchAll(PDO::FETCH_ASSOC);

    // на выходе из функции возвращаю переменную
     return $users_general_info;

};

/**
get_user_media_info

Parameters: нет.
Description: получить статус и аватарку пользователя.
Return value: array.
 */
function get_user_media_info($user_id){
    // подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");

    // готовлю запрос в БД
    $sql = "SELECT * FROM media_about_user WHERE user_id = :user_id";

    // подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);

    // выполняю запрос в БД
    $statement->execute(["user_id" => $user_id]);

    // возвращаю данные (объект) в виде ассоциативного массива и сохраняю в переменную.
    $user_media_info = $statement->fetchAll(PDO::FETCH_ASSOC);

    // на выходе из функции возвращаю переменную
    return $user_media_info;

};

/**
get_user_social_info

Parameters: $user_id.
Description: получить адреса социальных сетей пользователя.
Return value: array.
 */
function get_user_social_info($user_id){
    // подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");

    // готовлю запрос в БД
    $sql = "SELECT * FROM social_about_user WHERE user_id = :user_id";

    // подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);

    // выполняю запрос в БД
    $statement->execute(["user_id" => $user_id]);

    // возвращаю данные (объект) в виде ассоциативного массива и сохраняю в переменную.
    $user_social_info = $statement->fetchAll(PDO::FETCH_ASSOC);

    // на выходе из функции возвращаю переменную
    return $user_social_info;

};


/*---------------------Добавить пользователя---------------------*/

/**get_user_id_by_email

Parameters: $email.
Description: определить id пользователя.
Return value: string.
 */
function get_user_id_by_email($email) {
//получаем по email все данные пользователя  в виде ассоциативного массива из базы и помещаем его в переменную
    $user_from_db = get_user_by_email($email);

//получаем роль пользователя из базы данных и помещаем его в переменную
    $user_id = $user_from_db["id"];
    return $user_id;
}

/**
add_general_info_about_user

Parameters: string - user_name;
            string - job_title;
            string - phone;
            string - address.
Description: добавить общую информацию сетях в БД.
Return value: NULL.

 */
function add_general_info_about_user($user_name, $job_title, $phone, $address, $user_id){
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");
//  готовлю запрос в БД
    $sql = "INSERT INTO general_info_about_user (user_name, job_title, phone, address, user_id) VALUES (:user_name, :job_title, :phone, :address, :user_id)";
//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);
//  выполняю запрос в БД
    $statement->execute([
        "user_name" => $user_name,
        "job_title" => $job_title,
        "phone" => $phone,
        "address" => $address,
        "user_id" => $user_id,
    ]);
// на выходе из функции ничего не возвращаю
}

/**
add_social_info_about_user

Parameters: string - vk;
            string - telegram;
            string - instagram.
Description: добавить информацию о социальных сетях в БД.
Return value: NULL.

 */
function add_social_info_about_user($vk, $telegram, $instagram, $user_id){
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");
//  готовлю запрос в БД
    $sql = "INSERT INTO social_about_user (vk, telegram, instagram, user_id) VALUES (:vk, :telegram, :instagram, :user_id)";
//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);
//  выполняю запрос в БД
    $statement->execute([
        "vk" => $vk,
        "telegram" => $telegram,
        "instagram" => $instagram,
        "user_id" => $user_id,
    ]);
// на выходе из функции ничего не возвращаю
}

/**
upload_avatar

Parameters: string - user_id;
            file - image.
Description: обновить аватар.
Return value: NULL.

 */
function upload_avatar($user_id, $image){

}


/**
unset_user_in_post

Parameters: хранятся в POST.
Description: удалить данные пользователя из глобального массива POST.
Return value: null.
 */
function unset_user_in_post() {
    //удаляю "ключ-значение" в глобально массиве SESSION по ключу с использованием стандарной фукнции "unset"
    unset($_POST['email']);
    unset($_POST['password']);
    unset($_POST['user_name']);
    unset($_POST['job_title']);
    unset($_POST['phone']);
    unset($_POST['address']);
    unset($_POST['user_status']);
    unset($_POST['user_avatar']);
    unset($_POST['vk']);
    unset($_POST['telegram']);
    unset($_POST['instagram']);
}

/*---------------------Редактировать пользователя---------------------*/

/**
edit_general_info_about_user

Parameters: string - user_name;
            string - job_title;
            string - phone;
            string - address;
            string  - user_id.
Description: редактировать общую информацию о пользователе в БД.
Return value: NULL.

 */
function edit_general_info_about_user($user_name, $job_title, $phone, $address, $user_id){
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");
//  готовлю запрос в БД
    $sql = "UPDATE general_info_about_user SET user_name=:user_name,job_title=:job_title,phone=:phone,address=:address  WHERE user_id=:user_id";

//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);
//  выполняю запрос в БД
    $statement->execute([
        "user_name" => $user_name,
        "job_title" => $job_title,
        "phone" => $phone,
        "address" => $address,
        "user_id" => $user_id,
    ]);
// на выходе из функции ничего не возвращаю
}

/*---------------------Профиль пользователя---------------------*/

/**
get_user_by_id

Parameters: string - id.
Description: вернуть пользователя по id.
Return value: array.
 */
function get_user_by_id($id){
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");

//  готовлю запрос в БД
    $sql = "SELECT * FROM users WHERE id=:id";

//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);

//  выполняю запрос в БД
    $statement->execute(["id" => $id,]);

// возвращаю данные (объект) в виде ассоциативного массива и сохраняю в переменную.
    $user = $statement->fetch(PDO::FETCH_ASSOC);

// на выходе из функции возвращаю переменную
    return $user;
}

/*---------------------Профиль пользователя---------------------*/

/**
is_email_free

Parameters: $user_id.
Description: определить нет ли почты в базе.
Return value: boolen.
 */
function is_email_free($user_id,$input_email){
    //получаем по id данные пользователя, данные пользователя которого собираемся редактировать  из БД и помещаем в переменную
    $edit_user_from_db = get_user_by_id($user_id);
//    помещаем email редактируемого пользователя в переменную
    $edit_email_user_from_db = $edit_user_from_db["email"];

    // проверяем есть ли пользователь с такой почтой в базе, которую ввели в форме
    $email_in_db = get_user_by_email($input_email)["email"];

    // в базе нет пользователя, email которого совпадает с вводимым email-ом
    // или
    // если email редактируемого пользователя совпадает с вводимым email-ом

    if (!$email_in_db || $edit_email_user_from_db === $input_email ){
        return "true";

    } else {
        return "false";

    }
}

/**
edit_user

Parameters: string - email;
            string - id.
            string - password.
Description: редактировать email и пароль пользователя в БД.
Return value: NULL.

 */
function edit_user($email, $password, $id){
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");
//  готовлю запрос в БД
    $sql = "UPDATE users SET email=:email, password=:password WHERE id=:id";

//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);
//  выполняю запрос в БД
    $statement->execute([
        "email" => $email,
        "password"=>password_hash($password,PASSWORD_DEFAULT),
        "id" => $id,
    ]);
// на выходе из функции ничего не возвращаю
}


/*---------------------Установка статуса---------------------*/

/**
set_status

Parameters: string - user_id;
            string - status.
Description: редактировать пользователя в БД.
Return value: NULL.

 */
function set_status($user_id, $user_status) {
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");
//  готовлю запрос в БД
    $sql = "UPDATE media_about_user SET user_status=:user_status WHERE user_id=:user_id";

//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);
//  выполняю запрос в БД
    $statement->execute([
        "user_status" => $user_status,
        "user_id" => $user_id,
    ]);
// на выходе из функции ничего не возвращаю
};

/*---------------------Загрузка картинки---------------------*/

/**
has_image

  Parameters: $user_id - int;
              $image   - string.
 Description: проверяет имеется ли аватар у пользователя.
Return value: boolean.

 */
function has_image($user_id) {
//    заглушка
    $no_image = "no_image";

    // подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");

    // готовлю запрос в БД
    $sql = "SELECT user_avatar FROM media_about_user WHERE user_id = :user_id";

    // подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);

    // выполняю запрос в БД
    $statement->execute(["user_id" => $user_id]);

    // возвращаю данные (объект) в виде ассоциативного массива и сохраняю в переменную.
    $user_avatar_from_db = $statement->fetchAll(PDO::FETCH_ASSOC);

    // на выходе из функции возвращаю переменную
//    return $user_media_info;
    $has_avatar = $user_avatar_from_db[0]["user_avatar"];
    if ($has_avatar) {
        echo $has_avatar;
    } else {
        echo $no_image;
    }
};


/**
upload_avatar

  Parameters: $user_id - int;
              $image   - array.
 Description: загружает аватар.
Return value: null | boolean.

 */
function upload_name_avatar($user_id, $user_avatar) {
//  подключаюсь к БД
    $pdo = new PDO("mysql:host=localhost;dbname=my_project", "root","");
//  готовлю запрос в БД
    $sql = "UPDATE media_about_user SET user_avatar=:user_avatar WHERE user_id=:user_id";

//  подготоваливаю запрос в БД и помещаю его в переменную
    $statement = $pdo->prepare($sql);
//  выполняю запрос в БД
    $statement->execute([
        "user_avatar" => $user_avatar,
        "user_id" => $user_id,
    ]);
// на выходе из функции ничего не возвращаю

};