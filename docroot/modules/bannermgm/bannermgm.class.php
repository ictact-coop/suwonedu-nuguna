<?php
    /**
     * @file    bannermgm.class.php
     * @class   bannermgm
     * @author  ChoiHeeChul, KimJinHwan, ParkSunYoung
     * @brief   bannermgm module의 최상위 클래스
     *
     * @mainpage Bannermgm Module
     * @section intro 소개
     * bannermgm 모듈은 각 페이지에 삽입하고자 하는 배너들을 통합하여 관리하는 모듈입니다.\n
     * 기능
     * - 여러 배너들을 한곳에 통합하여 관리 할수 있음
     * - 직접 html소스를 수정하지 않아도 어드민 페이지에서 쉽게 수정 가능
     **/
    class bannermgm extends ModuleObject {
        /**
         * @brief 설치시 추가 작업이 필요할시 구현
         **/
        function moduleInstall() {
            return new Object();
        }

        /**
         * @brief 설치가 이상이 없는지 체크하는 method
         **/
        function checkUpdate() {
            return false;
        }

        /**
         * @brief 업데이트 실행
         **/
        function moduleUpdate() {
            return new Object();
        }

        /**
         * @brief 캐시 파일 재생성 ( 불필요한 이미지들도 삭제 )
         **/
        function recompileCache() {
            $output = executeQueryArray('bannermgm.getBannermgmImagePath');
            if(!$output->toBool()) return $output;
            
            $dir = "./files/attach/banner_images/";
            $arrImgList = array();
            for( $i=0; $i<count($output->data); $i++ ) {
                //debugPrint($output->data[$i]->image_path);
                array_push( $arrImgList, $output->data[$i]->image_path );
            }

            if(is_dir($dir)){
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if( $file == "." || $file == ".." ) continue;
                        $fh = opendir($dir.$file);
                        while(($imgFile = readdir($fh)) !== false) {
                            if( $imgFile == "." || $imgFile == ".." ) continue;
                            else break;
                        }
                        closedir($fh);
                        //debugPrint($imgFile);
                    
                        //$rmdir = explode('/',$file);
                        $cmpFile = $dir.$file."/".$imgFile;
                      //  debugPrint($cmpFile);
                       if(!in_array($cmpFile,$arrImgList)){
                            $this->deleteAll($dir.$file);            
                       }
                    }
                    closedir($dh);
                }
            }
        
        }
        /**
         * @brief 디렉토리 및 하위 파일 삭
         **/
        function deleteAll($directory, $empty = false) { 
            if(substr($directory,-1) == "/") { 
                $directory = substr($directory,0,-1); 
            } 
        
            if(!file_exists($directory) || !is_dir($directory)) { 
                return false; 
            } elseif(!is_readable($directory)) { 
                return false; 
            } else { 
                $directoryHandle = opendir($directory); 
                
                while ($contents = readdir($directoryHandle)) { 
                    if($contents != '.' && $contents != '..') { 
                        $path = $directory . "/" . $contents; 
                        
                        if(is_dir($path)) { 
                            deleteAll($path); 
                        } else { 
                            unlink($path); 
                        } 
                    } 
                } 
                
                closedir($directoryHandle); 
        
                if($empty == false) { 
                    if(!rmdir($directory)) { 
                        return false; 
                    } 
                } 
                
                return true; 
            } 
        } 
    }
?>
