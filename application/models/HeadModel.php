<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Jakarta");
class HeadModel extends CI_Model
{
    public function getProfile($id_member)
    {
        $result =  $this->db->query("SELECT member.id_member, member.name, member.status, member.photo, member.username, member.certificate_number, rank.name AS rank, member.id_role, member.period, member.expiration, IF(member.id_fee = 1, (SELECT fee1 FROM dojo WHERE id_dojo = member.id_dojo), IF(member.id_fee = 2, (SELECT fee2 FROM dojo WHERE id_dojo = member.id_dojo), (SELECT fee3 FROM dojo WHERE id_dojo = member.id_dojo))) AS fee,member.attendance, IF(member.attendance=0,0,(SELECT COUNT(id_member) FROM history_presence WHERE id_member = $id_member AND id_dojo = (SELECT id_dojo FROM member WHERE id_member = $id_member))) AS own, member.birth_date, member.blood, member.disease, member.phone1, member.phone2, member.address, member.place_of_birth FROM member JOIN rank ON member.id_rank = rank.id_rank WHERE member.id_member = $id_member ORDER BY member.status, member.id_role, member.id_rank, member.name")->result('array');
        if ($result != null) {
            return $result[0];
        }
        return null;
    }

    public function editProfile($data_array, $id_member)
    {
        return $this->db->update('member', $data_array, array('id_member' => $id_member));
    }

    public function getPassword($id_member)
    {
        return $this->db->query("SELECT password FROM member WHERE id_member = $id_member")->result('array')[0]['password'];
    }

    public function changePassword($id_member, $password)
    {
        return $this->db->query("UPDATE member SET password = '$password' WHERE id_member = $id_member");
    }

    public function uploadPhotoProfile($file_name, $id_member)
    {
        return $this->db->query("UPDATE member SET photo = '$file_name' WHERE id_member = $id_member");
    }

    public function updateActiveInactiveMember()
    {
        $this->db->query("UPDATE dojo SET dojo.active_member = (SELECT COUNT(*) FROM member WHERE status = 0 AND id_dojo = dojo.id_dojo), dojo.inactive_member = (SELECT COUNT(*) FROM member WHERE status = 1 AND id_dojo = dojo.id_dojo)");
    }

    public function getProfileDojo($id_dojo)
    {
        $this->updateActiveInactiveMember();
        return $this->db->query("SELECT id_dojo, id_master, active_member, inactive_member, name, period, request_code, address, email, latitude, longitude, id_master, id_head, IFNULL((SELECT name from member WHERE id_member = id_head),'-') AS head, id_secretary, IFNULL((SELECT name from member WHERE id_member = id_secretary),'-') AS secretary, id_treasurer, IFNULL((SELECT name from member WHERE id_member = id_treasurer),'-') AS treasurer, id_instructor, IFNULL((SELECT name from member WHERE id_member = id_instructor),'-') AS instructor, id_assistant, IFNULL((SELECT name from member WHERE id_member = id_assistant),'-') AS assistant, pengprov, pengcab, fee1, fee2, fee3, photo1, photo2, photo3 FROM dojo WHERE id_dojo = $id_dojo")->result('array');
    }

    public function getIDMaster($id_dojo)
    {
        $id_master = $this->db->query("SELECT id_master FROM dojo WHERE id_dojo = $id_dojo")->result('array');
        if ($id_master != null) {
            return $id_master[0]['id_master'];
        } else {
            return null;
        }
    }

    public function uploadDojoPhoto($name, $filePath, $id_dojo)
    {
        return $this->db->query("UPDATE dojo SET photo$name = '$filePath' WHERE id_dojo = $id_dojo");
    }

    public function getAllMemberForEdit($id_dojo)
    {
        return $this->db->query("SELECT member.id_member, member.name, dojo.name AS dojo, rank.name AS rank FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo ON dojo.id_dojo = member.id_dojo  WHERE member.id_dojo = $id_dojo ORDER BY rank.id_rank, dojo.name, member.name ASC")->result('array');
    }

    public function getRank()
    {
        return $this->db->query("SELECT * from rank")->result('array');
    }

    public function isMemberUsernameUnix($username)
    {
        $result = $this->db->query("SELECT id_member from member WHERE username = '$username'")->result('array');
        if ($result == null) {
            return true;
        } else {
            return false;
        }
    }

    public function getAdministrator($id_dojo)
    {
        return $this->db->query("SELECT id_head, id_secretary, id_treasurer, id_instructor, id_assistant FROM dojo WHERE id_dojo = $id_dojo")->result('array');
    }

    public function changeRole($id_member, $id_role)
    {
        $result = $this->db->query("UPDATE member SET id_role = $id_role WHERE id_member = $id_member");
    }

    public function editDojo($id_dojo, $data_array)
    {
        $this->db->update('dojo', $data_array, array('id_dojo' => $id_dojo));
    }

    public function updateMapDojo($id_dojo, $latitude, $longitude)
    {
        $this->db->query("UPDATE dojo SET latitude = $latitude, longitude = $longitude WHERE id_dojo = $id_dojo");
    }

    public function deletePhotoDojo($id_dojo, $position)
    {
        return $this->db->query("UPDATE dojo SET photo$position = NULL WHERE id_dojo = $id_dojo");
    }

    public function deletePhotoProfile($id_member)
    {
        return $this->db->query("UPDATE member SET photo = NULL WHERE id_member = $id_member");
    }

    public function getPresenceDojo($id_dojo)
    {
        return $this->db->query("SELECT id_presence, id_schedule, treasurer_permission, secretary_permission, IF(note IS NULL,0,1) AS isNoteExist, IF((SELECT history_presence.id_member FROM history_presence JOIN dojo ON history_presence.id_dojo = $id_dojo WHERE history_presence.id_presence = presence.id_presence AND history_presence.id_member = dojo.id_head) IS NULL,0,1) AS isHeadAttend, photo, datetime FROM presence WHERE id_dojo = $id_dojo ORDER BY datetime DESC")->result('array');
    }

    public function getCurrentInstructor($id_dojo)
    {
        return $this->db->query("SELECT id_instructor FROM dojo WHERE id_dojo = $id_dojo")->result('array')[0]['id_instructor'];
    }

    public function addPresence($data)
    {
        $this->db->insert('presence', $data);
        $id_presence = $this->db->insert_id();
        return $id_presence;
    }

    public function addHistoryPresence($data, $id_member)
    {
        $this->updateAttendance();
        $this->db->insert('history_presence', $data);
        return $this->db->query("UPDATE member SET attendance = attendance + 1 WHERE id_member = $id_member");
    }

    public function deletePresence($id_presence)
    {
        $this->updateAttendance();
        $this->db->query("DELETE FROM history_presence WHERE id_presence = $id_presence");
        $this->db->query("DELETE FROM presence WHERE id_presence = $id_presence");
    }

    public function editPermissionPresence($id_presence, $treasurer_permission, $secretary_permission)
    {
        $this->db->query("UPDATE presence SET treasurer_permission = $treasurer_permission, secretary_permission = $secretary_permission WHERE id_presence = $id_presence");
    }

    public function getDojoForSpinner($id_master)
    {
        return $this->db->query("SELECT id_dojo, name FROM dojo WHERE id_master = $id_master ORDER BY name")->result('array');
    }

    public function isMemberExist($id_member, $array)
    {
        foreach ($array as $data) {
            if ($data['id_member'] == $id_member) {
                return true;
            }
        }
        return false;
    }

    public function getMemberForManual($id_dojo, $id_presence)
    {
        $members =  $this->db->query("SELECT member.id_member, member.name, rank.name AS rank, dojo.name AS dojo FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo ON member.id_dojo = dojo.id_dojo WHERE dojo.id_dojo = $id_dojo ORDER BY member.name")->result('array');

        $membersJoined = $this->db->query("SELECT id_member FROM history_presence WHERE id_presence = $id_presence")->result('array');

        $result = array();
        foreach ($members as $member) {
            if (!$this->isMemberExist($member['id_member'], $membersJoined)) {
                $result[] = $member;
            }
        }

        return $result;
    }

    public function getIDDojoByIDMember($id_member)
    {
        $result = $this->db->query("SELECT id_dojo FROM member WHERE id_member = $id_member")->result('array');

        if ($result != null) {
            return $result[0]['id_dojo'];
        } else {
            return null;
        }
    }

    public function isMemberNotYetPresence($id_member, $id_presence)
    {
        $result = $this->db->query("SELECT id_member FROM history_presence WHERE id_member = $id_member AND id_presence = $id_presence")->result('array');

        if ($result == null) {
            return true;
        } else {
            return false;
        }
    }

    public function getDataPresenceForQRCode($id_presence)
    {
        $result = $this->db->query("SELECT dojo.name AS dojo, presence.datetime FROM presence JOIN dojo ON presence.id_dojo = dojo.id_dojo WHERE presence.id_presence = $id_presence")->result('array');
        if ($result != null) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function uploadPhotoPresence($id_presence, $link)
    {
        return $this->db->query("UPDATE presence SET photo = '$link' WHERE id_presence = $id_presence");
    }

    public function deletePhotoPresence($id_presence)
    {
        return $this->db->query("UPDATE presence SET photo = NULL WHERE id_presence = $id_presence");
    }

    public function getNotification($id_dojo)
    {
        return $this->db->query("SELECT id_notification, title, message, date FROM notification WHERE id_dojo = $id_dojo ORDER BY date")->result('array');
    }

    public function addNotification($data)
    {
        $this->db->insert('notification', $data);
    }

    public function editNotification($data_array, $id_notification)
    {
        $this->db->update('notification', $data_array, array('id_notification' => $id_notification));
    }

    public function deleteNotification($id_notification)
    {
        $this->db->query("DELETE FROM notification WHERE id_notification = $id_notification");
    }

    public function isMemberAdministrator($id_member)
    {
        $id_dojo = $this->db->query("SELECT id_dojo FROM member WHERE id_member = $id_member")->result('array')[0]['id_dojo'];

        $getAdmins = $this->db->query("SELECT id_head, id_secretary, id_treasurer, id_instructor, id_assistant FROM dojo WHERE id_dojo = $id_dojo")->result('array');

        if ($getAdmins == null) {
            return null;
        }

        $admins = $getAdmins[0];

        foreach ($admins as $admin) {
            if ($admin == $id_member) {
                return true;
            }
        }
        return false;
    }

    public function checkPresenceTime($id_presence)
    {
        $currentTime = strtotime(date('H:i:s'));
        $presence_time = $this->db->query("SELECT datetime FROM presence WHERE id_presence = $id_presence")->result('array')[0]['datetime'];

        $startTime = strtotime("-10 minutes", strtotime($presence_time));
        $endTime = strtotime("+15 minutes", strtotime($presence_time));

        if ($currentTime >= $startTime) {
            if ($currentTime <= $endTime) {
                return 'Success';
            } else {
                return 'Already late, presence closes 15 minutes after schedule';
            }
        } else {
            return 'Too early, presence opens 10 minutes before schedule';
        }
    }

    //Schedule
    public function getScheduleDojo($id_dojo)
    {
        return $this->db->query("SELECT id_schedule, day, time from dojo_schedule WHERE id_dojo = $id_dojo ORDER BY day, time")->result('array');
    }

    public function addScheduleDojo($data)
    {
        $this->db->insert('dojo_schedule', $data);
    }

    public function editScheduleDojo($data, $id_schedule)
    {
        $this->db->update('dojo_schedule', $data, array('id_schedule' => $id_schedule));
    }

    public function deleteScheduleDojo($id_schedule)
    {
        $this->db->query("DELETE FROM member_schedule WHERE id_schedule = $id_schedule");
        $this->db->query("DELETE FROM dojo_schedule WHERE id_schedule = $id_schedule");
    }

    public function getIDScheduleByIDPresence($id_presence)
    {
        $result = $this->db->query("SELECT id_schedule FROM presence WHERE id_presence = $id_presence")->result('array');

        if ($result != null) {
            return $result[0]['id_schedule'];
        } else {
            return null;
        }
    }

    public function isMemberHaveSchedule($id_member, $id_schedule)
    {
        $result = $this->db->query("SELECT id_member FROM member_schedule WHERE id_member = $id_member AND id_schedule = $id_schedule")->result('array');

        if ($result == null) {
            return false;
        } else {
            return true;
        }
    }

    public function getMemberForSchedule($id_dojo, $id_schedule)
    {
        return $this->db->query("SELECT member_schedule.id_member, member.name, dojo.name AS dojo, rank.name AS rank, member.username, role.name AS role FROM member_schedule JOIN member ON member_schedule.id_member = member.id_member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo ON dojo.id_dojo = member.id_dojo JOIN role ON role.id = member.id_role WHERE member.id_dojo = $id_dojo AND member_schedule.id_schedule = $id_schedule ORDER BY role.id, member.name")->result('array');
    }

    public function isScheduleMemberExist($array, $id_member)
    {
        foreach ($array as $object) {
            if ($object['id_member'] == $id_member) {
                return true;
            }
        }
        return false;
    }

    public function getMemberForAddSchedule($id_dojo, $id_schedule)
    {
        $listMember =  $this->db->query("SELECT member.id_member, member.name, rank.name AS rank, dojo.name AS dojo FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN dojo ON member.id_dojo = dojo.id_dojo WHERE member.id_dojo = $id_dojo")->result('array');

        $memberSchedule = $this->db->query("SELECT id_member FROM member_schedule WHERE id_schedule = $id_schedule")->result('array');

        $result = array();
        foreach ($listMember as $member) {
            if (!$this->isScheduleMemberExist($memberSchedule, $member['id_member'])) {
                $result[] = $member;
            }
        }

        return $result;
    }

    public function addScheduleMember($data)
    {
        $this->db->insert('member_schedule', $data);
    }

    public function deleteScheduleMember($id_member, $id_schedule)
    {
        $this->db->query("DELETE FROM member_schedule WHERE id_schedule = $id_schedule AND id_member = $id_member");
    }

    public function updateAttendance()
    {
        $this->db->query("UPDATE member SET attendance = (SELECT COUNT(id_member) FROM history_presence WHERE id_member = member.id_member)");
    }

    public function getDetailMember($id_dojo)
    {
        return $this->db->query("SELECT member.id_member, member.name, member.status, member.photo, member.certificate_number, member.username, rank.name AS rank, member.id_role, member.period, member.expiration, IF(member.id_fee = 1, (SELECT fee1 FROM dojo WHERE id_dojo = member.id_dojo), IF(member.id_fee = 2, (SELECT fee2 FROM dojo WHERE id_dojo = member.id_dojo), (SELECT fee3 FROM dojo WHERE id_dojo = member.id_dojo))) AS fee, member.attendance, IF(member.attendance=0,0,(SELECT COUNT(id_member) FROM history_presence WHERE id_member = member.id_member AND id_dojo = $id_dojo)) AS own, member.birth_date, member.blood, member.disease, member.phone1, member.phone2, member.address, member.place_of_birth FROM member JOIN rank ON member.id_rank = rank.id_rank WHERE id_dojo = $id_dojo ORDER BY member.status, member.id_role, member.name, member.id_rank")->result('array');
    }

    public function getScheduleMember($id_member)
    {
        return $this->db->query("SELECT dojo_schedule.id_schedule, dojo_schedule.day, dojo_schedule.time FROM member_schedule JOIN dojo_schedule ON member_schedule.id_schedule = dojo_schedule.id_schedule WHERE member_schedule.id_member = $id_member ORDER BY day, time")->result('array');
    }

    public function deleteMember($id_member)
    {
        $this->db->query("DELETE FROM history_presence WHERE id_member = $id_member");
        $this->db->query("DELETE FROM member_schedule WHERE id_member = $id_member");
        $this->db->query("DELETE FROM transfer WHERE id_member = $id_member");
        $this->db->query("DELETE FROM member WHERE id_member = $id_member");
    }

    public function getExam($id_master, $id_dojo)
    {
        return $this->db->query("SELECT exam.id_exam, datetime, descrip FROM exam WHERE id_master = $id_master AND status = 0 AND IF((SELECT COUNT(participant.id_participant) FROM participant JOIN member ON participant.id_member = member.id_member WHERE participant.id_exam = exam.id_exam AND member.id_dojo = $id_dojo) > 0, 1, IF((SELECT COUNT(participant.id_participant) FROM participant JOIN member ON participant.id_member = member.id_member WHERE participant.id_exam = exam.id_exam AND member.id_dojo = $id_dojo AND participant.status IS NOT NULL) > 0, 0, 1)) = 1 ORDER BY datetime DESC")->result('array');
    }

    public function addExam($array)
    {
        $this->db->insert('exam', $array);
    }

    public function editExam($array, $id_exam)
    {
        $this->db->update('exam', $array, array('id_exam' => $id_exam));
    }

    public function deleteExam($id_exam)
    {
        $this->db->query("DELETE FROM participant WHERE id_exam = $id_exam");
        $this->db->query("DELETE FROM exam WHERE id_exam = $id_exam");
    }

    public function getParticipant($id_exam, $id_dojo)
    {
        return $this->db->query("SELECT participant.id_participant, member.name, participant.from_rank AS id_from_rank, (SELECT name FROM rank WHERE id_rank = participant.from_rank) AS from_rank, participant.to_rank AS id_to_rank, (SELECT name FROM rank WHERE id_rank = participant.to_rank) AS to_rank, participant.isRecommend, member.username, member.certificate_number, dojo.name AS dojo, participant.status FROM participant JOIN member ON participant.id_member = member.id_member JOIN rank ON rank.id_rank = member.id_rank JOIN dojo ON member.id_dojo = dojo.id_dojo WHERE participant.id_exam = $id_exam AND member.id_dojo = $id_dojo ORDER BY member.name")->result('array');
    }

    public function addParticipan($array)
    {
        $this->db->insert('participant', $array);
    }

    public function editParticipant($array, $id_participant)
    {
        $this->db->update('participant', $array, array('id_participant' => $id_participant));
    }

    public function deleteParticipant($id_participant)
    {
        $this->db->query("DELETE FROM participant WHERE id_participant = $id_participant");
    }

    public function getExamHistory($id_master)
    {
        return $this->db->query("SELECT id_exam, datetime, descrip FROM exam WHERE id_master = $id_master AND status = 1 ORDER BY datetime DESC")->result('array');
    }

    public function getMemberForExam($id_dojo, $id_exam)
    {
        $members = $this->db->query("SELECT member.id_member, member.name, member.status, member.photo, member.username, member.certificate_number, rank.name AS rank, member.id_role, member.period, member.expiration, member.attendance, IF(member.attendance=0,0,(SELECT COUNT(id_member) FROM history_presence WHERE id_member = member.id_member AND id_dojo = $id_dojo)) AS own, member.birth_date, member.blood, member.disease FROM member JOIN rank ON member.id_rank = rank.id_rank WHERE id_dojo = $id_dojo ORDER BY member.status, member.id_role, member.name, member.id_rank")->result('array');

        $deleted = $this->db->query("SELECT id_member FROM participant WHERE id_exam = $id_exam")->result('array');

        $result = array();
        foreach ($members as $member) {
            if (!$this->isMemberExist($member['id_member'], $deleted)) {
                $result[] = $member;
            }
        }

        return $result;
    }

    public function getRankMember($id_member)
    {
        return $this->db->query("SELECT id_rank FROM member WHERE id_member = $id_member")->result('array')[0]['id_rank'];
    }

    public function finishExam($id_exam, $pass, $notPass)
    {
        if (count($pass) > 0) {
            $date = date('Y-m-d');

            foreach ($pass as $passData) {
                $id = $passData['id_participant'];
                $dataMember = $this->db->query("SELECT id_member, to_rank FROM participant WHERE id_participant = $id")->result('array');

                $id_member = $dataMember[0]['id_member'];
                $id_rank = $dataMember[0]['to_rank'];

                $this->db->query("UPDATE participant SET status = 1, from_date = (SELECT period FROM member WHERE id_member = $id_member), to_date = '$date' WHERE id_participant = $id");

                $this->db->query("UPDATE member SET id_rank = $id_rank, attendance = 0, period = '$date' WHERE id_member = $id_member");

                $this->db->query("DELETE FROM history_presence WHERE id_member = $id_member");
            }
        }

        if (count($notPass) > 0) {
            $string = "UPDATE participant SET status = 0 WHERE";
            $notPassSize = count($notPass);
            $i = 0;
            foreach ($notPass as $notPassData) {
                $i++;
                $id = $notPassData['id_participant'];
                $string = $string . " id_participant = $id";
                if ($notPassSize != $i) {
                    $string = $string . " OR";
                }
            }
            $this->db->query($string);
        }

        $isFinish = $this->db->query("SELECT id_participant FROM participant WHERE id_exam = $id_exam AND status IS NULL")->result('array');
        if ($isFinish == null) {
            $this->db->query("UPDATE exam SET status = 1 WHERE id_exam = $id_exam");
        }
    }

    public function getRecord($id_member)
    {
        $member = $this->db->query("SELECT member.name, rank.name AS rank, member.period FROM member JOIN rank ON member.id_rank = rank.id_rank WHERE member.id_member = $id_member")->result('array')[0];

        $rank = array();
        $rank = $this->db->query("SELECT (SELECT rank.name FROM rank WHERE id_rank = participant.from_rank) AS rank, participant.from_date, participant.to_date, participant.isRecommend FROM participant JOIN member ON participant.id_member = member.id_member WHERE participant.id_member = $id_member AND participant.status = 1 ORDER BY participant.from_rank")->result('array');

        array_unshift($rank, array(
            'rank' => $member['rank'],
            'from_date' => $member['period'],
            'to_date' => '-',
            'isRecommend' => '0'
        ));

        return $rank;
    }

    public function getRequestJoin($id_dojo)
    {
        $members = $this->db->query("SELECT id_request, name, birth_date, phone1, phone2, address, place_of_birth, datetime FROM join_request WHERE id_dojo = $id_dojo ORDER BY name")->result('array');

        if ($members != null) {
            $result = array();
            foreach ($members as $member) {
                $id_request = $member['id_request'];
                $schedule = $this->db->query("SELECT id_schedule FROM join_request_schedule WHERE id_request = $id_request")->result('array');
                $member['schedule'] = $schedule;
                $result[] = $member;
            }

            $dojo_schedule = $this->db->query("SELECT id_schedule, day, time FROM dojo_schedule WHERE id_dojo = $id_dojo ORDER BY day, time")->result('array');

            $rank = $this->db->query("SELECT id_rank, name FROM rank")->result('array');

            $fee = $this->db->query("SELECT fee1, fee2, fee3 FROM dojo WHERE id_dojo = $id_dojo")->result('array')[0];

            return array('member' => $result, 'schedule' => $dojo_schedule, 'rank' => $rank, 'fee' => $fee);
        } else {
            return null;
        }
    }

    public function deleteRequestJoin($id_request)
    {
        $this->db->query("DELETE FROM join_request WHERE id_request = $id_request");
        $this->db->query("DELETE FROM join_request_schedule WHERE id_request = $id_request");
    }

    public function acceptRequestJoin($id_request, $id_rank, $id_fee, $period, $schedules)
    {
        $request = $this->db->query("SELECT id_dojo, name, username, password, birth_date, phone1, phone2, address, place_of_birth FROM join_request WHERE id_request = $id_request")->result('array')[0];

        $request['expiration'] = date('Y-m-d', strtotime('+30 days', strtotime($period)));

        $request['id_rank'] = $id_rank;
        $request['id_fee'] = $id_fee;
        $request['period'] = $period;
        $request['id_role'] = 6;

        $this->db->insert('member', $request);
        $id_member = $this->db->insert_id();

        foreach ($schedules as $schedule) {
            $this->db->insert('member_schedule', array('id_schedule' => $schedule['id_schedule'], 'id_member' => $id_member));
        }

        $this->deleteRequestJoin($id_request);
    }

    public function getMemberFee($id_dojo)
    {
        $date = date('Y-m-d', strtotime('+7 days'));
        return $this->db->query("SELECT id_member, name, username, expiration, IF(id_fee = 1, (SELECT fee1 FROM dojo WHERE id_dojo = member.id_dojo), IF(id_fee = 2, (SELECT fee2 FROM dojo WHERE id_dojo = member.id_dojo), (SELECT fee3 FROM dojo WHERE id_dojo = member.id_dojo))) AS fee FROM member WHERE id_dojo = $id_dojo AND expiration < '$date' ORDER BY expiration")->result('array');
    }

    public function addMemberFee($id_member, $id_dojo, $month, $expiration, $fee, $is_late)
    {
        $array = array(
            'id_member' => $id_member,
            'fee' => $fee,
            'month' => $month,
            'is_late' => $is_late,
            'id_dojo' => $id_dojo
        );
        $this->db->insert('fee_history', $array);
        $this->db->query("UPDATE member SET expiration = '$expiration' WHERE id_member = $id_member");
    }

    public function getHistoryFee($id_dojo)
    {
        return $this->db->query("SELECT fee_history.id_history, member.name, member.username, fee_history.month, fee_history.fee, fee_history.date, fee_history.is_late FROM fee_history JOIN member ON fee_history.id_member = member.id_member WHERE fee_history.id_dojo = $id_dojo ORDER BY fee_history.date DESC, name")->result('array');
    }

    public function getEventMember($id_event, $id_dojo)
    {
        return $this->db->query("SELECT event_participant.id_participant, member.name, rank.name as rank, member.username, event_participant.is_qualify as qualify, IF(event_participant.datetime IS NULL,0,1) as attend FROM event_participant JOIN member ON event_participant.id_member = member.id_member JOIN rank ON member.id_rank = rank.id_rank WHERE event_participant.id_event = $id_event AND member.id_dojo = $id_dojo ORDER BY event_participant.is_qualify, member.name")->result('array');
    }

    public function deleteEventMember($id_participant)
    {
        $this->db->query("DELETE FROM event_participant WHERE id_participant = $id_participant");
    }

    public function editEventMember($id_participant, $data)
    {
        $this->db->update('event_participant', $data, array('id_participant' => $id_participant));
    }

    public function addEventMember($data)
    {
        $this->db->insert('event_participant', $data);
    }

    public function getMemberForEvent($id_event, $id_dojo)
    {
        $members = $this->db->query("SELECT member.id_member, member.name, member. username, rank.name AS rank FROM member JOIN rank ON member.id_rank = rank.id_rank WHERE member.id_dojo = $id_dojo ORDER BY member.name")->result('array');

        $alreadyExist = $this->db->query("SELECT event_participant.id_member FROM event_participant JOIN member ON event_participant.id_member = member.id_member WHERE member.id_dojo = $id_dojo AND event_participant.id_event = $id_event")->result('array');

        $result = array();

        foreach ($members as $member) {
            if (!$this->isMemberExist($member['id_member'], $alreadyExist)) {
                $result[] = $member;
            }
        }

        return $result;
    }

    public function getEvent($id_master, $id_dojo)
    {
        return $this->db->query("SELECT event.id_event, event.datetime, event.descrip, IF((SELECT treasurer FROM event_permission WHERE id_dojo = $id_dojo AND id_event = event.id_event) IS NULL,0,(SELECT treasurer FROM event_permission WHERE id_dojo = $id_dojo AND id_event = event.id_event)) as treasurer, IF((SELECT secretary FROM event_permission WHERE id_dojo = $id_dojo AND id_event = event.id_event) IS NULL,0,(SELECT secretary FROM event_permission WHERE id_dojo = $id_dojo AND id_event = event.id_event)) as secretary FROM event WHERE event.id_master = $id_master AND event.is_done = 0 ORDER BY event.datetime DESC")->result('array');
    }

    public function editEventPermission($id_permission, $data)
    {
        $this->db->update('event_permission', $data, array('id_permission' => $id_permission));
    }

    public function addEventPermission($data)
    {
        $this->db->insert('event_permission', $data);
    }

    public function getIDEventPermission($id_dojo, $id_event)
    {
        $result = $this->db->query("SELECT id_permission FROM event_permission WHERE id_event = $id_event AND id_dojo = $id_dojo")->result('array');
        if ($result != NULL) {
            return $result[0]['id_permission'];
        } else {
            return NULL;
        }
    }

    public function getEventData($id_event)
    {
        return $this->db->query("SELECT id_event, descrip, datetime, is_done FROM event WHERE id_event = $id_event")->result('array');
    }

    public function checkEventParticipant($id_event, $id_member)
    {
        $data = $this->db->query("SELECT id_event, descrip, datetime, is_done FROM event WHERE id_event = $id_event")->result('array');

        if ($data == null) {
            return array('status' => false, 'message' => "Event not found");
        } else {
            $event = $data[0];
            // Event sudah selesai
            if ($event['is_done'] == 1) {
                return array('status' => false, 'message' => "The event has been completed");
            } else {
                $isExist = $this->db->query("SELECT id_participant, is_qualify, IF(datetime IS NULL,0,1) AS attend FROM event_participant WHERE id_event = $id_event AND id_member = $id_member")->result('array');
                // Check data member terdaftar
                if ($isExist == NULL) {
                    return array('status' => false, 'message' => "You are not registered");
                } else {
                    $dataExist = $isExist[0];
                    //cek member memenuhi syarat
                    if ($dataExist['is_qualify'] == 0) {
                        return array('status' => false, 'message' => "You don't qualify");
                    } else {
                        // cek member sudah presensi
                        if ($dataExist['attend'] == 1) {
                            return array('status' => false, 'message' => "You already present");
                        } else {
                            $date = $event['datetime'];
                            $currentTime = strtotime(date('H:i:s'));

                            $startTime = strtotime("-10 minutes", strtotime($date));
                            $endTime = strtotime("+30 minutes", strtotime($date));

                            if ($currentTime >= $startTime) {
                                if ($currentTime <= $endTime) {
                                    $result = array();
                                    $result['id_participant'] = $dataExist['id_participant'];
                                    $result['id_event'] = $event['id_event'];
                                    $result['descrip'] = $event['descrip'];
                                    $result['datetime'] = $event['datetime'];

                                    return array('status' => true, 'message' => $result);
                                } else {
                                    return array('status' => false, 'message' => "Already late, presence closes 30 minutes after schedule");
                                }
                            } else {
                                return array('status' => false, 'message' => "Too early, presence opens 10 minutes before schedule");
                            }
                        }
                    }
                }
            }
        }
    }

    public function getMemberEvent($id_member)
    {
        return $this->db->query("SELECT event_participant.id_participant, event.datetime, event.descrip, event_participant.is_qualify, IF(event_participant.datetime IS NULL,0,1) as is_attend FROM event_participant JOIN event ON event_participant.id_event = event.id_event WHERE event_participant.id_member = $id_member ORDER BY event_participant.datetime")->result('array');
    }

    public function getMemberPresence($id_member)
    {
        return $this->db->query("SELECT presence.id_presence, dojo.name AS dojo, IF(presence.note IS NULL,0,1) AS isNoteExist, presence.photo, presence.datetime, history_presence.datetime AS time,history_presence.is_manual FROM history_presence JOIN presence ON history_presence.id_presence = presence.id_presence JOIN dojo ON presence.id_dojo = dojo.id_dojo WHERE history_presence.id_member = $id_member ORDER BY datetime DESC")->result('array');
    }

    public function getHistoryFeeMember($id_member)
    {
        return $this->db->query("SELECT fee_history.id_history, fee_history.month, fee_history.fee, fee_history.date, fee_history.is_late FROM fee_history JOIN member ON fee_history.id_member = member.id_member WHERE fee_history.id_member = $id_member ORDER BY fee_history.date DESC")->result('array');
    }

    public function getListDojoInstructor($id_member)
    {
        return $this->db->query("SELECT id_dojo, name FROM dojo WHERE id_instructor = $id_member ORDER BY name")->result('array');
    }

    public function getPresenceInstructor($id_member)
    {
        return $this->db->query("SELECT presence.id_presence, dojo.name AS dojo, presence.datetime, IF((SELECT id_member FROM history_presence WHERE id_presence = presence.id_presence AND id_member = $id_member) IS NULL,0,1) AS isAttend, IF(presence.note IS NULL,'null',presence.note) AS note, IF(presence.note IS NULL,0,1) AS isNoteExist, presence.photo FROM presence JOIN dojo ON presence.id_dojo = dojo.id_dojo WHERE presence.id_instructor = $id_member ORDER BY presence.datetime DESC")->result('array');
    }

    public function getPresenceAssistant($id_dojo)
    {
        return $this->db->query("SELECT presence.id_presence, dojo.name AS dojo, presence.datetime, IF((SELECT id_member FROM history_presence WHERE id_presence = presence.id_presence AND id_member = dojo.id_assistant) IS NULL,0,1) AS isAttend, IF(presence.note IS NULL,'null',presence.note) AS note, IF(presence.note IS NULL,0,1) AS isNoteExist, presence.photo FROM presence JOIN dojo ON presence.id_dojo = dojo.id_dojo WHERE presence.id_dojo = $id_dojo ORDER BY presence.datetime DESC")->result('array');
    }

    public function updateNote($id_presence, $note)
    {
        $this->db->update('presence', array('note' => $note), array('id_presence' => $id_presence));
    }

    public function addNoteSpecial($array)
    {
        $this->db->insert('note_special', $array);
    }

    public function editNoteSpecial($id_note, $array)
    {
        $this->db->update('note_special', $array, array('id_note' => $id_note));
    }

    public function deleteNoteSpecial($id_note)
    {
        $this->db->query("DELETE FROM note_special WHERE id_note = $id_note");
    }

    public function getSpecialNote($id_presence)
    {
        return $this->db->query("SELECT note_special.id_note, rank.name AS rank, note_special.note FROM note_special JOIN rank ON note_special.id_rank = rank.id_rank WHERE id_presence = $id_presence ORDER BY note_special.id_rank")->result('array');
    }


    public function getTreasurerSecretaryPresence($id_dojo, int $role)
    {
        $where = "";
        if ($role == 2) {
            $where = "presence.treasurer_permission = 1";
        } else {
            $where = "presence.secretary_permission = 1";
        }

        $date = date('Y-m-d H:i:s', strtotime("-1 day", strtotime(date('Y-m-d H:i:s'))));

        return $this->db->query("SELECT id_presence, datetime FROM presence WHERE id_dojo = $id_dojo AND $where AND datetime > '$date' ORDER BY datetime DESC")->result('array');
    }

    public function editMemberCertificate($id_member, $array)
    {
        $this->db->update('member', $array, array('id_member' => $id_member));
    }

    public function getExamMember($id_member)
    {
        return $this->db->query("SELECT participant.id_participant, exam.datetime, (SELECT name FROM rank WHERE id_rank = participant.from_rank) AS from_rank, (SELECT name FROM rank WHERE id_rank = participant.to_rank) AS to_rank, participant.isRecommend, exam.status AS is_done, participant.status AS result FROM participant JOIN exam ON participant.id_exam = exam.id_exam WHERE participant.id_member = $id_member ORDER BY exam.datetime DESC")->result('array');
    }

    public function checkNameRequestCodeUnix($id_dojo, $name, $request_code)
    {
        $id_master = $this->db->query("SELECT id_master FROM dojo WHERE id_dojo = $id_dojo")->result('array')[0]['id_master'];

        $checkName = $this->db->query("SELECT id_dojo FROM dojo WHERE id_master = $id_master AND name = '$name' AND id_dojo <> $id_dojo")->result('array');

        $checkCodeRequest = $this->db->query("SELECT id_dojo FROM dojo WHERE request_code IS NOT NULL AND request_code = '$request_code' AND id_dojo <> $id_dojo")->result('array');

        $result = true;
        if ($checkName != null || $checkCodeRequest != null) {
            $result = false;
        }

        return array(
            'status' => $result,
            'name' => $checkName == null,
            'request_code' => $checkCodeRequest == null
        );
    }

    public function getDetailMemberExcel($id_dojo)
    {
        $dojo = $this->db->query("SELECT dojo.name AS dojo, master.dojo_name AS dojo_name, master.up_rank AS up_rank, master.low_rank AS low_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE id_dojo = $id_dojo")->result('array');

        $member = $this->db->query("SELECT member.id_member, member.name, IF(member.status = 0, 'Active', 'Not active') AS status, member.certificate_number, member.username, rank.name AS rank, role.name AS role, member.period, member.expiration, IF(member.id_fee = 1, (SELECT fee1 FROM dojo WHERE id_dojo = member.id_dojo), IF(member.id_fee = 2, (SELECT fee2 FROM dojo WHERE id_dojo = member.id_dojo), (SELECT fee3 FROM dojo WHERE id_dojo = member.id_dojo))) AS fee, member.attendance, IF(member.attendance=0,0,(SELECT COUNT(id_member) FROM history_presence WHERE id_member = member.id_member AND id_dojo = $id_dojo)) AS own, member.birth_date, member.blood, member.disease, member.phone1, member.phone2, member.address, member.place_of_birth FROM member JOIN rank ON member.id_rank = rank.id_rank JOIN role ON member.id_role = role.id WHERE id_dojo = $id_dojo ORDER BY member.status, member.id_role, member.name, member.id_rank")->result('array');

        $member_result = array();
        foreach ($member as $m) {
            $rank = $m["rank"];
            $substring = substr($rank, 0, strlen($rank) - 3);

            if (substr($rank, strlen($rank) - 3, strlen($rank)) == "Dan") {
                $rank = $substring . $dojo[0]['up_rank'];
            } else {
                $rank = $substring . $dojo[0]['low_rank'];
            }
            $m["rank"] = $rank;

            if ($m["role"] == "Head") {
                $m["role"] = "Chairman";
            }

            array_push($member_result, $m);
        }

        if ($dojo != null) {
            return array(
                'dojo' => $dojo[0]['dojo'],
                'dojo_name' => $dojo[0]['dojo_name'],
                'up_rank' => $dojo[0]['up_rank'],
                'low_rank' => $dojo[0]['low_rank'],
                'member' => $member_result
            );
        } else {
            return null;
        }
    }

    public function getExamMemberExcel($id_dojo, $id_exam)
    {
        $exam = $this->db->query("SELECT datetime, descrip, (SELECT name FROM dojo WHERE id_dojo = $id_dojo) AS dojo, (SELECT master.dojo_name FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS dojo_name, (SELECT master.up_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS up_rank, (SELECT master.low_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS low_rank FROM exam WHERE id_exam = $id_exam")->result('array');

        $participant = $this->db->query("SELECT member.id_member, member.name, member.certificate_number, (SELECT name FROM rank WHERE id_rank = participant.from_rank) AS from_rank, (SELECT name FROM rank WHERE id_rank = participant.to_rank) AS to_rank, IF(participant.isRecommend = 0, 'Yes', 'No') AS isRecommend, member.username, rank.name AS rank FROM participant JOIN member ON participant.id_member = member.id_member JOIN rank ON member.id_rank = rank.id_rank WHERE member.id_dojo = $id_dojo AND participant.id_exam = $id_exam ORDER BY member.name, member.id_rank")->result('array');

        $participant_result = array();
        foreach ($participant as $p) {
            $rank = $p["rank"];
            $rank1 = $p["from_rank"];
            $rank2 = $p["to_rank"];
            $substring = substr($rank, 0, strlen($rank) - 3);
            $substring1 = substr($rank1, 0, strlen($rank1) - 3);
            $substring2 = substr($rank2, 0, strlen($rank2) - 3);

            if (substr($rank, strlen($rank) - 3, strlen($rank)) == "Dan") {
                $rank = $substring . $exam[0]['up_rank'];
            } else {
                $rank = $substring . $exam[0]['low_rank'];
            }
            $p["rank"] = $rank;

            if (substr($rank1, strlen($rank1) - 3, strlen($rank1)) == "Dan") {
                $rank1 = $substring1 . $exam[0]['up_rank'];
            } else {
                $rank1 = $substring1 . $exam[0]['low_rank'];
            }
            $p["from_rank"] = $rank1;

            if (substr($rank2, strlen($rank2) - 3, strlen($rank2)) == "Dan") {
                $rank2 = $substring2 . $exam[0]['up_rank'];
            } else {
                $rank2 = $substring2 . $exam[0]['low_rank'];
            }
            $p["to_rank"] = $rank2;

            array_push($participant_result, $p);
        }

        if ($exam != null) {
            return array(
                'exam' => $exam[0],
                'participant' => $participant_result
            );
        } else {
            return null;
        }
    }

    public function getExamResultExcel($id_dojo, $id_exam, $timezone)
    {
        $exam = $this->db->query("SELECT datetime, descrip, (SELECT name FROM dojo WHERE id_dojo = $id_dojo) AS dojo, (SELECT master.dojo_name FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS dojo_name, (SELECT master.up_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS up_rank, (SELECT master.low_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS low_rank FROM exam WHERE id_exam = $id_exam")->result('array');

        $participant = $this->db->query("SELECT member.id_member, member.name, IF(participant.status = 1, 'Yes','No') AS status, member.certificate_number, (SELECT name FROM rank WHERE id_rank = participant.from_rank) AS from_rank, (SELECT name FROM rank WHERE id_rank = participant.to_rank) AS to_rank, IF(participant.isRecommend = 0, 'Yes', 'No') AS isRecommend, member.username, rank.name AS rank FROM participant JOIN member ON participant.id_member = member.id_member JOIN rank ON member.id_rank = rank.id_rank WHERE member.id_dojo = $id_dojo AND participant.id_exam = $id_exam ORDER BY member.name, member.id_rank")->result('array');

        $participant_result = array();
        foreach ($participant as $p) {
            $rank = $p["rank"];
            $rank1 = $p["from_rank"];
            $rank2 = $p["to_rank"];
            $substring = substr($rank, 0, strlen($rank) - 3);
            $substring1 = substr($rank1, 0, strlen($rank1) - 3);
            $substring2 = substr($rank2, 0, strlen($rank2) - 3);

            if (substr($rank, strlen($rank) - 3, strlen($rank)) == "Dan") {
                $rank = $substring . $exam[0]['up_rank'];
            } else {
                $rank = $substring . $exam[0]['low_rank'];
            }
            $p["rank"] = $rank;

            if (substr($rank1, strlen($rank1) - 3, strlen($rank1)) == "Dan") {
                $rank1 = $substring1 . $exam[0]['up_rank'];
            } else {
                $rank1 = $substring1 . $exam[0]['low_rank'];
            }
            $p["from_rank"] = $rank1;

            if (substr($rank2, strlen($rank2) - 3, strlen($rank2)) == "Dan") {
                $rank2 = $substring2 . $exam[0]['up_rank'];
            } else {
                $rank2 = $substring2 . $exam[0]['low_rank'];
            }
            $p["to_rank"] = $rank2;

            array_push($participant_result, $p);
        }

        if ($exam != null) {

            $schedule_date = new DateTime($exam[0]['datetime'], new DateTimeZone('Asia/Jakarta'));
            $schedule_date->setTimezone(new DateTimeZone("+0" . $timezone . "00"));

            $exam[0]['datetime_new'] = $schedule_date->format('l, j F Y H:i');
            $exam[0]['datetime'] = $schedule_date->format('Y-m-d H:i');

            return array(
                'exam' => $exam[0],
                'participant' => $participant_result
            );
        } else {
            return null;
        }
    }

    public function getEventParticipantExcel($id_dojo, $id_event, $timezone)
    {
        $event = $this->db->query("SELECT datetime, descrip, (SELECT name FROM dojo WHERE id_dojo = $id_dojo) AS dojo, (SELECT master.dojo_name FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS dojo_name, (SELECT master.up_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS up_rank, (SELECT master.low_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS low_rank FROM event WHERE id_event = $id_event")->result('array');

        $member = $this->db->query("SELECT member.id_member, member.name, IF(event_participant.is_qualify = 0, 'No','Yes') AS is_qualify, member.certificate_number, member.username, rank.name AS rank FROM event_participant JOIN member ON event_participant.id_member = member.id_member JOIN rank ON member.id_rank = rank.id_rank WHERE event_participant.id_event = $id_event AND member.id_dojo = $id_dojo ORDER BY event_participant.is_qualify DESC, member.name, member.id_rank")->result('array');

        $member_result = array();
        foreach ($member as $m) {
            $rank = $m["rank"];
            $substring = substr($rank, 0, strlen($rank) - 3);

            if (substr($rank, strlen($rank) - 3, strlen($rank)) == "Dan") {
                $rank = $substring . $event[0]['up_rank'];
            } else {
                $rank = $substring . $event[0]['low_rank'];
            }
            $m["rank"] = $rank;

            array_push($member_result, $m);
        }

        if ($event != null) {
            $schedule_date = new DateTime($event[0]['datetime'], new DateTimeZone('Asia/Jakarta'));
            $schedule_date->setTimezone(new DateTimeZone("+0" . $timezone . "00"));

            $event[0]['datetime_new'] = $schedule_date->format('l, j F Y H:i');
            $event[0]['datetime'] = $schedule_date->format('Y-m-d H:i');

            return array(
                'event' => $event[0],
                'member' => $member_result
            );
        } else {
            return null;
        }
    }

    public function getEventParticipantAttendExcel($id_dojo, $id_event, $timezone)
    {
        $event = $this->db->query("SELECT datetime, descrip, (SELECT name FROM dojo WHERE id_dojo = $id_dojo) AS dojo, (SELECT master.dojo_name FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS dojo_name, (SELECT master.up_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS up_rank, (SELECT master.low_rank FROM dojo JOIN master ON dojo.id_master = master.id_master WHERE dojo.id_dojo = $id_dojo) AS low_rank FROM event WHERE id_event = $id_event")->result('array');

        $member = $this->db->query("SELECT member.id_member, member.name, event_participant.datetime AS attend, member.certificate_number, member.username, rank.name AS rank FROM event_participant JOIN member ON event_participant.id_member = member.id_member JOIN rank ON member.id_rank = rank.id_rank WHERE event_participant.id_event = $id_event AND event_participant.datetime IS NOT NULL AND member.id_dojo = $id_dojo ORDER BY member.name, member.id_rank")->result('array');

        $member_result = array();
        foreach ($member as $m) {
            $rank = $m["rank"];
            $substring = substr($rank, 0, strlen($rank) - 3);

            if (substr($rank, strlen($rank) - 3, strlen($rank)) == "Dan") {
                $rank = $substring . $event[0]['up_rank'];
            } else {
                $rank = $substring . $event[0]['low_rank'];
            }
            $m["rank"] = $rank;

            $schedule_date = new DateTime($m['attend'], new DateTimeZone('Asia/Jakarta'));
            $schedule_date->setTimezone(new DateTimeZone("+0" . $timezone . "00"));

            $m['attend'] = $schedule_date->format('Y-m-d H:i');

            array_push($member_result, $m);
        }

        if ($event != null) {

            $schedule_date = new DateTime($event[0]['datetime'], new DateTimeZone('Asia/Jakarta'));
            $schedule_date->setTimezone(new DateTimeZone("+0" . $timezone . "00"));

            $event[0]['datetime_new'] = $schedule_date->format('l, j F Y H:i');
            $event[0]['datetime'] = $schedule_date->format('Y-m-d H:i');

            return array(
                'event' => $event[0],
                'member' => $member_result
            );
        } else {
            return null;
        }
    }

    public function checkPeriodDojo($id_dojo)
    {
        $period = $this->db->query("SELECT period FROM dojo WHERE id_dojo = $id_dojo")->result('array');
        if ($period != null) {
            $period = $period[0]['period'];
            $threshold = date('Y-m-d', strtotime("+11 month", strtotime($period)));

            if (date('Y-m-d') > $threshold) {
                $datetime = $period . ' 00:00:00';
                $check = $this->db->query("SELECT exam.datetime, exam.id_exam FROM participant JOIN member ON participant.id_member = member.id_member JOIN exam ON participant.id_exam = exam.id_exam WHERE member.id_dojo = $id_dojo AND exam.datetime > '$datetime' GROUP BY exam.id_exam")->result('array');

                if ($check == null) {
                    return false;
                } else {
                    if (count($check) < 2) {
                        return false;
                    } else {
                        return true;
                    }
                }
            } else {
                return true;
            }
        } else {
            return null;
        }
    }

    public function codeForJoinNotNull($id_dojo)
    {
        $code = $this->db->query("SELECT request_code FROM dojo WHERE id_dojo = $id_dojo")->result('array');
        if ($code[0]['request_code'] != null) {
            return true;
        }
        return false;
    }

    public function submitCodeRequestDojo($id_dojo, $code)
    {
        $this->db->query("UPDATE dojo SET request_code = '$code' WHERE id_dojo = $id_dojo");
    }

    public function checkRequestCodeUnix($id_dojo, $code)
    {
        $result = $this->db->query("SELECT id_dojo FROM dojo WHERE request_code IS NOT NULL AND request_code = '$code' AND id_dojo <> $id_dojo")->result('array');

        return $result == null;
    }

    public function getDojoName($id_master)
    {
        return $this->db->query("SELECT dojo_name, low_rank, up_rank FROM master WHERE id_master = $id_master")->result('array');
    }

    public function getFeeDojo($id_dojo)
    {
        $fee = $this->db->query("SELECT fee1, fee2, fee3 FROM dojo WHERE id_dojo = $id_dojo")->result('array');
        if ($fee != null) {
            return $fee[0];
        } else {
            return null;
        }
    }

    public function editFeeMember($id_member, $fee)
    {
        $this->db->query("UPDATE member SET id_fee = $fee WHERE id_member = $id_member");
    }

    public function getRole($id_role)
    {
        switch ($id_role) {
            case 1:
                return "Chairman";
                break;

            case 2:
                return "Treasurer";
                break;

            case 3:
                return "Secretary";
                break;

            case 4:
                return "Instructor";
                break;

            case 5:
                return "Assistant Instructor";
                break;

            case 6:
                return "Member";
                break;
        }
    }

    public function getHistoryFeeExcel($id_dojo, $minDateRaw, $maxDateRaw)
    {
        $minDate = $minDateRaw . ' 00:00:00';
        $maxDate = $maxDateRaw . ' 23:59:59';

        if ($minDateRaw == 0 && $maxDateRaw == 0) {
            $fees = $this->db->query("SELECT fee_history.id_history, member.name, member.id_role, fee_history.month, fee_history.fee, fee_history.date, fee_history.is_late FROM fee_history JOIN member ON fee_history.id_member = member.id_member WHERE fee_history.id_dojo = $id_dojo ORDER BY fee_history.date DESC, name")->result('array');
        } else if ($maxDateRaw == 0) {
            $fees = $this->db->query("SELECT fee_history.id_history, member.name, member.id_role, fee_history.month, fee_history.fee, fee_history.date, fee_history.is_late FROM fee_history JOIN member ON fee_history.id_member = member.id_member WHERE fee_history.id_dojo = $id_dojo AND fee_history.date >= '$minDate' ORDER BY fee_history.date DESC, name")->result('array');
        } else if ($minDateRaw == 0) {
            $fees = $this->db->query("SELECT fee_history.id_history, member.name, member.id_role, fee_history.month, fee_history.fee, fee_history.date, fee_history.is_late FROM fee_history JOIN member ON fee_history.id_member = member.id_member WHERE fee_history.id_dojo = $id_dojo AND fee_history.date <= '$maxDate' ORDER BY fee_history.date DESC, name")->result('array');
        } else {
            $fees = $this->db->query("SELECT fee_history.id_history, member.name, member.id_role, fee_history.month, fee_history.fee, fee_history.date, fee_history.is_late FROM fee_history JOIN member ON fee_history.id_member = member.id_member WHERE fee_history.id_dojo = $id_dojo AND fee_history.date >= '$minDate' AND fee_history.date <= '$maxDate' ORDER BY fee_history.date DESC, name")->result('array');
        }

        $result = array();

        foreach ($fees as $fee) {
            $fee['role'] = $this->getRole($fee['id_role']);
            $fee['date'] = substr($fee['date'], 0, 10);
            if ($fee['is_late'] == 1) {
                $fee['late'] = 'Yes';
            } else {
                $fee['late'] = 'No';
            }
            array_push($result, $fee);
        }

        return $result;
    }
}
