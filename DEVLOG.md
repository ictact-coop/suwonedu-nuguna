# 개발 일지

## 2018/02/09 - 유지보수에 대한 단상.

다른 사람이 구축한 웹사이트, 코드를 관리하는 건 내키지 않고 어려운 일이다.
또 전임 유지보수 업체와 만나서 진행하는 인수인계 따위는 1도 없는 경우가 대부분이다.
형식적으로 영혼없이 말하는 이야기를 듣기보다는 웹사이트 개발 및 운영에 대한 구조화된 문서가 필요하다.
개발하며 차곡차곡 기록해놨더라면 그 내용이 실제 유지보수를 이어받게 되는 사람에게 훨씬 큰 도움이 될 것이다.

이 프로젝트도 절실하게 그런걸 느끼게 된다. 오죽하면 이렇게 개발 일지에 적고 앉아 있겠나? ㅎ
우리 사협은 힘들더라도 이렇게 기록히려고 노력이나 해봤으면 좋겠다.


## 2018/02/10 - 깔끔한 코드를 만들어보자!

기존 소스에 XE 최신 버전을 덮어써볼까 했지만 코어가 제공하는 기본 모듈, 위젯, 애드온, 레이아웃, 스킨 등과 구분이 되지 않아서
일단 최신 버전의 XE 소스에 기존 프로젝트에서 쓰던 모듈, 위젯 등을 역으로 추가해가며 커밋을 하고
안전하다거나 믿을 수 있는 코드베이스를 만들어보자!

XE 릴리즈 주기를 대강 보면 한달에 버전 한개씩을 등록이 되는 듯 보여서 이걸 좀 편하게 하려면 어떻게 해야할까 고민이 좀 됐다.
xe 깃헙 레포지토리를 upstream으로 잡아서 버전을 찍어 머지하는 방식으로 할까도 생각했으나 너무 지저분해지는 듯 하여 포기...
컴포저 프로젝트로 구성하고 XE 코어와 각종 커뮤니티 제작 모듈, 위젯 등을 컴포저로 관리할 수 있다면 최고...였으나,
이런 레거시 프로젝트는 누구도 컴포저로 관리할 엄두를 못 내는 듯.... 결국 기존 방식대로 한땀한땀 코드 전체를 관리하는 방식으로 가자!

```
/addons
/layouts
/modules
/widgets
```

위의 4개 디랙토리에 추가한 사용하고 있는지도 정확히 파악이 안되는 기존 패키지들을 일일이 확인하며 하나씩 옮기며 순차적으로 커밋한다.
귀찮고 위험하며 지겹고.. 하여튼 권장할만한 방법은 아니다. 일단 시작은 이렇게... 누군가 더 좋은 방식을 찾아내면 개선해보자.

주의할 점은 회원 스킨과 보드 스킨도 추가하고 설정해야한다는...
코드 커밋하기 전에 실제 관리자 화면에서 설정 값을 확인하고 해당하는 코드를 옮겨준다.
혹은 일단 가능한 범위에서 대강 확인하고 추가하고 오류가 발생하거나 하면 그때그때 대응한다.


## 2018/02/12 - 개발 스테이징용 서버를 만들자.

아 뭐 이렇게 까지 하냐 그러다가 제대로 해보자는 생각에 스테이징 서버를 만들기로...
수정하고 배포해서 학습관쪽에서 검토하고 이상이 없으면 실제 서버에 반영하는 방식으로 작업하기로 결정했다.
담당자들 깃헙 가입도 시키고 이슈도 깃헙을 통해 관리하는 방식으로 일해보려고 한다. 장문의 메일도 작성을....
젊은이들이 이런 걸 경험해봐야할텐데 다들 상황이 별로 관심이 없을 수 밖에 없는 듯 하여 좀 안타깝다만...
일단 계약한거니 진행하자...ㅎ


## 2018/02/21 - 메모

원고 수정 #10

```html

## http://nuguna-dev.ictact.kr/index.php?mid=lec_infor

<th class="xe_selected_cell">
	<p style="line-height: 1.8;">
		<strong><span style="color: rgb(255, 255, 255); font-size: 16px; background-color: rgb(255, 102, 0);">학습관에서 열리는</span></strong>
		<br />
		<span style="color: rgb(255, 255, 255); font-size: 16px; background-color: rgb(255, 102, 0);">누구나학교 만들기</span>
	</p>
</th>
<th class="xe_selected_cell">
	<p style="line-height: 1.8;">
		<strong><span style="color: rgb(255, 255, 255); font-size: 16px; background-color: rgb(255, 102, 0);">어디서나 열리는</span></strong>
		<br />
		<span style="color: rgb(255, 255, 255); font-size: 16px; background-color: rgb(255, 102, 0);">누구나학교 만들기</span>
	</p>
</th>



```

## 2018/02/28 - 이슈 수정

#1. 가입 인증 메일에 문구 추가 - ```이 메일은 발신전용 메일로, 수신이 불가합니다.```

#2. 가입 폼 이메일 직접입력을 선택했을때만 드롭박스가 박스로 남아 있도록

#3. 관리자-회원목록-개별회원 정보

- 회원정보에서 "쪽지수신허용"여부 감추기
- 문자수신 동의여부 출력하기
	* 가입 폼에 콤보박스로 기본값 Y로 되어 있어서 딱히 강제할 필요없음.
	* db - xe_member - extra_vars 필드에 가입 폼에서 넘어온 변수 중 코어 필드를 제외한 나마지 변수를 객체로 만들어 직렬화해서 저장..
	* 값은 폼에 하드코딩 되어 있는 Y/N으로 넘어오는데 관리자 설정에서 예/아니오로 입력해놔서 실제 수정 폼이나 관리자 폼에서 값이 일치하지 않아 출력이 안됨
	* 일단 설정은 Y/N으로 수정해서 출력되고 있는 상태

#4. 회원 정보 - 쪽지함을 표시하고 레이블을 댓글 알림으로 교체

#5. 상단 내정보 보기 URL 고정하기

#12. 푸터
- COPYRIGHT ⓒ By 2011 Suwon Lifelong Learning Center. All Right Reserved. 삭제
- CC 아이콘 이미지 교체

#14, #15 학교소식 4개 게시판 정상화

```
누구나학습마을 > 게시판 - nuguna_town_bbs - sketchbook5_nu2016_town
학교소식 >
- 공지사항 - publish - sketchbook5_nuguna2016
- 후기 - postscript - sketchbook5_nuguna2016
- 학교 일상 -  story_human - sketchbook5_nuguna2016
- 언론 보도 - media - sketchbook5_nuguna2016

에디터 컴포넌트
- multimedia_link
- photo_editor
- quotation
- soo_youtube
- textbox_0.1
```

## 2018-03-01 확장검색에 확장변수 추가하기

참고
<https://www.xpressengine.com/tip/17366138>

```
### 추가하려는 확장 변수

- 강좌제목: title (이건 확장 변수가 아님...)
- 강좌번호: 14
- 강사명: 13

/modules/integration_search/integration_search.view.php
+105 // 검색 대상 검사 시 확장 변수 추가
+142 // 출력 대상에 확장 변수 추가

/modules/integration_search/lang/lang.xml
+178 tag 뒤에 확장 변수 레이블 추가

```

### 2018-03-31 레이아웃에 레이어 배너, 하루 동안 안보기 추가

```
레이어 스타일 조정
<div class="absolute-banner">
  <div class="container">
    <img class="zbxe_widget_output" widget="bannermgm_widget" skin="banner" colorset="default" link_target="_blank" />
  </div>
</div>
<style>
.absolute-banner {
  overflow: hidden;
  position: absolute;
  width: 100%;
  z-index: 1000;
  top: 200px;
}
.absolute-banner .container {
  position: relative;
  width: 100%;
}
.absolute-banner .container .xe-widget-wrapper {
  max-width: 640px;
  margin: 0 auto;
}
</style>
```

참고:
http://wishml.tistory.com/45


### 2018-04-01 회원 약관 재동의

https://www.xpressengine.com/qna/23155643
기존회원이 변경된 약관 동의해야 사용할 수 있게 하려면

마침 관련 기능을 문의하는 게시글을 찾았지만 아무도 답변이 없다... 새로 써야한다는....
우리 조합 홈페이지에는 일단 약관이나 개인정보처리방침도 없다. 아... 마추어...

xe-쿼리-직접-실행방법
http://www.coolnix.net/2017/05/xe-%EC%BF%BC%EB%A6%AC-%EC%A7%81%EC%A0%91-%EC%8B%A4%ED%96%89%EB%B0%A9%EB%B2%95/

일단 제대로 접근한다고 하면 일단 변경된 약관에 대한 회원의 동의 여부와 시간을 기록해야한다.
그렇다면 약관부터가 걍 한 필드에 쓰고 지우고 업데이트하는 텍스트가 아니라 리비전으로 관리가 되어야 한다.
그 새로운 약관이 실효를 갖게 되면 그 약관에 동의하지 않는 경우 사용의 제약을 주던가 계정 정지 혹은 삭제 등이 되어야할 듯 하다.
약관을 새로 등록하면 바로 회원에게 동의 여부를 물어야 할까? 이상하다...
보통 언제부터 효력이 발생하니 검토하고 그때 전까지 동의하거나 안하면 어떻게 된다등 충분한 설명이 필요할 듯..
그리고 다들 소홀한 듯 한데 변경 세부사항 diff 없이 그냥 새로운 약관만 공개하고 알아서 어디가 바뀌었는지 찾아보라는 것도 참... 실효성없는 듯...
이 모든 것을 XE에서 구현하는 건 아닌거 같다... 시간이 아까우심...

일단 모듈을 새로 작성해서 간단하게 원하는 것만 달성하자.
그런데 모듈 쓰기 위해서 xe 공부하는 시간도 아까워진다...

요구사항 정리

- 로그인 완료 시 새로운 약관에 동의하지 않은 경우 약관 동의 화면으로 리다이렉트
- 약관 동의하면 관리자 화면에 동의 여부 표시 (코어 관리자 회원 관리 수정 필요)

xe 회원 확장변수는 가관이다. 그냥 회원 가입 폼에서 넘기는 키와 값을 전부 php 객체에 때려넣고 직렬화해서 디비에 저장. ㅋ
사실 문서 확장변수처럼 할 수도 있었겠지만 복잡도가... ㅋ 역시 이런 경우 드루팔이다.
모듈을 작성하는게 아니라 야매로 하려면 이 확장변수를 활용해야 할듯..
로그인한 사용자인지 확인하고 동의 시 이 확장변수에 동의 여부와 시간을 기록하면..ㅋ
관리자 회원 목록 화면 템플릿에 동의 여부를 강제로 추가하면 일단 상황정리될 듯...


```
# 추가 전
O:8:"stdClass":3:{s:6:"mobile";a:3:{i:0;s:3:"000";i:1;s:4:"0000";i:2;s:4:"0000";}s:3:"mms";s:1:"Y";s:21:"__profile_image_exist";s:5:"false";}

# 추가 후
O:8:"stdClass":5:{s:6:"mobile";a:3:{i:0;s:3:"000";i:1;s:4:"0000";i:2;s:4:"0000";}s:3:"mms";s:1:"Y";s:21:"__profile_image_exist";s:5:"false";s:18:"privacy2018agreeYN";s:1:"Y";s:22:"privacy2018agreeYNTime";s:19:"2018-04-02 05:07:19";}
```


## 2019-11-01

```
#24 (~19.11.7) 개인정보 처리방침 재동의 요청페이지

- [x] 개인정보 취급방침 => 개인정보 처리방침 (번역 파일 수정)
- [x] 회원가입 시 개인정보 처리방침 2차안으로 수정
- [x] 배너 띄우기

애드온 > 이용약관/개인정보 처리방침
/index.php?module=admin&act=dispAddonAdminSetup&selected_addon=member_join_ex

배너 띄우기
모듈 > 배너관리자
/index.php?module=admin&act=dispBannermgmAdminContent

```

## 2019-11-12

### [22. (~19.11.7) 개인정보 처리방침 재동의 요청페이지 #24](https://github.com/ictact-coop/suwonedu-nuguna/issues/24)

로그인 시 일단 무조건 /agree_201804 (XE 관리자 회원 설정 - 로그인에서 지정) 으로 리다리엑트된다.
해당 외부 페이지에서 개정 약관 동의 여부 및 회원 가입일을 확인해서 메인으로 보내거나 
약관 동의하면 확장변수에 ```agree20181YN: "Y", agree20181YNTime: "2018-05-02 02:18:35"``` 형식으로 저장한다.

#### 외부 페이지 생성 및 설정 (재동의 요청)

[사이트 메뉴 편집](http://localhost:8888/index.php?module=admin&act=dispMenuAdminSiteMap)에서 unlinked 아래
메뉴 추가 > 외부 페이지에서 아래 정보로 생성한다. unlinked는 메뉴로 출력하지 않는 페이지들을 묶어놓은 메뉴이다.

- 메뉴 이름 => 2018 개인정보처리방침 변경안 동의
- 메뉴 ID => agree_201804

으로 새로운 페이지를 만든 후 [즐겨찾기 > 페이지 관리 > 페이지 설정](http://localhost:8888/index.php?module=admin&act=dispPageAdminInfo&module_srl=134805) 화면에서 외부 페이지 템플릿으로 사용할 파일 위치를 입력하고 저장한다.

```
# 관련 파일
- docroot/layouts/portalon/2018-privacy-policy-agreement.html <= 외부 페이지 템플릿 (외부 페이지에서는 php 사용 가능)
- docroot/layouts/portalon/footer.html
- docroot/layouts/portalon/block-members-not-agreed.php

// 2018년 4월 30일 이전 가입자 수: 1367
// 그 중 동의하지 않은 회원: 1217 (extra_vars not contains agree20181YN => 1217)

```


## 2019-12-01

### [25. (~19.11.1 /~19.12.1) 팝업창 운영 후 수정/ 개인정보처리방침 3차 #27](https://github.com/ictact-coop/suwonedu-nuguna/issues/27)

애드온 > 이용약관/개인정보 처리방침 > 수집하는 개인정보의 항목
/index.php?module=admin&act=dispAddonAdminSetup&selected_addon=member_join_ex
에 ```docroot/layouts/portalon/privacy-policy-201912.html``` 3차 개인정보처리방침 내용을 붙혀넣고 저장


### [27. (~19.12.1) 수정 / 회원가입 시 필수항목 추가 #29](https://github.com/ictact-coop/suwonedu-nuguna/issues/29)

```
이름
닉네임
성별 : 남, 여 선택 (*형식: 문자수신허용처럼)
생년월일  (*형식:  연도/월/일자를 선택할 수 있는 박스)

연락처
주소(동까지)  (*형식: 이름처럼 네모박스)
※옆에 예시로 회색글씨 “예: 수원시 팔달구 우만1동” 넣어주세요
```

회원 > 회원설정 > 회원가입 > 가입 폼 관리
/index.php?module=admin&act=dispMemberAdminSignUpConfig

- [x] 성별 => gender // radio 남|여
- [x] 생년월일 => birthday //생일 필드 재활용 (달력선택기)
- [x] 주소(동까지) => address // text
- [x] 개인정보취급방침 동의 여부 => agree201912yn
- [x] 개인정보취급방침 동의 시간 => agree201912yntime

### [23. (~19.12.1) 페이지 추가/ 로그인 시, 개인정보 확인 및 수정, 추가기재 강제 화면 생성 #25](https://github.com/ictact-coop/suwonedu-nuguna/issues/25)

```
모든 회원이 로그인 시 수정가능한 상태로 기존 개인정보(저 위의 항목들만)를 보여주고
성별/생년월일/주소를 추가로 필수 입력하고 확인키를 눌러야 이용 가능하도록.

상단 안내문구
원활한 운영을 위해, 내 정보를 확인해주세요!
3차 개인정보 처리방침 적용에 따라, 성별/생년월일/주소도 기재해주세요.

1번 개인정보 취급방침 미동의자에게 조건생성되는 페이지와 동시에 운영 가능할까요?
가능하다면 1번 후 1-1번가 뜰 수 있으면 좋을 것 같습니다.
```

- [x] 외부 페이지 교체 agree201804 => reconfirm_privacy_terms
- [x] 회원 신규 가입 시 개인정보취급방침 동의 여부와 시간을 저장
- [x] 기존 회원 정보 수정 폼에 type=add_additional_info 파라미터 넘기기
- [x] 회원 수정 시 비번 물어보지 않기
- [x] 동의하지 않은 경우 reconfirm_privacy_terms
- [x] 동의했지만 성별/생년월일/주소 없는 경우 type=add_additional_info

```
# docroot/modules/member/member.controller.php
$extra_vars->agree201912yn = 'Y';
$extra_vars->agree201912yntime = date('YmdHis');
```

### [XE 업그레이드 #32](https://github.com/ictact-coop/suwonedu-nuguna/issues/32)

```bash
# 코어 업그레이드 변경분 다운로드 및 압축 풀기
# https://github.com/xpressengine/xe-core/releases
$ mkdir -p dump/xe-upgrade
$ cd $_ && mkdir 1.11.0 1.11.1 1.11.2 1.11.3 1.11.4 1.11.5 1.11.6
$ curl -LO https://github.com/xpressengine/xe-core/releases/download/1.11.0/xe.1.11.0.changed.tar.gz && tar zxvf xe.1.11.0.changed.tar.gz -C 1.11.0/
$ curl -LO https://github.com/xpressengine/xe-core/releases/download/1.11.1/xe.1.11.1.changed.tar.gz && tar zxvf xe.1.11.1.changed.tar.gz
$ curl -LO https://github.com/xpressengine/xe-core/releases/download/1.11.2/xe.1.11.2.changed.tar.gz && tar zxvf xe.1.11.2.changed.tar.gz
$ curl -LO https://github.com/xpressengine/xe-core/releases/download/1.11.3/xe.1.11.3.changed.tar.gz && tar zxvf xe.1.11.3.changed.tar.gz
$ curl -LO https://github.com/xpressengine/xe-core/releases/download/1.11.4/xe.1.11.4.changed.tar.gz && tar zxvf xe.1.11.4.changed.tar.gz
$ curl -LO https://github.com/xpressengine/xe-core/releases/download/1.11.5/xe.1.11.5.changed.tar.gz && tar zxvf xe.1.11.5.changed.tar.gz
$ curl -LO https://github.com/xpressengine/xe-core/releases/download/1.11.6/xe.1.11.6.changed.tar.gz && tar zxvf xe.1.11.6.changed.tar.gz

# 변경분 실제 소스에 반영
$ rsync -azvh 1.11.0/ ../../docroot/

# 코어 수정 내역

# docroot/modules/document/document.model.php
# docroot/modules/integration_search/integration_search.view.php
# docroot/modules/integration_search/lang/lang.xml
# docroot/modules/integration_search/skins/default/document.html
# docroot/modules/member/member.controller.php
# docroot/modules/member/tpl/insert_member.html
# docroot/modules/member/tpl/member_list.html

# 번번히 패치를 만들기가 번거로워 그냥 눈으로 확인하고 변경 사항을 제외하고 커밋하기로 한다.
```