<?php

/**
 * Class mhOperation
 */
class mhOperation
{
    private $con;

    /**
     * mhOperation constructor.
     */
    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new dbConnect();
        $this->con = $db->connect();
    }

    /**
     * Получение всех сотрудников клиники
     * @return array
     */
    public function getAllDoctors()
    {
        $stmt = $this->con->prepare("SELECT d.id, d.ФИО, s.id_спец
                                            FROM сотрудники_инф AS d, сотрудники_сциальность AS s WHERE d.id = s.id_сотр");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();
        $response["error"] = false;
        $response["message"] = REQUEST_OK;
        $response["response"] = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_doctor"] = $row["id"];
            $temp["full_name"] = $row["ФИО"];
            $temp["id_spec"] = $row["id_спец"];

            $stmt = $this->con->prepare("SELECT Специальности FROM специальности WHERE id = ?");
            $stmt -> bind_param('i', $row["id_спец"]);
            $stmt->execute();

            $temp["specialty"] = $row["Специальности"];
            array_push($response['response'], $temp);
        }
        return $response;
    }

    /**
     * Получение доктора id
     * @param $id_doctor
     * @return mixed
     */
    public function getDoctorById($id_doctor)
    {
        $stmt = $this->con->prepare("SELECT d.id, d.ФИО FROM сотрудники_инф AS d WHERE s.id = ?");
        $stmt->bind_param("i", $id_doctor);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_doctor"] = $row["id"];
            $temp["full_name"] = $row["ФИО"];

            array_push($response['response'], $temp);
        }
        return $response;
    }

    /**
     * Получение списка докторов по услуге
     * @param $id_service
     * @return mixed
     */
   public function getDoctorByService($id_service)
    {
        $stmt = $this->con->prepare("SELECT d.id, d.ФИО, s.id_специал FROM сотрудники_инф AS d, сотрудники_услуги AS s 
                                            WHERE s.id_услуги = ? AND d.id = s.id_сотрудника");
        $stmt->bind_param("i", $id_service);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_doctor"] = $row["id"];
            $temp["full_name"] = $row["ФИО"];
            $temp["id_spec"] = $row["id_специал"];

            $stmt = $this->con->prepare("SELECT Специальности FROM специальности WHERE id = ?");
            $stmt -> bind_param('i', $row["id_специал"]);
            $stmt->execute();

            $temp["specialty"] = $row["Наименование"];
            array_push($response['response'], $temp);
        }
        return $response;
    }

    /**
     * Получение списка сотрудников по специальности
     * @param $id_spec
     * @return array
     */
    public function getDoctorBySpec($id_spec)
    {
        $stmt = $this->con->prepare("SELECT * FROM  doctors WHERE id_centr=? AND specialty=?");
        $stmt->bind_param("i", $id_spec);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp['id_doctor'] = $row['id_doctor'];
            $temp['id_doc_center'] = $row['id_doc_centr'];
            $temp['id_center'] = $row['id_centr'];
            $temp['full_name'] = $row['fullname'];
            $temp['photo'] = $row['photo'];
            $temp['expr'] = $row['expr'];
            $temp['info'] = $row['info'];
            $temp['specialty'] = $row['specialty'];
            $temp['username'] = $row['username'];
            $temp['fb_key'] = $row['fb_key'];
            $temp['token'] = $row['token'];
            array_push($response, $temp);
        }
        return $response;
    }

    /**
     * Получение списка специальностей центра
     * @return array
     */
    public function getAllSpecialty()
    {
        $stmt = $this->con->prepare("SELECT id, Специальности FROM специальности");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();
        $response['error'] = false;
        $response['message'] = REQUEST_OK;
        $response['response'] = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_spec"] = $row["id"];
            $temp["title"] = $row["Специальности"];
            array_push($response['response'], $temp);
        }
        return $response;
    }

    /**
     * Получение списка специальностей по id доктора
     * @param $id_doctor
     * @return array
     */
    public function getSpecialtyByDoctor($id_doctor)
    {
        $stmt = $this->con->prepare("SELECT d.id_спец, s.title FROM сотрудники_специальность AS d, специальности AS s 
                                              WHERE d.id_сотр =? AND d.id_спец = s.id GROUP BY d.id_spec");
        $stmt->bind_param("i", $id_doctor);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_spec"] = $row["id"];
            $temp["title"] = $row["title"];
            array_push($response, $temp);
        }
        return $response;
    }

    /**
     * Получение списка услуг центра
     * @return array
     */
    public function getAllService()
    {
        $stmt = $this->con->prepare("SELECT * FROM прейскурант");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();
        $response['error'] = false;
        $response["message"] = REQUEST_OK;
        $response['response'] = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_service"] = $row["id_услуги"];
            $temp["id_spec"] = $row["id_специал"];
            $temp["admission"] = $row["Время _приема"];
            $temp["value"] = $row["Цена"];
            $temp["title"] = $row["Наименование"];
            array_push($response["response"], $temp);
        }
        return $response;
    }

    /**
     * Получение списка услуг специалиста
     * @param $id_doctor
     * @return array
     */
    public function getServiceByDoctor($id_doctor)
    {
        $stmt = $this->con->prepare("SELECT * FROM `прейскурант` AS p, сотрудники_услуги AS s WHERE
                                            s.id_сотрудника =? AND p.id_услуги = s.id_услуги GROUP BY p.id_услуги");
        $stmt->bind_param("i", $id_doctor);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_service"] = $row["id_услуги"];
            $temp["id_spec"] = $row["id_специал"];
            $temp["admission"] = $row["Время _приема"];
            $temp["value"] = $row["Цена"];
            $temp["title"] = $row["Наименование"];
            array_push($response["response"], $temp);
        }
        return $response;
    }

    /**
     * Получение списка услуг по специальности
     * @param $id_spec
     * @return array
     */
    public function getServiceBySpecialty($id_spec)
    {
        $stmt = $this->con->prepare("SELECT * FROM прейскурант WHERE id_специал=?");
        $stmt->bind_param("i", $id_spec);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();
        $response['error'] = false;
        $response["message"] = REQUEST_OK;
        $response['response'] = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_service"] = $row["id_услуги"];
            $temp["id_spec"] = $row["id_специал"];
            $temp["admission"] = $row["Время _приема"];
            $temp["value"] = $row["Цена"];
            $temp["title"] = $row["Наименование"];
            array_push($response["response"], $temp);
        }
        return $response;
    }

    /**
     * Получение записей о приеме по расписанию врача
     * @param $date
     * @param $id_doctor
     * @param $adm
     * @return array|string
     */
    public function getRecordByDate($date, $id_doctor, $adm)
    {
        $stmt = $this->con->prepare("SELECT `Время_приема`,`id_клиента` FROM `raspisanie_sotr`
                                            WHERE `Дата_приема` = ? AND `id_сотрудника` = ? ORDER BY `Время_приема` ASC");
        $stmt->bind_param("si", $date, $id_doctor);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["adm_time"] = $row["Время_приема"];
            $temp["id_client"] = $row["id_клиента"];
            array_push($response, $temp);
        }

        if (count($response) > 0)
        {
            $reception_time = array();
            $reception_status = array();

            if ($adm != 0)
            {
                foreach ($response as $item)
                {
                    array_push($reception_time, $item["adm_time"]);
                    if ($item['id_client'] == 0)
                    {
                        $item['status'] = 'нет записи';
                        array_push($reception_status, $item['status']);
                    } else
                    {
                        $item['status'] = 'занято';
                        array_push($reception_status, $item['status']);
                    }
                }
            }

            $free_time = array();

            if ($adm != 0)
            {
                for ($i = 0; $i < count($reception_time) - 1; $i++)
                {
                    $timeStart = $reception_time[$i];
                    $clientStart = $reception_status[$i];
                    $timeNext = $reception_time[$i + 1];

                    if ($clientStart == "нет записи")
                    {
                        $k = 1;

                        while ($k != 0)
                        {
                            $start_time = date_create_from_format("H:i", $timeStart);
                            $next_time = date_create_from_format("H:i", $timeNext);
                            $new_time = date_modify($start_time, $adm . ' min');

                            if ($new_time <= $next_time)
                            {
                                array_push($free_time, $timeStart);
                                $timeStart = $new_time->format('H:i');
                            } else
                            {
                                $k = 0;
                            }
                        }
                    }
                }
            }
            return $free_time;
        }
        return NO_WORK;
    }

    /**
     * Запись пациента на прием
     * @param $id_sotr
     * @param $data
     * @param $time_zap
     * @param $id_kl
     * @param $id_spec
     * @param $id_ysl
     */
    public function recordClient($id_sotr, $data, $time_zap, $id_kl, $id_spec, $id_ysl, $dlit)
    {
        $stmt = $this->con->prepare("SELECT COUNT(Время_приема) AS adm_time FROM `raspisanie_sotr` WHERE `Дата_приема` = ? 
                                      AND `Время_приема` = ? AND `id_сотрудника` = ?");
        $stmt->bind_param("sss", $data, $time_zap, $id_sotr);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $prov2 = $result->fetch_assoc();

        $stmt = $this->con->prepare("SELECT `id_клиента` FROM `raspisanie_sotr` 
                                            WHERE `Дата_приема` = ? AND `Время_приема` = ? AND `id_сотрудника` = ?");
        $stmt->bind_param("sss", $data, $time_zap, $id_sotr);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $prov3 = $result->fetch_assoc();

        if ($prov2 == "1" && $prov3 != "0")
        {
            var_dump("На это время уже записан другой пациент");
        } else
        {
            $day_today = date("d.m.Y");;
            $day_zapis = $data;

            $day_today = date('d.m.Y', strtotime("$day_today"));
            $day_zapis = date('d.m.Y', strtotime("$day_zapis"));

            if ($day_today == $day_zapis)
            {
                $obzvon = "mobile";
            } else
            {
                $obzvon = "нет";
            }
            if ($prov2 == "0")
            {
                $stmt = $this->con->prepare("INSERT INTO `raspisanie_sotr`(id_сотрудника,
                                                    Дата_приема,Время_приема,id_клиента,Статус_приема,id_специал,
                                                    id_услуги,obzvon) VALUES(?,?,?,?,'wk',?,?,?)");
                $stmt->bind_param("sssssss", $id_sotr, $data, $time_zap, $id_kl, $id_spec, $id_ysl, $obzvon);
                $stmt->execute();
                $stmt->close();
            } else if ($prov2 == "1" && $prov3 == "0")
            {
                $stmt = $this->con->prepare("UPDATE `raspisanie_sotr` SET `id_клиента` =?,`Статус_приема`='wk',
                                                    `id_услуги` =?, `obzvon` =?,`id_специал` =? 
                                                    WHERE `Дата_приема` =? AND `Время_приема` =? AND `id_сотрудника` =?");
                $stmt->bind_param("sssssss", $id_kl, $id_ysl, $obzvon, $id_spec, $data, $time_zap, $id_sotr);
                $stmt->execute();
                $stmt->close();
            }

            $konec_time = DateTime::createFromFormat('H:i', $time_zap);
            $konec_time->modify('+' . $dlit . 'minutes');
            echo $konec_time->format('H:i');

            $stmt = $this->con->prepare("SELECT COUNT(Время_приема) FROM `raspisanie_sotr`
                                                                          WHERE `Дата_приема` = ? AND `Время_приема` = ?
                                                                          AND `id_сотрудника` = ?");
            $stmt->bind_param("sss", $data, $konec_time, $id_sotr);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $prov_time = $result->fetch_assoc();

            if ($prov_time == "0") //если времени такого нет, то добавляем
            {
                $stmt = $this->con->prepare("INSERT INTO `raspisanie_sotr`
                                                    (id_сотрудника,Дата_приема,Время_приема,
                                                    id_клиента,Статус_приема,id_специал,id_услуги,
                                                    obzvon) VALUES(?,?,?,'0','wk','0','0','нет')");
                $stmt->bind_param("sss", $id_sotr, $data, $konec_time);
                $stmt->execute();
                $stmt->close();
            }

//            проверяем есть ли между ними свободное время которое надо удалить

            $stmt = $this->con->prepare("SELECT `Время_приема` FROM `raspisanie_sotr` WHERE `Дата_приема` = ? 
                                                AND `id_сотрудника` = ? AND `id_клиента` = '0'");
            $stmt->bind_param("ss", $data, $id_sotr);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            $response = array();

            while ($row = $result->fetch_assoc())
            {
                $temp = array();
                $temp["adm_time"] = $row["Время_приема"];
                array_push($response, $temp);
            }

            for ($i = 0; $i < count($response); $i++)
            {
                //то что думаем удалить
                $chasDel = substr($response[$i], 0, 2);
                $minytDel = substr($response[$i], 3, 2);
                $vremiaDel = $chasDel . ':' . $minytDel;

                $del = date('H:i', strtotime($vremiaDel));

                //начальное-то куда записываем на прием nach_time;
                //конечное-то что создали после записи konec_time;
                //удаляем если между

                $nach_time = date('H:i', strtotime($time_zap));

                if ($del > $nach_time && $del < $konec_time)
                {
                    $stmt = $this->con->prepare("DELETE FROM `raspisanie_sotr` WHERE `Дата_приема` = ? 
                                                        AND `id_сотрудника` = ? AND `Время_приема` = ?");
                    $stmt->bind_param("sss", $data, $id_sotr, $response[$i]);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }

    /**
     * Получение списка посещений по id_клиента
     * @param $id_клиента
     * @return array
     */
    public function getVisitsByClient($id_клиента)
    {
        $stmt = $this->con->prepare("SELECT * FROM raspisanie_sotr WHERE id_клиента=? ORDER BY Статус_приема");
        $stmt->bind_param("i", $id_клиента);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $response = array();
        $response['error'] = false;
        $response["message"] = REQUEST_OK;
        $response['response'] = array();

        while ($row = $result->fetch_assoc())
        {
            $temp = array();
            $temp["id_doctor"] = $row["id_сотрудника"];
            $temp["id_client"] = $row["id_клиента"];
            $temp["id_service"] = $row["id_услуги"];
            $temp["id_specialty"] = $row["id_специал"];
            $temp["adm_date"] = $row["Дата_приема"];
            $temp["adm_time"] = $row["Время_приема"];
            $temp["status"] = $row["Статус_приема"];
            $temp["call"] = $row["obzvon"];
            array_push($response["response"], $temp);
        }
        return $response;
    }

    /**
     * Получение даты с сервера
     * @return array
     */
    public function getCurrentDate()
    {
        $date = date("d.m.Y");
        $day = date("w");
        if ($day == 1)
        {
            $monday = $day;
        } else
        {
            $monday = date('d.m.Y', strtotime("last Monday"));
        }
        $message = [
            "today" => $date,
            "week_day" => $day,
            "last_monday" => $monday
        ];
        return $message;
    }
}
