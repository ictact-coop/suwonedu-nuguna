<?php
/**
 * @file   ko.lang.php
 * @author Perbiz (http://perbiz.co.kr)
 * @brief  SMS 모듈의 기본 언어팩
 **/
// 버튼에 사용되는 언어
$lang->cmd_sms_remain_point = '잔여건수';
$lang->cmd_sms_update_point = 'SMS 구매';


$lang->cmd_sms_reset_db     =  'DB 삭제 후 재설치의 경우';
$lang->cmd_sms_reset_server =  '서버이전으로 재설치의 경우';
$lang->sms_join_first       = 'smsXE 를 처음 사용하는 경우';



$lang->cmd_sms_board_search = '게시판 검색';
$lang->cmd_point_sync       = '잔여건수 확인';

$lang->js_point_210_msg     = 'SMS 잔여건수 확인 성공';
$lang->js_point_11_msg      = 'SMS 잔여건수 확인 실패';

$lang->cmd_sms_admin_use    = '관리자 SMS 사용';
$lang->cmd_sms_user_use     = '사용자 SMS 사용';
$lang->cmd_sms_extra_vars   = '확장변수 설정';
$lang->cmd_sms_cate_vars    = '카테고리 사용';
$lang->cmd_sms_cate_set_button = '설정';
$lang->cmd_sms_log_set      = 'SMS 로그 설정';
$lang->cmd_sms_cate_set     = '카테고리 설정';
$lang->cmd_sms_get_pcs      = '수신번호';

$lang->cmd_sms_search_id    = '추적아이디';
$lang->cmd_sms_search_use_id= '추적아이디 사용';
$lang->cmd_sms_search_set_id= '추적아이디 설정';


$lang->cmd_sms_search_year  = '년별 검색';
$lang->cmd_sms_search_month = '월별 검색';
$lang->cmd_sms_search_day   = '일별 검색';



$lang->join_form_head       = '초기설정의 경우 일반회원의 가입폼에서 수신받을 휴대폰번호 설정을 추가하세요';
$lang->join_sms         = '회원가입';
$lang->sms_not_install  = '먼저 SMS 모듈을 설치하세요';
$lang->sms_not_socket   = '서버에서 fsockopen 함수를 지원하지 않습니다.<br /> 서버설정사항을 먼저 확인하세요';

$lang->ezcro_agreement  = '이지크로 이용약관';
$lang->ezcro_agreement_view = '이지크로 이용약관 보기';
$lang->ezcro_agreement_msg  = '이지크로 이용약관에 동의합니다';

$lang->sms_about_user_id  = '사용자 아이디는 이메일주소형식을 사용합니다';

$lang->phone1           = '휴대폰번호';
$lang->phone2           = '휴대폰번호';
$lang->phone3           = '휴대폰번호';

$lang->remain_sms1      = '회신번호';
$lang->remain_sms2      = '회신번호';
$lang->remain_sms3      = '회신번호';
$lang->pcsfield         = '설정된 휴대폰입력폼';
$lang->rec_pcs      = '보내는 사람';

$lang->js_add_pcs_msg   = '전화번호추가는 10개까지만 가능합니다';
$lang->js_add_wrong_pcs_msg = '전화번호는 숫자로 입력해주세요.';
$lang->js_already_addrset = '다른 설정창이 열려있습니다';

$lang->join_sms_info1   = 'SMS 서비스를 이용하기 위해서는 아래의 회원가입절차를 진행해야 합니다.';
$lang->join_sms_info2   = 'SMS 서비스를 이용하기 위해서는 서비스이용약관과 개인정보취급방침에 동의해야 합니다.';

$lang->js_sms_new_add_pcs    = '회원가입시 수신받을 휴대폰번호 설정 추가';

$lang->member_sms_service   = '서비스 이용약관';
$lang->member_sms_user_info = '개인정보 처리방침';
$lang->member_sms_user_agree    = '개인정보 처리방침에 동의합니다';
$lang->member_sms_service_agree= '서비스 이용약관에 동의합니다';
$lang->member_sms_tail_text = '보기';
$lang->js_sms_tail_text = '을 확인하세요!';

$lang->js_sms_service_agree   = '먼저 서비스 이용약관에 동의하셔야 합니다.';
$lang->js_sms_user_agree      = '먼저 개인정보 보호방침에 동의하셔야 합니다.';

$lang->js_sms_confirm_direct  = 'SMS를 발송하시겠습니까?';
// 목록
$lang->cmd_sms_number   = '번호';
$lang->cmd_sms_board    = '게시판모듈명';

$lang->cmd_sms_board_copy= 'SMS환경설정 복사';
$lang->js_lang_copy_choice = '선택된 게시판모듈이 없습니다.';

$lang->cmd_sms_static   = '통 계';
$lang->cmd_sms_static_board  = '게시판별 SMS 통계';
$lang->cmd_set_reauthkey = '인증키 재발급';



$lang->cmd_sms_rdate    = '등록일';
$lang->cmd_sms_use_all  = '전체';
$lang->cmd_sms_use_yes  = '사용';
$lang->cmd_sms_use_no   = '사용안함';

$lang->cmd_sms_total_mount = '총 SMS 전송 건수';
$lang->cmd_sms_send_unit   = '건';
$lang->cmd_sms_stat_date   = '날짜';
$lang->cmd_sms_domain      = '도메인';
$lang->cmd_sms_send_num    = 'SMS 전송 횟수';
$lang->cmd_sms_detail_view = '상세보기';

$lang->cmd_sms_auth_key    = '인증키';
$lang->cmd_sms_add_list    = '목록';
$lang->cmd_sms_member      = '회원';
$lang->cmd_sms_more_text   = '상세보기';
$lang->cmd_notResult       = '그룹내 회원이 존재하지 않습니다';
$lang->addPcsButton        = '직접발송번호추가';
$lang->cmd_signup_go       = '회원가입하기';

// 폼
$lang->search_result         = '검색결과';
$lang->cmd_sms_default_pcs   = '대표회신번호';
$lang->cmd_sms_pcs_fmsg      = 'SMS 전송시 기본회신번호';
$lang->cmd_sms_set_pcs       = '설정된 휴대폰 입력폼';
$lang->cmd_sms_set_pcs2      = '휴대폰 입력폼';

$lang->cmd_sms_nonact        = '비활성화됨';
$lang->cmd_sms_set_xepcs     = 'XE회원가입폼에 설정된 활성화된 휴대폰번호입력필드';

$lang->cmd_sms_addr_set      = '수신인 설정';
$lang->cmd_sms_set_button    = '설 정';
$lang->cmd_sms_addr_direct   = '수신인';
$lang->cmd_sms_addr_direct_board = '게시판별 수신인';

$lang->cmd_sms_addr_view     = '수신인 보기';
$lang->cmd_sms_addr_add      = '수신인 추가';

$lang->cmd_sms_return_set_pcs    = '회신번호 설정';
$lang->cmd_sms_return_pcs    = '회신번호';
$lang->cmd_sms_appoint_send  = '예약발송';
$lang->cmd_sms_appoint_time  = '예약시간';
$lang->cmd_sms_appoint_day   = '일';
$lang->cmd_sms_appoint_am    = '오전';
$lang->cmd_sms_appoint_pm    = '오후';
$lang->cmd_sms_appoint_hour  = '시';
$lang->cmd_sms_appoint_min   = '분';

$lang->cmd_sms_sample        = '작성예';
$lang->cmd_sms_use           = 'SMS 사용';
$lang->cmd_sms_head_text     = '머릿글 설정';
$lang->cmd_sms_head_msg      = '문자메시지 첫머리에 나올 머릿글 내용입니다';
$lang->cmd_sms_set_site      = '기본설정[사이트명]';
$lang->cmd_sms_set_direct    = '직접설정';
$lang->cmd_sms_send_text     = '발송 내용';
$lang->cmd_sms_user_id_msg   = '회원각각의 아이디';
$lang->cmd_sms_user_name_msg = '회원각각의 이름';
$lang->cmd_sms_user_id       = '아이디';
$lang->cmd_sms_user_name     = '이름';

$lang->cmd_sms_set_board_new_use = '새글 SMS 사용';
$lang->cmd_sms_set_board_new_modify_use = '새글 수정시 SMS 사용';
$lang->cmd_sms_set_board_new = '새글 SMS 내용';
$lang->cmd_sms_board_title   = '게시물 제목';
$lang->cmd_sms_board_msg     = '님께 알립니다';

$lang->cmd_sms_set_board_reply_modify_use = '댓글 수정시 SMS 사용';
$lang->cmd_sms_set_board_reply_use = '댓글 SMS 사용';
$lang->cmd_sms_set_board_reply     = '댓글 SMS 내용';
$lang->cmd_sms_board_content       = '댓글 내용';

$lang->join_sms_pcs_msg       = '아이디/비밀번호 및 기타문자 안내시에 사용될 회원기본정보임';
$lang->join_sms_return_msg    = 'SMS 전송시 기본회신번호';
$lang->join_sms_pcs_onclick   = '확인';
$lang->default_domain         = '기본도메인';
$lang->cmd_sms_addDomain_msg  = 'http://www 는 생략하세요';
$lang->cmd_sms_gun            = '건';

$lang->setup_option             = '추가 기능 설정';
// 회원가입시
$lang->join_sms_send            = '회원가입시 알림';
$lang->join_sms_send_admin      = '관리자에게 알림';
$lang->join_sms_send_member     = '가입자에게 알림';
$lang->join_sms_admin_text      = '관리자 SMS';
$lang->join_sms_member_text     = '가입자 SMS';
$lang->join_sms_admin_about     = '신규회원가입시 관리자에게 SMS를 발송합니다.<br />아이디 : #id#, 이름 : #name#, 닉네임 : #nick#, 이메일 : #email#';
$lang->join_sms_member_about    = '신규회원가입시 가입자에게 SMS를 발송합니다.<br />아이디 : #id#, 이름 : #name#, 닉네임 : #nick#, 이메일 : #email#';

// 잔여 건수 알림
$lang->setup_sms_remain_alert              = '잔여 수 알림';
$lang->setup_sms_remain_alert_use          = '잔여 수 알림 받기';
$lang->setup_sms_remain_alert_setup        = '알림 설정';
$lang->setup_sms_remain_alert_item         = '건 잔여시 알림';
$lang->setup_sms_remain_alert_item_about   = '콤마(,)로 구분하여 여러 항목에 대하여 알림을 받을 수 있습니다.';

// 발송 시간 제한
$lang->setup_sms_idle_setup        = '발송시간제한 설정';
$lang->setup_use_sms_idle          = '발송시간제한 사용';
$lang->setup_sms_idle_time         = '발송 제한 시간';
$lang->setup_sms_idle_time_about   = '지정한 시간동안 메시지를 발송하지 않습니다.';

// 설정 페이지
$lang->sms_remain_alert       = '발송가능한 문자메시지가 %d건 남았습니다.';
$lang->sms_none_alert         = '발송가능한 문자메시지가 없습니다. 충전해주세요.';


//팝업 메뉴
$lang->cmd_sms_all_select    = '전체 회원 선택';
$lang->cmd_sms_group_view    = '그룹으로 설정';
$lang->cmd_sms_user_view     = '회원으로 설정';
$lang->cmd_sms_sort          = '구분';
$lang->cmd_sms_group         = '그룹';
$lang->cmd_sms_group_name    = '그룹명';
$lang->cmd_sms_name_id       = '이름';
$lang->cmd_sms_nick_name     = '닉네임';
$lang->cmd_sms_user_id       = '아이디';
$lang->cmd_sms_bigo          = '비고';
$lang->cmd_sms_groupIn       = '설정된 그룹내 회원';
$lang->cmd_sms_userInfo      = '회원정보';
$lang->cmd_sms_pcs           = '휴대폰번호';
$lang->cmd_sms_group_total   = '그룹 총 회원 수';
$lang->cmd_sms_allIn         = '전체 회원 설정';
$lang->cmd_sms_all_out       = '전체 회원 선택 해제';

// 탭메뉴에 사용되는 언어
$lang->cmd_board_list       = 'SMS사용 게시판 목록';
$lang->cmd_sms_menu_static  = 'SMS 통계';
$lang->cmd_sms_menu_domain  = '도메인 추가';
$lang->cmd_sms_menu_default = '기본설정';
$lang->cmd_sms_menu_set     = '설정';

$lang->cmd_sms_menu_direct  = 'SMS 직접발송';
$lang->cmd_sms_button_direct= 'SMS 발송';

// 자바스크립트..
$lang->js_fail_system_msg   = '필수인자값이 부족합니다';
$lang->msg_not_enough_point = '포인트가 부족합니다';
$lang->js_reauthkey_succ_msg= '인증키가 재발행되었습니다';
$lang->js_reauthkey_fail_msg= '인증키가 재발행에 실패하였습니다';
$lang->js_set_addrset_msg   = '주소록설정이 되지 않았습니다';
$lang->js_set_ok_msg        = '설정에 성공하였습니다';
$lang->js_set_direct_msg    = '직접설정할 내용을';
$lang->js_set_pcs_msg       = '회신번호를';
$lang->js_set_searchId_msg  = '수신번호를';
$lang->js_set_date_msg      = '날짜를';
$lang->js_set_hour_msg      = '시간을';
$lang->js_set_min_msg       = '분을';
$lang->js_set_hours_msg       = '시간은';
$lang->js_set_tail_input_msg  = ' 입력하세요';
$lang->js_set_tail_number_msg = ' 반드시 숫자로만 입력해야 합니다';

$lang->js_set_wrong_hour_msg  = '잘못된 시간입니다';
$lang->js_set_wrong_min_msg  = '잘못된 분입니다';
$lang->js_set_wrong_appoint_msg= '예약발송일은 현재보다 미래이어야합니다';
$lang->cmd_sms_del_msg        = '삭제하시겠습니까?';
$lang->js_sms_add_pcs         = '먼저회원가입시 수신받을 휴대폰번호 설정을 추가하세요';
$lang->js_sms_differ_pass     = '비밀번호가 서로 다릅니다';
$lang->js_sms_choice_object   = '대상을 선택하세요';
$lang->js_sms_fail_del        = '삭제에 실패하였습니다';
$lang->js_sms_do_send         = '송신가능';
$lang->js_sms_donot_send      = '송신불가';
$lang->js_sms_set             = '선택된 회원의 SMS설정을 ';
$lang->js_sms_searchId        = '선택된 회원의 추적아이디설정을 ';

$lang->js_sms_group_set       = '선택된 그룹의 SMS설정을 ';
$lang->js_sms_group_searchId  = '선택된 그룹의 추적아이디설정을 ';

$lang->js_sms_all_set         = '모든 회원의 SMS 설정을 ';
$lang->js_sms_all_searchId    = '모든 회원의 추적아이디 설정을 ';

$lang->js_sms_msg             = ' 상태로 변경하시겠습니까?';
$lang->js_sms_domain_require  = '도메인을 입력하세요';
$lang->js_sms_domain_wrong    = '잘못된 도메인입니다';
$lang->js_set_fail_msg        = '설정에 실패하였습니다';
$lang->js_succ_msg            = '저장에 성공 하였습니다';
$lang->js_fail_msg            = '저장에 실패 하였습니다';
$lang->js_success_msg         = '저장에 성공 하였습니다';
$lang->js_sms_require_direct  = '메세지 내용을 입력하세요';
$lang->js_searchId_input_msg  = '추적아이디 대상자를 한명이상 선택하세요';

$lang->cmd_sms_direct_msg     = '문자메시지 1건은 80Byte 이며 80Byte를 초과하면 추가로 메시지가 발송됩니다';

$lang->js_f206_msg = '이미 저장된 그룹입니다';
$lang->js_f207_msg = '이미 저장된 회원입니다';

$lang->js_f213_msg = '저장된 그룹에 포함된 회원입니다';

$lang->js_f227_msg = '기존의 회원정보중 새로 추가되는 그룹속에 포함되는 회원은 기존주소록에서 삭제합니다';
$lang->js_f237_msg = '회원가입을 축하합니다';
$lang->js_f273_msg = '중복된 아이디입니다';
$lang->js_userId_check_msg = '잘못된 아이디입니다';
$lang->js_f274_msg = '중복된 이메일주소입니다';
$lang->js_f247_msg = '저장되었습니다';
$lang->js_f246_msg = '저장에 실패하였습니다';

$lang->js_c5_msg = '받는 사람 번호가 없습니다.';
$lang->js_c7_msg = '발송되었습니다';
$lang->js_c3_msg = '발송에 실패하였습니다';

$lang->js_wrong_sms_text  = '%과 &은 사용할 수없는 문자입니다';

$lang->js_login_747_msg = '가입된 회원이 없습니다';
$lang->js_login_757_msg = '아이디 또는 비밀번호가 일치하지 않습니다';
$lang->js_login_767_msg = '탈퇴 및 기타 사유로 인하여 사용이 불가능한 아이디입니다';
$lang->js_login_777_msg = 'smsXE 서버로 부터 회원님의 정보를 로드하였습니다';
