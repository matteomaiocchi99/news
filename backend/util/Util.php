<?php

namespace backend\util;

use common\models\Languages;
use SoapClient;
use Yii;
use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class Util {
    
    /**
     * URL constants as defined in the PHP Manual under "Constants usable with
     * http_build_url()".
     *
     * @see http://us2.php.net/manual/en/http.constants.php#http.constants.url
     */
    /*COSTANTI PER COSTRUZIONE URL E AGGIUNTA DEI PARAMETRI*/
    const HTTP_URL_REPLACE = 1;
    const HTTP_URL_JOIN_PATH = 2;
    const HTTP_URL_JOIN_QUERY = 4;
    const HTTP_URL_STRIP_USER = 8;
    const HTTP_URL_STRIP_PASS = 16;
    const HTTP_URL_STRIP_AUTH = 32;
    const HTTP_URL_STRIP_PORT = 64;
    const HTTP_URL_STRIP_PATH = 128;
    const HTTP_URL_STRIP_QUERY = 256;
    const HTTP_URL_STRIP_FRAGMENT = 512;
    const HTTP_URL_STRIP_ALL = 1024;
    
    public static function sendEmail($obj,$content,$email,$attachment = null){
        
        //$attachment = array
        //file => il file
        //name => il nome da dare al file
        //type => il formato del file
        
        $mail = Yii::$app->mailer->compose()
                        ->setFrom([Yii::$app->params["EMAIL_FROM"] => "Dieffetech"])
                        ->setTo($email)
                        ->setSubject($obj)
                        ->setHtmlBody($content);
        
        if(!empty($attachment)){
            $mail->attachContent($attachment["file"], ["fileName" => $attachment["name"], "contentType" => $attachment["type"], "Content-Transfer-Encoding" => "7bit"]);
        }
        return $mail->send();
    }

    public static function sendEmailTemplate($obj, $template, $emails, $params=[], $reply_to = null, $cc = "", $ccn = "", $attachment = null)
    {
        $success = true;

        if (!empty(Yii::$app->params["EMAIL_BLOCK"])) {
            $emails = Yii::$app->params["EMAIL_BLOCK"];
            $reply_to = Yii::$app->params["EMAIL_BLOCK"];
            $obj = "AMBIENTE TEST - " . $obj;
        }

        $mail = Yii::$app->mailer->compose($template, $params)
            ->setFrom([Yii::$app->params["EMAIL_FROM"] => Yii::$app->params["EMAIL_NAME"]])
            ->setTo($emails)
            ->setSubject($obj);

        if(!empty($cc)){
            $mail->setCc($cc);
        }

        if(!empty($ccn)){
            $mail->setBcc($ccn);
        }

        if(!empty($attachment)){
            $mail->attach($attachment);
        }

        if(!empty($reply_to)){
            $mail->setReplyTo($reply_to);
        }

        try {

            $success = $mail->send();

            if (!$success){

                throw new Exception('Impossibile inviare la mail');
            }

        } catch (Exception $error){

        } catch (\Swift_TransportException $error){

        }

        return $success;
    }
    
    public static function getPreferredLanguage(array $languages = [])
    {
        if (empty($languages)) {
            return Yii::$app->language;
        }
        
        foreach ($languages as $acceptableLanguage) {
            $acceptableLanguage = str_replace('_', '-', strtolower($acceptableLanguage));
            foreach (Yii::$app->request->getAcceptableLanguages() as $language) {
                $normalizedLanguage = str_replace('_', '-', strtolower($language));
                if (
                    $normalizedLanguage === $acceptableLanguage // en-us==en-us
                    || strpos($acceptableLanguage, $normalizedLanguage . '-') === 0 // en==en-us
                    || strpos($normalizedLanguage, $acceptableLanguage . '-') === 0 // en-us==en
                ) {
                    return substr($language, 0,2);
                }
            }
        }
        return Yii::$app->language;
    }
    
    public static function createHash($value){
        return md5($value.Yii::$app->params["ENCRYPTED_KEY"]);
    }
    
    public static function checkHash($value,$hash){
        $newHash = md5($value.Yii::$app->params["ENCRYPTED_KEY"]);
        
        if($newHash == $hash){
            return true;
        }
        
        return false;
    }
    
    public static function getValute(){
        $valute = Yii::$app->params["VALUTE"];
        
        foreach ($valute as $key => $value){
            $valute[$key] = Yii::t("app",$value);
        }
        
        return $valute;
    }
    
    public static function translateArray($array){
        foreach ($array as $key => $value){
            $array[$key] = Yii::t("app",$value);
        }
        
        return $array;
    } 
    
    public static function translateItemParams($params,$value){
        $array = Yii::$app->params[$params];
        
        $array = Util::translateArray($array);
        
        return $array[$value];
    }
    
    public static function convertArraySelectToJson($array){
        $results = []; 
        foreach ($array as $author => $docArray) {
            $docs  = [];
            foreach ($docArray as $id => $title) {
                $docs[] = ['id' => $id, 'text' => $title];
            }
            $results[] = ['text' => $author, 'children' => $docs];
        }
        
        return json_encode($results);
    }
    
    public static function getSingleValueFromArraySelectWithOpt($array){
        if(count($array) == 1){
            foreach ($array as $key => $value){
                if(count($value) == 1){
                    foreach ($value as $k => $v){
                        return $k;
                    }
                }
            }
        }
        
        return null;
    }
    
    public static function convertDateToSql($data){
        
        if(empty($data)){
            return "";
        }
        
        $date = str_replace("/", "-", $data);
        return date("Y-m-d",strtotime($date));
    }
    
    public static function convertDate($date){
        
        if(empty($date)){
            return "";
        }
        
        return date("d/m/Y",strtotime($date));
    }
    
    public static function convertHour($hour){
        if(empty($hour)){
            return "";
        }
        
        return date("H:i",strtotime($hour));
    }
    
    public static function convertDateTime($date){
        if(empty($date)){
            return "";
        }
        
        return date("d/m/Y H:i",strtotime($date));
    }
    
    public static function convertDateTimeToSql($data){
        
        if(empty($data)){
            return "";
        }
        
        $date = str_replace("/", "-", $data);
        return date("Y-m-d H:i:s",strtotime($date));
    }
    
    public static function getAlert($message,$success){
        if($message != null){
            
            $class = "alert-danger";
            
            if($success){
                $class = "alert-success";
            }
            
            $html = '<div class="myadmin-alert myadmin-alert-icon myadmin-alert-click '.$class.' myadmin-alert-top alerttop" style="display:block"> 
                        <i class="ti-check"></i> 
                        '.$message.'
                        <a href="#" data-pjax="0" class="closed">&times;</a> 
                    </div>
                    <script>
                        $(document).ready(function(){
                            $(".alertbottom").fadeToggle(350);

                            $(".closed").on("click",function(){
                                $(this).parent().remove();
                            })
                        });
                    </script>';
            
            return $html;
        }
        
        return "";
    }
    
    public static function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    public static function bindValue($text,$params){
        foreach ($params as $key => $param){
            $text = str_replace($key, $param, $text);
        }
        
        return $text;
    }
    
    public static function formatTextFromTextArea($text){
        return str_replace("\n", "<br>", $text);
    }
    
    public static function preparePeriodFromDates($from_date,$to_date,$lenght = null,$uppercase = null ){
        
        $from_date = self::convertDateToSql($from_date);
        $to_date = self::convertDateToSql($to_date);
        
        $string = date("d",strtotime($from_date));
        
        if(!empty($to_date) && $to_date != $from_date){
            $string .= " - ";
            $string .= date("d",strtotime($to_date));
            $string .= "/";
            $string .= self::getMonthName(intval(date("m",strtotime($to_date))),$lenght,$uppercase);
        }else{
            $string .= "/";
            $string .= self::getMonthName(intval(date("m",strtotime($from_date))),$lenght,$uppercase);
        }
        
        return $string;
    }
    
    public static function getMonthName($n,$lenght = null, $uppercase = false,$language='it'){
        $n= (int)$n;
        $array = [
            'it'=>[
                1 => "Gennaio",
                2 => "Febbraio",
                3 => "Marzo",
                4 => "Aprile",
                5 => "Maggio",
                6 => "Giugno",        
                7 => "Luglio",
                8 => "Agosto",
                9 => "Settembre",
                10 => "Ottobre",
                11 => "Novembre",
                12 => "Dicembre"
            ],
            'en'=>[
                1 => "January",
                2 => "February",
                3 => "March",
                4 => "April",
                5 => "May",
                6 => "June",        
                7 => "July",
                8 => "August",
                9 => "September",
                10 => "October",
                11 => "November",
                12 => "December"
            ]
        ];
        
        $name = $array[$language][$n];
        
        if(!empty($lenght)){
            $name = substr($name, 0, $lenght);
        }
        
        if($uppercase){
            $name = strtoupper($name);
        }
        
        return $name;
    }
    
    public static function convertModelsToArray($models){
        $array = [];
        
        foreach ($models as $model){
            $tmpArray = [];
            foreach ($model as $key => $value){
                echo $key."<br>";
                $tmpArray[$key] = $value; 
            }
            exit();
            $array[] = $tmpArray;
        }
        
        return $array;
    }
    
    public static function prepareJsonForSelect($array,$id,$text){
        $return = [];
        $return["results"] = [];
        
        foreach ($array as $value){
            $return["results"][] = ["id" => $value[$id],"text" => $value[$text]];
        }
        
        return $return;
    }
    
    public static function filterArray($array,$fields){
        $return = [];
        foreach ($array as $value){
            $tmp = [];
            foreach ($fields as $field){
                $tmp[$field] = $value[$field];
            }
            $return[] = $tmp;
        }
        
        return $return;
    }

	public static function 	prepareJsonForFileInput($array,$pathField,$nameField,$idField,$urlDelete,$single = false,$vimeo = false,$internal = false,$attachmentModel = null){

		if($single){
			if(empty((string)$array[$pathField])){
				return ["names" => [],"config" => []];
			}
			$tmp = $array;
			$array = [];
			$array[] = $tmp;
		}

		$nomi = [];
		$return_json = [];
		foreach ($array as $value){
			if(empty($nameField)){
				$arrayPath = explode("/", $value[$pathField]);
				$name = end($arrayPath);
			}else{


				$name = $value[$nameField];

			}

			$fileExtension = $value[$pathField];

			$arrayExtension = explode(".",$fileExtension);

			$extension = end($arrayExtension);

			$type = 'video';


			if(!$vimeo){
				if(in_array($extension, Yii::$app->params["IMAGE_EXTENSION"])){
					$type = "image";
				}else if(in_array($extension, Yii::$app->params["VIDEO_EXTENSION"])){
					$type = "video";
				}else if($extension=='zip'){
					$type = "zip";
				} else if(in_array($extension,Yii::$app->params["DOCUMENT_EXTENSION"])){

					$type = "pdf";
				} else {
					$type='other';
				}
			} else {


				$type = 'pdf';

			}


			if(is_array($idField)){
				$extra = [];
				foreach ($idField as $primaryKey){
					$extra[$primaryKey] = $value[$primaryKey];
				}
				$key= $value[$idField[0]];
			}else{
				$extra = ['id' => $value[$idField]];
				$key= $value[$idField];
			}

			$return_json[] = [
				'caption' => $name,
				'width' => '100px',
				'url' => !empty($urlDelete) ? Url::to([$urlDelete]) : '',
				'key' => $key,
				'extra' => $extra,
				'type' => $type,
				'downloadUrl' => !$vimeo ? Yii::$app->request->baseUrl.'/'. $value[$pathField] : '//player.vimeo.com/video/'.$value[$pathField].'?autoplay=1&api=1',
				'filetype' => "",
			];

			if($vimeo){

				$nomi = self::bindValue(\Yii::$app->params['VIMEO']['PREVIEW_URL'],['{videoId}' => $value[$pathField]]);

			} else if(!$internal){

				$nomi[] = $value[$pathField];

			} else {

				$nomi[] = Url::to(['/global/render-attachments',
					'id' => $value[$idField],
					'token' => md5($value[$idField].\Yii::$app->params['SECURE_TOKEN']),
					'key_name' => $idField,
					'languageidfk' => $attachmentModel->languageidfk,
					'model' => get_class($attachmentModel),
					'file_name' => $nameField,
					'path_field' => $pathField
				]);
			}


		}


		if(!$single){
			return ["names" => $nomi, "config" => $return_json];
		}

		return ["names" => $nomi,"config" => $return_json];
	}
    
    public static function getDistinctValueFromArray($array,$field,$resultHaveArray = false){
        $oldField = null;
        $result = [];
        
        foreach ($array as $key => $value){
            if($value[$field] != $oldField){
                if($resultHaveArray){
                    $result[$value[$field]] = [];
                }else{
                    $result[] = $value[$field];
                }
                
                $oldField = $value[$field];
            }
        }
        
        return $result;
    }
    
    public static function createMultiArrayFromField($values,$array,$fieldCompare){
        foreach ($array as $key => $group){
            foreach ($values as $value){
                if($key == $value[$fieldCompare]){
                    $array[$key][] = $value;
                }
            }
        }
        
        return $array;
    }
    public static function getStatoHtml($stato,$testoSi,$testoNo){
        if($stato == 1){
            return "<label class='label label-success'>$testoSi</label>";
        }

        return "<label class='label label-danger'>$testoNo</label>";
    }
    public static function getTranslatedLanguages($id, $tableName, $idColumnName, $idFkColumnName, $idLanguageName, $idfk)
    {
        $excludedLanguages = \Yii::$app->db->createCommand("SELECT languageid,languages.language from languages inner join $tableName on $tableName.$idLanguageName=languages.languageid where $tableName.$idColumnName =$id or $tableName.$idFkColumnName=$idfk or $tableName.$idColumnName =$idfk ")->queryAll();
         
        $languages = Languages::getAllIdAndName();

        if (sizeof($excludedLanguages) == sizeof($languages)) //NON CE N'è NESSUNA SELEZIONATA
        {
            return [
                'all'=>true,
                'languages'=>$languages
            ];
        }
        else
        {
            $columnExcluded = array_column($excludedLanguages, "languageid");
            $columnExcluded = implode(',', $columnExcluded);
            if(!empty($columnExcluded)){
                $ex = \Yii::$app->db->createCommand("SELECT languageid,language FROM languages where languageid  IN ($columnExcluded)")->queryAll();
            }

            return $ex;
        }
    }
    
    public static function getTraductions($className,$idMain,$onlyLanguageId=false,$asArray=false,$languageCode=false,$notIn=null,$defaultLanguage=false){
        /* RICORDATI DI DEFINIRE NEL MODEL DELLE TRADUCTION UNA VARIABILE CHE SI CHIAMA languageCode che conterrà il code della lingua 
           la colonna della lingua si deve chiamare languageidfk
         *          */
        $class= new $className;
        
        $key= $class->attributes;
        $key= self::array_first_key($key);
        $traductions= $class->find()
                ->where("$key = :id",[':id'=>$idMain]);
        
        if(!empty($notIn)){
            
            $traductions->andWhere("languageidfk NOT IN (".implode(',',$notIn).")");
        }
        if($asArray){
            $traductions->asArray(true);
        }
        
        $traductions=$traductions->all();
        $default=array();
        
        if($languageCode){
            foreach ($traductions as $k=>$traduction){
                if(is_array($traduction)){
                    $lang= Languages::findOne($traduction['languageidfk']);
                    $traductions[$k]['languageCode']= $lang->code;
                } else if (is_object($traduction)){
                    $lang= Languages::findOne($traduction->languageidfk);
                    $traductions[$k]->languageCode= $lang->code;
                }
                if($defaultLanguage){
                    if($lang->code==='it'){
                        $default= $traductions[$k]; 
                    }
                }

            }  
        }
        
        if($onlyLanguageId){
            return ArrayHelper::getColumn($traductions, "languageidfk");
        }
        
        return [
           'traduction'=>$traductions,
            'default'=>$default
        ];
    }

   
    
    public static function checkAllTranslate($className,$id){
        
        $class= new $className;
        $key= $class->attributes;
        $key= self::array_first_key($key);
        $languages= Languages::getAll();
        $languages= ArrayHelper::toArray($languages);
        $languages= ArrayHelper::map($languages, 'languageid', 'languageid');
        $class= $class->find()
                ->where("$key = :id",[':id'=>$id])
                ->andWhere("languageidfk IN (".implode(',', $languages).")") //Yii::$app->params['ARRAY_LANGUAGES']
                ->all();
        if(count($class) >= $_SESSION["COUNT_LANGS"]){
            return true;
        }

        return false;
    }
    
    
    public static function getAvailableLanguages($languages, $excluded)
    {
        $excluded = ArrayHelper::map($excluded, "languageid", "name");
        $available = array_diff($languages, $excluded);
       
        if (count($available) > 1)
        {
            // se esistono più lingue disponibili prendo la prima
            $tmp[key($available)] = reset($available);
            $available = [];
            $available = $tmp;
        }
        return $available;
    }
   
    public static function getLabelLanguages($arrayLanguages=[]){
        
        $return=array();
        if(!empty($arrayLanguages)){
            $languages= Languages::find()
                ->where('languageid IN('.implode(',', $arrayLanguages).')')
                ->all();
            
            foreach ($languages as $language){
                $return[]=$language['language'];
            }
        }
        
        
        
        return $return;
        
    }
    
    public static function array_first_key(array $array)
    {
        reset($array);

        return key($array);
    }
    
    public static function calculateNextId($className,$scenario='create'){
        
        $class= new $className;
        $keyName= $class->attributes;
        $keyName= self::array_first_key($keyName);
        switch ($scenario):
            case 'create':
                    return $class::find()->max("$keyName")+1;
                break;
        endswitch;
    }
    
    /**
     * Attachment: un oggetto che poi verrà cancellato in db
     * mainFolder: cartella dove andare a cercare la sottocartella del singolo elemento esempio: lessons-pdf
     * lessonFolder: sottocartella che va cancellata
     */
    public static function unlinkFolderNotEmpty($mainFolder,$subFolder){
        
        if(empty($mainFolder) || empty($subFolder)){
            
            throw new InvalidArgumentException;
        }
        
        $webRoot= Yii::getAlias('@webroot');
        
        $folderUnlink=$webRoot.'/'.$mainFolder.'/'.$subFolder;
        if(scandir($folderUnlink)){
            foreach (scandir($folderUnlink) as $file){
                if ($file != '.' && $file != '..'){

                    if(filetype($folderUnlink.'/'.$file) =='dir'){

                        rmdir($folderUnlink.'/'.$file);

                    } else {
                       unlink($folderUnlink.'/'.$file);
                    }

                }
            }
            rmdir($folderUnlink);


            return true;
        } else {
            
            return false;
            
        }
    }
    /**
     * type_return: 0: select2 normale,1: depDrop,2: Select2 ajax
     * $whereCondition: array con 2 chiavi: ['condition'=>condizione, 'params'=>'valori su qui eseguire la where']
     * $joinCondition: array con 2 chiavi: ['type'=> 'inner,left o right' ,'table'=>nome tabella su cui andare in join, 'join_clause'=>'Condizione della join','params'=>'parametri della join'] esempio : ['table'=>'courses','join_clause'=>'courses.id=lessons.courseidfk']
     * model: da passare con get_class()
     * keyName: nome della primary key
     * textName: nome del campo di testo, esempio titolo
     * selected: un array dove passare gli id degli elementi da selezionare nella select
     * disabled: un array dove passare gli id degli elementi da settare a disabled
     * printRawSql: stampa a schermo la query
     */
    public static function getArrayForSelect(
            $model,
            $fieldsToSelect=[],
            $whereCondition=[],
            $joinCondition=[],
            $keyName=null,
            $textName='',
            $type_return= 0,
            $orderBy=null,
            $distinct=false,
            $selected=array(),
            $disabled=array(),
            $printRawSql=false
    ){
        
        
        if(!empty($model)){
            $models= $model::find();
        } else {
            
            return []; 
        } 
        /* @var $models  ActiveRecord */
        if(!empty($fieldsToSelect)){
            $models->select($fieldsToSelect);
        }
        
        if(!empty($whereCondition)){
            foreach ($whereCondition as $where){
                $models->andWhere($where['condition'], isset($where['params']) ? $where['params'] : null);
            }
        }
        if(!empty($joinCondition)){
            foreach ($joinCondition as $join){
                switch ($join['type']) {
                    case 'inner':
                        $models->innerJoin($join['table'],$join['join_clause'], isset($join['params']) ? $join['params'] : null);
                        break;
                    case 'left':
                        $models->leftJoin($join['table'],$join['join_clause'], isset($join['params']) ? $join['params'] : null);
                        break;
                    case 'right':
                        $models->rightJoin($join['table'],$join['join_clause'], isset($join['params']) ? $join['params'] : null);
                        break;
                    default:
                        $models->innerJoin($join['table'],$join['join_clause'], isset($join['params']) ? $join['params'] : null);
                        break;
                }
            }
        }
        if(!empty($orderBy)){
            if(is_array($orderBy)){
                $models->orderBy(implode(',', $orderBy));
            } else {
                $models->orderBy($orderBy);
            }
        }
        if($distinct){
            $models->distinct(true);
        }
        
        if($printRawSql){
            
            echo '<pre>';
            print_r($models->createCommand()->getRawSql());
            exit();
            
        }
        
        
        $models=$models->all();
        
        
       
        $models= ArrayHelper::toArray($models);
        $return=array();
        
        if($type_return == 0){

            foreach ($models as $model){
                
                $return[$model[isset($keyName) ? $keyName : 'id']]= $model[isset($textName) ? $textName : 'name'];
            }
        }else if($type_return == 1){
            foreach ($models as $model){
                $selectedCondition=false;
                if(!empty($selected)){
                    if(in_array($model[isset($keyName) ? $keyName : 'id'], $selected)){
                        $selectedCondition=true;
                    }
                }
                $disableCondition=false;
                if(!empty($disabled)){
                    if(in_array($model[isset($keyName) ? $keyName : 'id'], $disabled)){
                        $disableCondition=true;
                    }
                }
                $return[] = [
                    "id" => $model[isset($keyName) ? $keyName : 'id'],
                    "name" => $model[isset($textName) ? $textName : 'name'],
                    'selected'=>$selectedCondition,
                    'disabled'=>$disableCondition
                ];
            }
        }else{
            $return[] = [
                    "id" => '',
                    "text" => '',
            ];  
            foreach ($models as $model){
                
                $selectedCondition=false;
                if(!empty($selected)){
                    if(in_array($model[isset($keyName) ? $keyName : 'id'], $selected)){
                        $selectedCondition=true;
                    }
                }
                
                $disableCondition=false;
                if(!empty($disabled)){
                    if(in_array($model[isset($keyName) ? $keyName : 'id'], $disabled)){
                        $disableCondition=true;
                    }
                }
                
                $return[] = [
                    "id" => $model[isset($keyName) ? $keyName : 'id'],
                    "text" => isset($model[isset($textName) ? $textName : 'name']) ? $model[isset($textName) ? $textName : 'name'] : $model['name'],
                    'selected'=>$selectedCondition,
                    'disabled'=>$disableCondition
                ];
            }
        }
        
        return $return;
        
    }
    public static function convertFloatToSql($number,$formatNumber=false){
        if(!empty($number)){
            $number=str_replace(',','.', $number);
            if($formatNumber){
                $number= floatval($number);
                $number=  number_format($number,2,'.','');
            }
            return $number;
        }
        return null;
    }
    public static function convertFloat($number){
        if(!empty($number)){
            $number= (float)$number;

            $number= number_format($number,2,',','');
            return $number;
        }
        
        return null;
    }
    
    public static function get_current_url($noQueryString=false)
    {
        $url = '';

        // Check to see if it's over https
        $is_https = self::is_https();
        if ($is_https) {
            $url .= 'https://';
        } else {
            $url .= 'http://';
        }

        // Was a username or password passed?
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $url .= $_SERVER['PHP_AUTH_USER'];

            if (isset($_SERVER['PHP_AUTH_PW'])) {
                $url .= ':' . $_SERVER['PHP_AUTH_PW'];
            }

            $url .= '@';
        }


        // We want the user to stay on the same host they are currently on,
        // but beware of security issues
        // see http://shiflett.org/blog/2006/mar/server-name-versus-http-host
        $url .= $_SERVER['HTTP_HOST'];

        $port = $_SERVER['SERVER_PORT'];

        // Is it on a non standard port?
        if ($is_https && ($port != 443)) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        } elseif (!$is_https && ($port != 80)) {
            $url .= ':' . $_SERVER['SERVER_PORT'];
        }

        // Get the rest of the URL
        if (!isset($_SERVER['REQUEST_URI']) && !$noQueryString) {
            // Microsoft IIS doesn't set REQUEST_URI by default
            $url .= $_SERVER['PHP_SELF'];

            if (isset($_SERVER['QUERY_STRING']) && !$noQueryString) {
                
                $url .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            $url .= $_SERVER['REQUEST_URI'];
            
        }

        return $url;
    }
    public static function is_https()
    {
        return isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
    }
    
    public static function createMultiple($modelClass, $multipleModels = [])
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass;
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }
    
    public static function checkFirstTranslation($class,$languageidfk){
        
        
        
        $model=$class::find()
            ->where('languageidfk != :lid',[':lid'=>$languageidfk])
            ->one();
        
        return empty($model->id);
        
    }
    
    public static function getIdTranslation($class) //serve solo per le pagine singole
    {
        
        $model= $class::find()
                ->one();
        
        return $model->id;
    }
     /**
     * Add or remove query arguments to the URL.
     *
     * @param  mixed  $newKey          Either newkey or an associative array
     * @param  mixed  $newValue        Either newvalue or oldquery or uri
     * @param  mixed  $uri             URI or URL to append the queru/queries to.
     * @return string
     */
    public static function add_query_arg($newKey, $newValue = null, $uri = null)
    {
        // Was an associative array of key => value pairs passed?
        if (is_array($newKey)) {
            $newParams = $newKey;

            // Was the URL passed as an argument?
            if (!is_null($newValue)) {
                $uri = $newValue;
            } elseif (!is_null($uri)) {
                $uri = $uri;
            } else {
                $uri = self::array_get($_SERVER['REQUEST_URI'], '');
            }
        } else {
            $newParams = array($newKey => $newValue);

            // Was the URL passed as an argument?
            $uri = is_null($uri) ? self::array_get($_SERVER['REQUEST_URI'], '') : $uri;
        }

        // Parse the URI into it's components
        $puri = parse_url($uri);

        if (isset($puri['query'])) {
            parse_str($puri['query'], $queryParams);
            $queryParams = array_merge($queryParams, $newParams);
        } elseif (isset($puri['path']) && strstr($puri['path'], '=') !== false) {
            $puri['query'] = $puri['path'];
            unset($puri['path']);
            parse_str($puri['query'], $queryParams);
            $queryParams = array_merge($queryParams, $newParams);
        } else {
            $queryParams = $newParams;
        }

        // Strip out any query params that are set to false
        foreach ($queryParams as $param => $value) {
            if ($value === false) {
                unset($queryParams[$param]);
            }
        }

        // Re-construct the query string
        $puri['query'] = http_build_query($queryParams);

        // Re-construct the entire URL
        $nuri = self::http_build_url($puri);

        // Make the URI consistent with our input
        if ($nuri[0] === '/' && strstr($uri, '/') === false) {
            $nuri = substr($nuri, 1);
        }

        if ($nuri[0] === '?' && strstr($uri, '?') === false) {
            $nuri = substr($nuri, 1);
        }

        return rtrim($nuri, '?');
    }
    
    /**
     * Build a URL.
     *
     * The parts of the second URL will be merged into the first according to
     * the flags argument.
     *
     * @author Jake Smith <theman@jakeasmith.com>
     * @see https://github.com/jakeasmith/http_build_url/
     *
     * @param mixed $url     (part(s) of) an URL in form of a string or
     *                       associative array like parse_url() returns
     * @param mixed $parts   same as the first argument
     * @param int   $flags   a bitmask of binary or'ed HTTP_URL constants;
     *                       HTTP_URL_REPLACE is the default
     * @param array $new_url if set, it will be filled with the parts of the
     *                       composed url like parse_url() would return
     * @return string
     */
    public static function http_build_url($url, $parts = array(), $flags = self::HTTP_URL_REPLACE, &$new_url = array())
    {
        is_array($url) || $url = parse_url($url);
        is_array($parts) || $parts = parse_url($parts);

        isset($url['query']) && is_string($url['query']) || $url['query'] = null;
        isset($parts['query']) && is_string($parts['query']) || $parts['query'] = null;

        $keys = array('user', 'pass', 'port', 'path', 'query', 'fragment');

        // HTTP_URL_STRIP_ALL and HTTP_URL_STRIP_AUTH cover several other flags.
        if ($flags & self::HTTP_URL_STRIP_ALL) {
            $flags |= self::HTTP_URL_STRIP_USER | self::HTTP_URL_STRIP_PASS
                | self::HTTP_URL_STRIP_PORT | self::HTTP_URL_STRIP_PATH
                | self::HTTP_URL_STRIP_QUERY | self::HTTP_URL_STRIP_FRAGMENT;
        } elseif ($flags & self::HTTP_URL_STRIP_AUTH) {
            $flags |= self::HTTP_URL_STRIP_USER | self::HTTP_URL_STRIP_PASS;
        }

        // Schema and host are alwasy replaced
        foreach (array('scheme', 'host') as $part) {
            if (isset($parts[$part])) {
                $url[$part] = $parts[$part];
            }
        }

        if ($flags & self::HTTP_URL_REPLACE) {
            foreach ($keys as $key) {
                if (isset($parts[$key])) {
                    $url[$key] = $parts[$key];
                }
            }
        } else {
            if (isset($parts['path']) && ($flags & self::HTTP_URL_JOIN_PATH)) {
                if (isset($url['path']) && substr($parts['path'], 0, 1) !== '/') {
                    $url['path'] = rtrim(
                            str_replace(basename($url['path']), '', $url['path']),
                            '/'
                        ) . '/' . ltrim($parts['path'], '/');
                } else {
                    $url['path'] = $parts['path'];
                }
            }

            if (isset($parts['query']) && ($flags & self::HTTP_URL_JOIN_QUERY)) {
                if (isset($url['query'])) {
                    parse_str($url['query'], $url_query);
                    parse_str($parts['query'], $parts_query);

                    $url['query'] = http_build_query(
                        array_replace_recursive(
                            $url_query,
                            $parts_query
                        )
                    );
                } else {
                    $url['query'] = $parts['query'];
                }
            }
        }

        if (isset($url['path']) && substr($url['path'], 0, 1) !== '/') {
            $url['path'] = '/' . $url['path'];
        }

        foreach ($keys as $key) {
            $strip = 'HTTP_URL_STRIP_' . strtoupper($key);
            if ($flags & constant('self::' . $strip)) {
                unset($url[$key]);
            }
        }

        $parsed_string = '';

        if (isset($url['scheme'])) {
            $parsed_string .= $url['scheme'] . '://';
        }

        if (isset($url['user'])) {
            $parsed_string .= $url['user'];

            if (isset($url['pass'])) {
                $parsed_string .= ':' . $url['pass'];
            }

            $parsed_string .= '@';
        }

        if (isset($url['host'])) {
            $parsed_string .= $url['host'];
        }

        if (isset($url['port'])) {
            $parsed_string .= ':' . $url['port'];
        }

        if (!empty($url['path'])) {
            $parsed_string .= $url['path'];
        } else {
            $parsed_string .= '/';
        }

        if (isset($url['query'])) {
            $parsed_string .= '?' . $url['query'];
        }

        if (isset($url['fragment'])) {
            $parsed_string .= '#' . $url['fragment'];
        }

        $new_url = $url;

        return $parsed_string;
    }
    /**
     * Removes an item or list from the query string.
     *
     * @param  string|array  $keys Query key or keys to remove.
     * @param  bool          $uri  When false uses the $_SERVER value
     * @return string
     */
    public static function remove_query_arg($keys, $uri = null)
    {
        if (is_array($keys)) {
            return self::add_query_arg(array_combine($keys, array_fill(0, count($keys), null)), $uri);
        }

        return self::add_query_arg(array($keys => null), $uri);
    }
    
    /**
     * Access an array index, retrieving the value stored there if it
     * exists or a default if it does not. This function allows you to
     * concisely access an index which may or may not exist without
     * raising a warning.
     *
     * @param  array  $var     Array value to access
     * @param  mixed  $default Default value to return if the key is not
     *                         present in the array
     * @return mixed
     */
    public static function array_get(&$var, $default = null)
    {
        if (isset($var)) {
            return $var;
        }

        return $default;
    }

    
    public static function prepareHourString($hour){
        
        $string= explode(':', $hour);
        
        if(!empty($string)){
            
            return $string[0].'h '.$string[1].'m';
        }
        
        return null;
    }

    public static function getCoordinates($address){
 
        $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern

        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&address=$address&key=AIzaSyDXe5pnEyFO0KGLTP3FeRtaJAV9n17yneY";

        $response = file_get_contents($url);

        $json = json_decode($response,TRUE); //generate array object from the response from the web
        $coordinate[] = $json['results'][0]['geometry']['location']['lat'];
        $coordinate[] = $json['results'][0]['geometry']['location']['lng'];

        return ($coordinate);

    }
    
    public static function decodeJWToken($token,$return=null){
        $tokenEncoded=new \Nowakowskir\JWT\TokenEncoded($_GET['id_token']);
        $result=\Nowakowskir\JWT\JWT::decode($tokenEncoded);
        $bodyResult=$result->getPayload();
        
        if(empty($return))
            return $bodyResult;
        
        if(is_array($return)){
            $returnArray= array();
            foreach ($bodyResult as $k=>$result){
               
                if(in_array($k,$return)){
                    $returnArray[$k]= $result;
                }
            }
          
            
            return $returnArray;
            
        }
        
    }
    public static  function sum_seconds($times) {
        $i = 0;
        foreach ($times as $time) {
            $time= explode(':', $time);
            $i += ($time[0] * 60) + ($time[1]*60) + $time[2];
        }
        
        return $i;
    }
    
    public static function sendSOAPRequest($url,$method,$params=null){
        
        if(!empty($url) && !empty($method)){
            $soap = new SoapClient($url,array("trace" => 1, "exception" => 1));

            $result=$soap->__soapCall($method, array(
                $method => $params
            ));
            
            
        
            return $result;
        }
        
        return 'Url e metodo non possono essere vuoti';
        
    }

    public static function openSession(){
        if(!Yii::$app->session->isActive){
            Yii::$app->session->open();
        }
    }

    public static function convertByte($size)
    {
        if (empty($size)){

            return '0 KB';
        }

        $bytes = number_format($size / 1024, 0,'','');

        if ($bytes < 900) {
            return $bytes . ' KB';
        }

        return sprintf("%4.2f MB", $size/1048576);
    }

    public static function getReferreAction($referrer)
    {
        if (!empty($referrer)){

            $url = explode('/', $referrer);
            return end($url);
        }

        return '';
    }
}

