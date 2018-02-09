
# [수원 누구나 학교](http://nuguna.suwonedu.org/)

누구나 가르치고 누구나 배우는 학습 공동체

수원시 평생 학습관에서 운영하는 누구나학교는 스스로 그리고 더불어 배우는 시민주도 평생학습 플랫폼입니다.
지식, 재능, 경험, 삶의 지혜를 나누고 싶은 누구나 학교를 열고 배움의 기회를 갖고 싶은 누구나 참여할 수 있습니다.

## 로컬 개발

```bash

# 데이터베이스 복원
$ mysql -u root -p -e 'create database `nuguna_local`;'
$ gunzip < dump/nuguna-2018-02-06-13.53.18.sql.gz | mysql -u root -p nuguna_local

# xe 데이터베이스 설정 파일 위치에 놓기
$ mkdir -p docroot/files/config/
$ cp dump/db.config.php.local docroot/files/config/db.config.php

# php 내장 웹서버 실행
$ cd docroot
$ php -S localhost:8888

```