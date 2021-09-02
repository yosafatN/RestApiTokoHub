<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Jakarta");
class MasterModel extends CI_Model
{
    public function updateActiveInactiveMember()
    {
        $this->db->query("UPDATE dojo SET dojo.active_member = (SELECT COUNT(*) FROM member WHERE status = 0 AND id_dojo = dojo.id_dojo), dojo.inactive_member = (SELECT COUNT(*) FROM member WHERE status = 1 AND id_dojo = dojo.id_dojo)");
    }

    public function getMaster($id_master = null)
    {
        $this->updateActiveInactiveMember();
        if ($id_master === null) {
            return $this->db->query("SELECT master.id_master AS id_master, master.unix_key AS unix_key , master.name AS name, master.phone AS phone, DATE_FORMAT(master.expired,'%e %M %Y') AS expired, master.username AS username, master.dojo AS dojo, master.max_dojo AS max_dojo, master.status AS status, master.dojo_name, master.up_rank, master.low_rank, IFNULL(SUM(dojo.active_member),0) AS active, IFNULL(SUM(dojo.inactive_member),0) AS inactive from master LEFT JOIN dojo ON master.id_master = dojo.id_master GROUP BY id_master ORDER BY master.status, master.name, master.max_dojo")->result('array');
        } else {
            $result = $this->db->query("SELECT master.id_master AS id_master, master.unix_key AS unix_key , master.name AS name, master.phone AS phone, master.expired, master.username AS username, master.dojo AS dojo, master.max_dojo AS max_dojo, master.status AS status, master.dojo_name, master.up_rank, master.low_rank, IFNULL(SUM(dojo.active_member),0) AS active, IFNULL(SUM(dojo.inactive_member),0) AS inactive, photo FROM master LEFT JOIN dojo ON master.id_master = dojo.id_master WHERE master.id_master = $id_master")->result('array');
            if ($result) {
                return $result;
            }
        }
    }

    public function newMaster($data = null)
    {
        if ($data) {
            $insert = $this->db->insert('master', $data);
            if ($insert) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function editExpiration($expired, $id_master)
    {
        if ($expired != null && $id_master != null) {
            $edit = $this->db->query("UPDATE master SET expired = '$expired' WHERE id_master = $id_master");
            if ($edit) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function editUnixKey($unix_key, $id_master)
    {
        if ($unix_key != null && $id_master != null) {
            $edit = $this->db->query("UPDATE master SET unix_key = '$unix_key' WHERE id_master = $id_master");
            if ($edit) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function editPassword($password, $id_master)
    {
        if ($password != null && $id_master != null) {
            $edit = $this->db->query("UPDATE master SET password = '$password' WHERE id_master = $id_master");
            if ($edit) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function editActivate($status, $id_master)
    {
        if ($status != null && $id_master != null) {
            $edit = $this->db->query("UPDATE master SET status = $status WHERE id_master = $id_master");
            if ($edit) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getAllDojo($id_master = null)
    {
        if ($id_master) {
            $this->updateActiveInactiveMember();
            return $this->db->query("SELECT id_dojo, name, address, active_member AS active, inactive_member AS inactive FROM dojo WHERE id_master = $id_master ORDER BY name")->result('array');
        }
    }

    public function getAllDetailDojo($id_master = null)
    {
        if ($id_master) {
            $this->updateActiveInactiveMember();
            return $this->db->query("SELECT id_dojo, id_master, active_member, inactive_member, name, period, request_code, address, email, latitude, longitude, id_master, id_head, IFNULL((SELECT name from member WHERE id_member = id_head),'-') AS head, id_secretary, IFNULL((SELECT name from member WHERE id_member = id_secretary),'-') AS secretary, id_treasurer, IFNULL((SELECT name from member WHERE id_member = id_treasurer),'-') AS treasurer, id_instructor, IFNULL((SELECT name from member WHERE id_member = id_instructor),'-') AS instructor, id_assistant, IFNULL((SELECT name from member WHERE id_member = id_assistant),'-') AS assistant, pengprov, pengcab, fee1, fee2, fee3, photo1, photo2, photo3, isActive FROM dojo WHERE id_master = $id_master ORDER BY name")->result('array');
        }
    }

    public function deleteMember($id_master)
    {
        $this->db->query("DELETE member FROM member JOIN dojo ON member.id_dojo = dojo.id_dojo JOIN master ON dojo.id_master = master.id_master WHERE master.id_master = $id_master");
    }

    public function deleteDojo($id_master)
    {
        $this->deleteMember($id_master);
        $this->db->query("DELETE dojo FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE master.id_master = $id_master");
    }

    public function deleteMaster($id_master)
    {
        $this->deleteDojo($id_master);
        $this->db->delete('master', ['id_master' => $id_master]);
        return $this->db->affected_rows();
    }

    public function editProfile($id_master, $data)
    {
        if ($this->db->update('master', $data, array('id_master' => $id_master))) {
            return true;
        } else {
            return false;
        }
    }

    public function getPassword($id_master)
    {
        return $this->db->query("SELECT password from master WHERE id_master = $id_master")->result('array');
    }

    public function getAllMemberForEdit($id_master, $id_dojo)
    {
        return $this->db->query("(SELECT member.id_member, member.name, dojo.name AS dojo, rank.name AS rank FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo ON dojo.id_dojo = member.id_dojo JOIN master ON dojo.id_master = master.id_master WHERE master.id_master = $id_master AND member.id_dojo = $id_dojo ORDER BY rank.id_rank, dojo.name, member.name ASC) UNION (SELECT member.id_member, member.name, dojo.name AS dojo, rank.name AS rank FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo ON dojo.id_dojo = member.id_dojo JOIN master ON dojo.id_master = master.id_master WHERE master.id_master = $id_master AND member.id_role = 6 ORDER BY rank.id_rank, dojo.name, member.name ASC)")->result('array');
    }

    public function getAllMemberForAdd($id_master)
    {
        return $this->db->query("SELECT member.id_member, member.name, dojo.name AS dojo, rank.name AS rank FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo ON dojo.id_dojo = member.id_dojo JOIN master ON dojo.id_master = master.id_master WHERE master.id_master = $id_master AND member.id_role = 6 ORDER BY rank.id_rank, dojo.name, member.name ASC")->result('array');
    }

    public function getAllInstructorForAdd($id_master)
    {
        return $this->db->query("SELECT member.id_member, member.name, dojo.name AS dojo, rank.name AS rank FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo ON dojo.id_dojo = member.id_dojo JOIN master ON dojo.id_master = master.id_master WHERE master.id_master = $id_master AND (member.id_role = 6 OR member.id_role = 4) ORDER BY member.id_role, rank.id_rank, dojo.name, member.name ASC")->result('array');
    }

    public function changeRole($id_member, $id_role)
    {
        $result = $this->db->query("UPDATE member SET id_role = $id_role WHERE id_member = $id_member");
    }

    public function editDojo($id_dojo, $data_array)
    {
        $this->db->update('dojo', $data_array, array('id_dojo' => $id_dojo));
    }

    public function getRank()
    {
        return $this->db->query("SELECT * from rank")->result('array');
    }

    public function addMember($data, $username)
    {
        $this->updateActiveInactiveMember();
        $this->db->insert('member', $data);
        $id = $this->db->query("SELECT id_member from member WHERE username = '$username'")->result('array');
        $id_string = array_values($id[0]);
        return $id_string[0];
    }

    public function memberChangeDojo($id_dojo, $id_member)
    {
        $this->db->query("DELETE FROM member_schedule WHERE id_member = $id_member");
        return $this->db->query("UPDATE member SET id_dojo = $id_dojo WHERE id_member = $id_member");
    }

    public function addNewDojo($data, $name, $id_master)
    {
        $this->db->insert('dojo', $data);
        $this->db->query("UPDATE master SET dojo = dojo + 1 WHERE id_master = $id_master");

        $id = $this->db->query("SELECT id_dojo from dojo WHERE name = '$name' AND id_master = $id_master")->result('array')[0];
        return $id['id_dojo'];
    }

    public function isDojoNameUnix($name, $id_master)
    {
        $result = $this->db->query("SELECT id_dojo from dojo WHERE name = '$name' AND id_master = $id_master")->result('array');
        if ($result == null) {
            return true;
        } else {
            return false;
        }
    }

    public function isUsernameUnix($username)
    {
        $result = $this->db->query("SELECT id_master from master WHERE username = '$username'")->result('array');
        if ($result == null) {
            return true;
        } else {
            return false;
        }
    }

    public function isAddAvailable($id_master)
    {
        $result = $this->db->query("SELECT dojo, max_dojo from master WHERE id_master = $id_master")->result('array');
        $string_result = array_values($result[0]);

        if ($string_result[0] < $string_result[1]) {
            return true;
        }
        return false;
    }

    public function isMemberUsernameUnix($username)
    {
        $member = $this->db->query("SELECT id_member from member WHERE username = '$username'")->result('array');
        $request_member = $this->db->query("SELECT id_request from join_request WHERE username = '$username'")->result('array');
        if ($member == null && $request_member == null) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteAllDojoPhoto($id_master, $id_dojo)
    {
        $path = "photo_dojo/$id_master/$id_dojo/";
        $files = glob("$path" . "*", GLOB_BRACE);
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($path);
    }

    public function deleteDojoByDojoID($id_master, $id_dojo)
    {
        $this->db->query("DELETE FROM member WHERE id_dojo = $id_dojo");
        $this->db->query("DELETE FROM dojo WHERE id_dojo = $id_dojo");
        $this->db->query("UPDATE master SET dojo = dojo-1 WHERE id_master = $id_master");
    }

    public function updateMapDojo($id_dojo, $latitude, $longitude)
    {
        $this->db->query("UPDATE dojo SET latitude = $latitude, longitude = $longitude WHERE id_dojo = $id_dojo");
    }

    public function updateAttendance()
    {
        $this->db->query("UPDATE member SET attendance = (SELECT COUNT(id_member) FROM history_presence WHERE id_member = member.id_member)");
    }

    public function getDetailMember($id_dojo)
    {
        $this->updateAttendance();
        return $this->db->query("SELECT member.id_member, member.name, member.status, member.photo, member.username, rank.name AS rank, member.id_role, member.period, member.expiration, IF(member.id_fee = 1, (SELECT fee1 FROM dojo WHERE id_dojo = member.id_dojo), IF(member.id_fee = 2, (SELECT fee2 FROM dojo WHERE id_dojo = member.id_dojo), (SELECT fee3 FROM dojo WHERE id_dojo = member.id_dojo))) AS fee, member.attendance, IF(member.attendance=0,0,(SELECT COUNT(id_member) FROM history_presence WHERE id_member = member.id_member AND id_dojo = $id_dojo)) AS own, member.birth_date, member.blood, member.disease, member.phone1, member.phone2, member.address FROM member JOIN rank ON member.id_rank = rank.id_rank WHERE id_dojo = $id_dojo ORDER BY member.status, member.id_role, member.name, member.id_rank")->result('array');
    }


    //CURRICULUM
    public function getCurriculum($id_master)
    {
        $curriculum = $this->db->query("SELECT curriculum.id_curriculum, rank.name AS rank , curriculum.link FROM curriculum JOIN rank ON curriculum.id_rank = rank.id_rank WHERE curriculum.id_master = $id_master ORDER BY curriculum.id_rank")->result('array');

        $result = array();
        foreach ($curriculum as $content) {
            $id = $content['id_curriculum'];
            $result_content = $this->db->query("SELECT id_content, content FROM content WHERE id_curriculum = $id")->result('array');
            if ($result_content ==  null) {
                $content = $content + array('array_content' => null);
            } else {
                $content = $content + array('array_content' => $result_content);
            }
            array_push($result, $content);
        }
        return $result;
    }

    public function addCurriculum($data)
    {
        return $this->db->insert('curriculum', $data);
    }

    public function deleteCurriculum($id_curriculum)
    {
        $this->db->delete('content', array('id_curriculum' => $id_curriculum));
        return $this->db->delete('curriculum', array('id_curriculum' => $id_curriculum));
    }

    public function getContent($id_curriculum)
    {
        return $this->db->select('*')->from('content')->where(array('id_curriculum' => $id_curriculum))->get()->result('array');
    }

    public function addContent($data)
    {
        return $this->db->insert('content', $data);
    }

    public function editContent($data, $id_content)
    {
        return $this->db->update('content', $data, array('id_content' => $id_content));
    }

    public function deleteContent($id_content)
    {
        return $this->db->delete('content', array('id_content' => $id_content));
    }

    public function editLink($id_curriculum, $link)
    {
        return $this->db->update('curriculum', array('link' => $link), array('id_curriculum' => $id_curriculum));
    }

    //TRANSFER
    public function getTransfer($id_master)
    {
        return $this->db->query("SELECT transfer.id_transfer, member.name AS 'member', IFNULL((SELECT name FROM dojo WHERE id_dojo = transfer.id_dojo_from),'-') AS 'dojo_from', IFNULL((SELECT name FROM dojo WHERE id_dojo = transfer.id_dojo_to),'-') AS dojo_to, transfer.date FROM transfer JOIN member ON transfer.id_member = member.id_member WHERE transfer.id_master = $id_master ORDER BY transfer.date DESC")->result('array');
    }

    public function addTransfer($data)
    {
        return $this->db->insert('transfer', $data);
    }

    public function getDojoForSpinner($id_master)
    {
        return $this->db->query("SELECT id_dojo, name FROM dojo WHERE id_master = $id_master ORDER BY name")->result('array');
    }

    public function getMemberForTransfer($id_dojo)
    {
        return $this->db->query("SELECT member.id_member, member.name, rank.name AS rank, dojo.name AS dojo FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo On member.id_dojo = dojo.id_dojo WHERE dojo.id_dojo = $id_dojo AND member.id_role = 6 ORDER BY member.name")->result('array');
    }

    public function getAdministrator($id_dojo)
    {
        return $this->db->query("SELECT id_head, id_secretary, id_treasurer, id_assistant FROM dojo WHERE id_dojo = $id_dojo")->result('array');
    }

    public function getAllInstructor($id_master)
    {
        return $this->db->query("SELECT member.id_member, member.name, member.photo, member.username, rank.name AS rank, member.period, member.attendance, member.birth_date, member.blood, member.disease, member.phone1, member.phone2, member.address, member.place_of_birth FROM member JOIN dojo ON member.id_dojo = dojo.id_dojo JOIN rank ON member.id_rank = rank.id_rank WHERE dojo.id_master = $id_master AND member.id_role = 4 ORDER BY member.name")->result('array');
    }

    public function getInstructorDojo($id_member)
    {
        return $this->db->query("SELECT id_dojo, name FROM dojo WHERE id_instructor = $id_member")->result('array');
    }


    public function getDojoAndInstructor($id_master)
    {
        return $this->db->query("SELECT dojo.id_dojo, dojo.name AS dojo, member.id_member, member.name AS instructor FROM dojo JOIN member ON dojo.id_instructor = member.id_member WHERE dojo.id_master = $id_master AND member.id_role = 4")->result('array');
    }

    public function isInstrcutorHave1Dojo($id_instructor)
    {
        $result = $this->db->query("SELECT COUNT(id_dojo) AS sum FROM dojo WHERE id_instructor = $id_instructor")->result('array')[0];
        if ($result['sum'] > 1) {
            return false;
        } else {
            return true;
        }
    }

    public function editDojoInstpector($id_dojo, $id_new_instructor)
    {
        $get_instructor = $this->db->query("SELECT id_instructor FROM dojo WHERE id_dojo = $id_dojo")->result('array')[0];
        $id_current_instructor = $get_instructor['id_instructor'];

        if ($this->isInstrcutorHave1Dojo($id_current_instructor)) {
            $this->changeRole($id_current_instructor, 6);
        }

        $this->changeRole($id_new_instructor, 4);
        $this->db->update('dojo', array('id_instructor' => $id_new_instructor), array('id_dojo' => $id_dojo));
    }

    public function uploadDojoPhoto($name, $filePath, $id_dojo)
    {
        return $this->db->query("UPDATE dojo SET photo$name = '$filePath' WHERE id_dojo = $id_dojo");
    }

    public function getAttendance($id_dojo = null, $date_min = null, $date_max = null)
    {
        if ($date_min == null && $date_max == null) {
            return $this->db->query("SELECT id_presence, datetime, photo, IF(note IS NULL,false,true) AS note_available FROM presence WHERE id_dojo = $id_dojo ORDER BY datetime DESC")->result('array');
        } else if ($date_min != null && $date_max != null) {
            return $this->db->query("SELECT id_presence, datetime, IF(note IS NULL,false,true) AS note_available photo FROM presence WHERE id_dojo = $id_dojo AND (datetime BETWEEN '$date_min' AND '$date_max') ORDER BY datetime DESC")->result('array');
        } else if ($date_min != null && $date_max == null) {
            return $this->db->query("SELECT id_presence, datetime, photo IF(note IS NULL,false,true) AS note_available FROM presence WHERE id_dojo = $id_dojo AND datetime > '$date_min' ORDER BY datetime DESC")->result('array');
        } else {
            return $this->db->query("SELECT id_presence, datetime, photo IF(note IS NULL,false,true) AS note_available FROM presence WHERE id_dojo = $id_dojo AND datetime < '$date_max' ORDER BY datetime DESC")->result('array');
        }
    }

    public function getAttendanceList($id_presence)
    {
        return $this->db->query("SELECT history_presence.id_history, member.name, rank.name AS rank, dojo.name AS dojo, member.id_role, history_presence.is_manual, history_presence.datetime FROM history_presence JOIN member ON history_presence.id_member = member.id_member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo on dojo.id_dojo = history_presence.id_dojo WHERE history_presence.id_presence = $id_presence ORDER BY member.id_role, member.name")->result('array');
    }

    public function getNote($id_presence)
    {
        $note = $this->db->query("SELECT presence.id_presence, presence.datetime, presence.note, member.name AS instructor FROM presence JOIN member ON presence.id_instructor = member.id_member WHERE presence.id_presence = $id_presence")->result('array');

        $note_special = $this->db->query("SELECT note_special.id_note, rank.name AS rank, note_special.note FROM note_special JOIN rank ON note_special.id_rank = rank.id_rank WHERE note_special.id_presence = $id_presence ORDER BY note_special.id_rank")->result('array');

        $result = $note[0] + array('note_special' => $note_special);
        return $result;
    }

    public function getEvent($id_master)
    {
        return $this->db->query("SELECT id_event, datetime, descrip FROM event WHERE id_master = $id_master AND is_done = 0 ORDER BY datetime DESC")->result('array');
    }

    public function addEvent($array)
    {
        $this->db->insert('event', $array);
    }

    public function editEvent($id_event, $array)
    {
        $this->db->update('event', $array, array('id_event' => $id_event));
    }

    public function deleteEvent($id_event)
    {
        $this->db->query("DELETE FROM event_participant WHERE id_event = $id_event");
        $this->db->query("DELETE FROM event WHERE id_event = $id_event");
    }

    public function getEventHistory($id_master)
    {
        return $this->db->query("SELECT id_event, datetime, descrip FROM event WHERE id_master = $id_master AND is_done = 1 ORDER BY datetime DESC")->result('array');
    }

    public function getEventHistoryMember($id_event, $id_dojo)
    {
        return $this->db->query("SELECT event_participant.id_participant, member.name, member.username, rank.name AS rank, IF(event_participant.datetime IS NULL,IF(event_participant.is_qualify = 1,2,3),1) AS status FROM event_participant JOIN member ON event_participant.id_member = member.id_member JOIN rank ON member.id_rank = rank.id_rank WHERE member.id_dojo = $id_dojo AND event_participant.id_event = $id_event")->result('array');
    }

    public function getEventHistoryDojo($id_master, $id_event)
    {
        return $this->db->query("SELECT id_dojo as ID, dojo.name, (SELECT COUNT(event_participant.id_participant) FROM event_participant JOIN member ON event_participant.id_member = member.id_member JOIN event ON event_participant.id_event = event.id_event WHERE member.id_dojo = ID AND event.id_event = $id_event) AS count FROM dojo WHERE dojo.id_master = $id_master")->result('array');
    }

    public function getInstructorExcel($id_master)
    {
        $master = $this->db->query("SELECT name, dojo_name, up_rank, low_rank FROM master WHERE id_master = $id_master")->result('array');

        $instructors = $this->db->query("SELECT member.id_member, member.name, member.certificate_number, member.username, rank.name AS rank, member.period, member.expiration, IF(member.id_fee = 1, (SELECT fee1 FROM dojo WHERE id_dojo = member.id_dojo), IF(member.id_fee = 2, (SELECT fee2 FROM dojo WHERE id_dojo = member.id_dojo), (SELECT fee3 FROM dojo WHERE id_dojo = member.id_dojo))) AS fee, member.birth_date, member.blood, member.disease, member.phone1, member.phone2, member.address FROM member JOIN dojo ON member.id_dojo = dojo.id_dojo JOIN rank ON member.id_rank = rank.id_rank JOIN role ON member.id_role = role.id WHERE dojo.id_master = $id_master AND member.id_role = 4 ORDER BY member.name, member.id_rank")->result('array');

        if ($master != null) {
            $size = count($instructors);
            for ($i = 0; $i < $size; $i++) {
                $id_instructor = $instructors[$i]['id_member'];
                $dojo = $this->db->query("SELECT name FROM dojo WHERE id_instructor = $id_instructor ORDER BY name")->result('array');
                $instructors[$i]['dojo'] = $dojo;
            }

            $instructors_result = array();
            foreach ($instructors as $m) {
                $rank = $m["rank"];
                $substring = substr($rank, 0, strlen($rank) - 3);

                if (substr($rank, strlen($rank) - 3, strlen($rank)) == "Dan") {
                    $rank = $substring . $master[0]['up_rank'];
                } else {
                    $rank = $substring . $master[0]['low_rank'];
                }
                $m["rank"] = $rank;
                $m["period"] = substr($m["period"], 0, 10);

                array_push($instructors_result, $m);
            }

            return array(
                'master' => $master[0]['name'],
                'dojo_name' => $master[0]['dojo_name'],
                'up_rank' => $master[0]['up_rank'],
                'low_rank' => $master[0]['low_rank'],
                'member' => $instructors_result
            );
        } else {
            return null;
        }
    }

    public function getDojoExcel($id_master)
    {
        $this->updateActiveInactiveMember();
        $master = $this->db->query("SELECT name AS master, dojo_name FROM master WHERE id_master = $id_master")->result('array');

        $dojo = $this->db->query("SELECT id_dojo, id_master, active_member, inactive_member, name, request_code, address, email, latitude, longitude, id_master, (SELECT name from member WHERE id_member = id_head) AS head, (SELECT name from member WHERE id_member = id_secretary) AS secretary, (SELECT name from member WHERE id_member = id_treasurer) AS treasurer, (SELECT name from member WHERE id_member = id_instructor) AS instructor, (SELECT name from member WHERE id_member = id_assistant) AS assistant, pengprov, pengcab, fee1, fee2, fee3 FROM dojo WHERE id_master = $id_master ORDER BY name")->result('array');

        if ($master != null) {
            return array(
                'master' => $master[0]['master'],
                'dojo_name' => $master[0]['dojo_name'],
                'dojo' => $dojo
            );
        } else {
            return null;
        }
    }

    public function uploadPhotoProfile($file_name, $id_master)
    {
        return $this->db->query("UPDATE master SET photo = '$file_name' WHERE id_master = $id_master");
    }

    public function deletePhotoProfile($id_master)
    {
        return $this->db->query("UPDATE master SET photo = NULL WHERE id_master = $id_master");
    }

    public function activateDojo($id_dojo, $period)
    {
        $this->db->query("UPDATE dojo SET isActive = 1, period = '$period' WHERE id_dojo = $id_dojo");
    }

    public function deactivateDojo($id_dojo)
    {
        $this->db->query("UPDATE dojo SET isActive = 0 WHERE id_dojo = $id_dojo");
    }

    public function addAd($array)
    {
        $id_master = $array['id_master'];
        $number = $array['number'];

        $checkIfExist = $this->db->query("SELECT id_ad FROM ad WHERE id_master = $id_master AND number = $number")->result('array');

        $this->db->insert('ad', $array);
    }

    public function editAd($id_ad, $link)
    {
        $this->db->query("UPDATE ad SET link = '$link' WHERE id_ad = $id_ad");
    }

    public function editScaleAd($id_ad, $scale)
    {
        $this->db->query("UPDATE ad SET scale = $scale WHERE id_ad = $id_ad");
    }

    public function deleteAd($id_ad)
    {
        $this->db->query("DELETE FROM ad WHERE id_ad = $id_ad");
    }

    public function getAd($id_master)
    {
        return $this->db->query("SELECT id_ad, number, link, scale FROM ad WHERE id_master = $id_master ORDER BY number")->result('array');
    }

    public function getParticipant($id_exam)
    {
        return $this->db->query("SELECT participant.id_participant, member.name, participant.from_rank AS id_from_rank, (SELECT name FROM rank WHERE id_rank = participant.from_rank) AS from_rank, participant.to_rank AS id_to_rank, (SELECT name FROM rank WHERE id_rank = participant.to_rank) AS to_rank, participant.isRecommend, member.username, member.certificate_number, dojo.name AS dojo, participant.status FROM participant JOIN member ON participant.id_member = member.id_member JOIN rank ON rank.id_rank = member.id_rank JOIN dojo ON member.id_dojo = dojo.id_dojo WHERE participant.id_exam = $id_exam ORDER BY member.name")->result('array');
    }

    public function getExam($id_master)
    {
        return $this->db->query("SELECT id_exam, datetime, descrip FROM exam WHERE id_master = $id_master AND status = 0 ORDER BY datetime DESC")->result('array');
    }

    public function editPasswordAdmin($password, $username)
    {
        if ($password != null && $username != null) {
            $edit = $this->db->query("UPDATE admin SET password = '$password' WHERE username = '$username'");
            if ($edit) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function editAbout($id, $data)
    {
        $this->db->query("UPDATE about SET about = '$data' WHERE id = $id");
    }
}
