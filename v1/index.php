<?php

require_once '../include/MhOperation.php';
require_once '../include/VerifyOperation.php';
require '.././libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

/**
 * Фукция отображения ответа сервера
 * @param $status_code
 * @param $response
 */
function echoResponse($status_code, $response)
{
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json; charset=utf-8');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

/**
 * Проверка валидности параметров (проверяет пустое ли значение было передано в поле)
 * @param $required_fields
 */
function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    if ($_SERVER['REQUEST_METHOD'] == 'PUT')
    {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }

    foreach ($required_fields as $field)
    {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0)
        {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error)
    {
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Поля ' . substr($error_fields, 0, -2) . ' не должны быть пустыми';
        $response["response"] = " ";
        echoResponse(403, $response);
        $app->stop();
    }
}

/**
 * Аутентификация пользователя
 **/
function authenticate()
{
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();

    if (isset($headers['Authorization']))
    {
        $db = new verifyOperation();
        $api_key = $headers['Authorization'];
        if ($db->isHaveAccess($api_key) == false)
        {
            $response["error"] = true;
            $response["message"] = "Неверное значение ключа API";
            $response["response"] = " ";
            echoResponse(403, $response);
            $app->stop();
        }
    } else
    {
        $response["error"] = true;
        $response["message"] = "Пустое значение ключа API";
        $response["response"] = " ";
        echoResponse(401, $response);
        $app->stop();
    }
}

/**
 * Получение всех специальностей клиники
 * URL: http://localhost/api/v1/specialty
 * Authorization: API Key in Request Header
 * Method: GET
 **/
$app->get('/specialty', 'authenticate', function () use ($app)
{
    $db = new mhOperation();
    $result = $db->getAllSpecialty();

    if ($result != null)
    {
        echoResponse(200, $result);
    } else
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение всех специальностей по id доктора
 * URL: http://localhost/api/v1/specialty/doctor/:id_doctor
 * @param $id_doctor
 * Authorization: API Key in Request Header
 * Method: GET
 **/
$app->get('/specialty/doctor/:id_doctor', 'authenticate', function ($id_doctor) use ($app)
{
    $db = new mhOperation();
    $result = $db->getSpecialtyByDoctor($id_doctor);

    $response = array();

    if ($result != null && count($result) > 0)
    {
        $response['error'] = false;
        $response['message'] = REQUEST_OK;
        $response['response'] = $result;
        echoResponse(200, $response);
    } else
    {
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение списка сотрудников клиники
 * URL: http://localhost/api/v1/doctors/:id_center
 * Authorization: API Key in Request Header
 * Method: GET
 **/
$app->get('/doctors/:id_center', 'authenticate', function () use ($app)
{
    $db = new mhOperation();
    $result = $db->getAllDoctors();

    if ($result != null)
    {
        echoResponse(200, $result);
    } else
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение списка сотрудников по id услуги
 * URL: http://localhost/api/v1/doctors/service/:id_service
 * @param $id_service
 * Authorization: API Key in Request Header
 * Method: GET
 **/
$app->get('/doctors/service/:id_service', 'authenticate', function ($id_service) use ($app)
{
    $db = new mhOperation();
    $result = $db->getDoctorByService($id_service);

    if ($result != null)
    {
        echoResponse(200, $result);
    } else
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение списка сотрудников по специальности
 * URL: http://localhost/api/v1/doctors/specialty/:id_spec
 * @param $id_spec
 * Authorization: API Key in Request Header
 * Method: GET
 **/

$app->get('/doctors/specialty/:id_spec', 'authenticate', function ($id_spec) use ($app)
{
    $db = new mhOperation();
    $result = $db->getDoctorBySpec($id_spec);

    if ($result != null)
    {
        echoResponse(200, $result);
    } else
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение списка всех услуг медицинского центра
 * URL: http://localhost/api/v1/services
 * Authorization: API Key in Request Header
 * Method: GET
 **/
$app->get('/services', 'authenticate', function () use ($app)
{
    $db = new mhOperation();
    $result = $db->getAllService();

    if ($result != null)
    {
        echoResponse(200, $result);
    } else
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение списка услуг по id сотрудника
 * URL: http://localhost/api/v1/services/doctor/:id_doctor
 * @param $id_doctor
 * Authorization: API Key in Request Header
 * Method: GET
 **/
$app->get('/services/doctor/:id_doctor', 'authenticate', function ($id_doctor) use ($app)
{
    $db = new mhOperation();
    $result = $db->getServiceByDoctor($id_doctor);

    $response = array();

    if ($result != null || count($result) > 0)
    {
        $response['error'] = true;
        $response['message'] = REQUEST_OK;
        $response['response'] = $result;
        echoResponse(200, $response);
    } else
    {

        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение списка услуг по специальности
 * URL: http://localhost/api/v1/services/specialty/:id_spec
 * @param $id_spec
 * Authorization: API Key in Request Header
 * Method: GET
 **/
$app->get('/services/specialty/:id_spec', 'authenticate', function ($id_spec) use ($app)
{
    $db = new mhOperation();
    $result = $db->getServiceBySpecialty($id_spec);

    if ($result != null)
    {
        echoResponse(200, $result);
    } else
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение списка посещений медицинского центра
 * URL: http://localhost/api/v1/visits/:id_user'
 * @param $id_user
 * Authorization: API Key in Request Header
 * Method: PUT
 **/
$app->get('/visits/:id_user', 'authenticate', function ($id_user) use ($app)
{
    $db = new mhOperation();
    $result = $db->getVisitsByClient($id_user);

    if ($result != null)
    {
        echoResponse(200, $result);
    } else
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

/**
 * Получение расписания доктора
 * URL: http://localhost/api/v1/schedule/doctor/:id_center/:id_doctor/:date/:adm
 * Parameter: $id_doctor - id доктора в центе
 * Parameter: $id_service - id услуги в центре
 * Parameter: $adm - длительность приема
 * Authorization: API Key in Request Header
 * Method: GET
 **/

$app->get('/schedule/doctor/:id_doctor/:date/:adm', 'authenticate', function ($id_doctor, $date, $adm) use ($app)
{
    $db = new mhOperation();

    $week = array();

    $response = array();
    $response['error'] = false;
    $response['message'] = REQUEST_OK;
    $response['response'] = array();

    $result = $db->getDoctorById($id_doctor);

    $full_name = $result["response"][0]["full_name"];

    $day = 0;

    while ($day < 7)
    {
        $day++;
        $result = $db->getRecordByDate($date, $id_doctor, $adm);
        if ($result == NULL)
        {
            $week['id_doctor'] = $id_doctor;
            $week['full_name'] = $full_name;
            $week['is_work'] = true;
            $week['adm_day'] = $date;
            $week['adm_time'] = null;
            array_push($response['response'], $week);
        } else
        {
            if ($result == NO_WORK)
            {
                $week['id_doctor'] = $id_doctor;
                $week['full_name'] = $full_name;
                $week['is_work'] = false;
                $week['adm_day'] = $date;
                $week['adm_time'] = null;
                array_push($response['response'], $week);
            } else
            {
                $week['id_doctor'] = $id_doctor;
                $week['full_name'] = $full_name;
                $week['is_work'] = true;
                $week['adm_day'] = $date;
                $week['adm_time'] = $result;
                array_push($response['response'], $week);
            }
        }
        $next = date_create_from_format("d.m.Y", $date);
        $next = date_modify($next, '1 day');
        $date = $next->format('d.m.Y');
    }
    echoResponse(200, $response);
});

/**
 * Получение расписания по услуге
 * URL: http://localhost/api/v1/schedule/service/:id_service/:date/:adm
 * Parameter: $id_center - id центра
 * Parameter: $id_doctor - id доктора в центе
 * Parameter: $id_service - id услуги в центре
 * Parameter: $adm - длительность приема
 * Authorization: API Key in Request Header
 * Method: GET
 **/

$app->get('/schedule/service/:id_center/:id_service/:date/:adm', 'authenticate', function ($id_service, $date, $adm) use ($app)
{
    $db = new mhOperation();

    $week = array();

    $response = array();
    $response['error'] = false;
    $response['message'] = REQUEST_OK;
    $response['response'] = array();

    $result = $db->getDoctorByService($id_service);

    foreach ($result as $value)
    {
        $id_doctor = (string)$value['id_doctor'];
        $full_name = $value['full_name'];

        $day = 0;

        $date_cash = $date;

    while ($day < 7)
    {
        $day++;

        $result = $db->getRecordByDate($date_cash, $id_doctor, $adm);

        if ($result == NULL)
        {
            $week['id_doctor'] = $id_doctor;
            $week['full_name'] = $full_name;
            $week['is_work'] = true;
            $week['adm_day'] = $date_cash;
            $week['adm_time'] = null;
            array_push($response['response'], $week);
        } else
        {
            if ($result == NO_WORK)
            {
                $week['id_doctor'] = $id_doctor;
                $week['full_name'] = $full_name;
                $week['is_work'] = false;
                $week['adm_day'] = $date_cash;
                $week['adm_time'] = null;
                array_push($response['response'], $week);
            } else
            {
                $week['id_doctor'] = $id_doctor;
                $week['full_name'] = $full_name;
                $week['is_work'] = true;
                $week['adm_day'] = $date_cash;
                $week['adm_time'] = $result;
                array_push($response['response'], $week);
            }
        }
        $next = date_create_from_format("d.m.Y", $date_cash);
        $next = date_modify($next, '1 day');
        $date_cash = $next->format('d.m.Y');
    }
}
    echoResponse(200, $response);
});

/**
 * Запись на прием
 * URL: http://localhost/api/v1/record
 * Parameters: none
 * Authorization: API Key in Request Header
 * Method: GET
 */
$app->post('/record', 'authenticate', function () use ($app)
{
    verifyRequiredParams(array('id_sotr', 'data', 'time_zap', 'id_kl', 'id_spec', 'id_ysl', 'dlit'));
    $id_sotr = $app->request->post('$id_sotr');
    $data = $app->request->post('data');
    $time_zap = $app->request->post('time_zap');
    $id_kl = $app->request->post('id_kl');
    $id_spec = $app->request->post('id_spec');
    $id_ysl = $app->request->post('id_ysl');
    $dlit = $app->request->post('dlit');

    try
    {
        $db = new mhOperation();
        $db->recordClient($id_sotr, $data, $time_zap, $id_kl, $id_spec, $id_ysl, $dlit);
    } catch (Exception $e)
    {
        $response = array();
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = $e;
        echoResponse(500, $response);
    }
});

/**
 * Получение текущей даты с сервера
 * URL: http://localhost/api/v1/date
 * Parameters: none
 * Authorization: API Key in Request Header
 * Method: GET
 **/
$app->get('/date', 'authenticate', function () use ($app)
{
    $db = new mhOperation();
    $result = $db->getCurrentDate();
    $response = array();

    if ($result != NULL)
    {
        $response['error'] = false;
        $response['message'] = REQUEST_OK;
        $response['response'] = $result;
        echoResponse(200, $response);
    } else
    {
        $response['error'] = true;
        $response['message'] = EMPTY_DATA;
        $response['response'] = NULL;
        echoResponse(404, $response);
    }
});

$app->run();