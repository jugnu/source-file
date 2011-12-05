<?php
ob_start();
include_once 'application_controller.php';
//require_once("/init.php");
require_once("dao/userDAO.php");
require_once("dao/giftDAO.php");
require_once("dao/locationDAO.php");
require_once("dao/giftlocationDAO.php");
require_once("dao/profileDAO.php");
require_once("dao/categoryDAO.php");
require_once("dao/orderDAO.php");
require_once("dao/userclaimgiftDAO.php");
require_once("helpers/resize-class.php");
require_once("helpers/send_mail.php");
require_once("helpers/CallerService.php");
//require_once("helpers/class.xmltoarray.php");

class vendersController extends application_controller {
    public function encrypt_password($password) {
        $encrypt_password=md5(md5(SALT.$password));
        return $encrypt_password;
    }
    public function getFaq() {
        return $this->render("views/users/faq.php",compact('cat_list'));
    }
    public function getAddGift() {

        $category_list = new categoryDAO();
        $cat_list = $category_list->getAll();
        return $this->render("views/users/add_gift.php",compact('cat_list'));
    }

        private  function fnWriteXml($string) {
           
            $file="controller/testFile.txt";
            if(file_exists($file)) {
                
                $fileid = fopen($file,"a");
                $strmsg = "";
                $strmsg.= "***************************************************\r\n";
                $strmsg.=$string;
                $strmsg.="\r\n***************************************************\r\n";
                fwrite($fileid,$strmsg);
                fclose($fileid);
            }else {
                
                $fileid = fopen($file,"a");
                $strmsg = $string;
                fwrite($fileid,$strmsg);
                fclose($fileid);
            }
        }


    public function getGoogleResponse() {



       // session_start();
        $xml_data = $HTTP_RAW_POST_DATA;
//Get rid of PHP's magical escaping of quotes

//$this->fnWriteXml($xml_data);
       if (get_magic_quotes_gpc()) {

           $xml_data = stripslashes($xml_data);
// Capture the Return Response XML from the Google Checkout.
           $this->fnWriteXml($xml_data);
            
       }

      
 

        


    }



    public function getGoogleResponse2() {

        session_start();
        $xml_data = $HTTP_RAW_POST_DATA;
//Get rid of PHP's magical escaping of quotes


        if (get_magic_quotes_gpc()) {
            $xml_data = stripslashes($xml_data);
// Capture the Return Response XML from the Google Checkout.
            fnWriteXml($xml_data);
        }

        function fnWriteXml($string) {
            $file="controller/testFile.txt";
            if(file_exists($file)) {
                $fileid = fopen($file,"a");
                $strmsg = "";
                $strmsg.= "***************************************************\r\n";
                $strmsg.=$string;
                $strmsg.="\r\n***************************************************\r\n";
                fwrite($fileid,$strmsg);
                fclose($fileid);
            }else {
                $fileid = fopen($file,"a");
                $strmsg = $string;
                fwrite($fileid,$strmsg);
                fclose($fileid);
            }
        }


        function xml2ary($string) {
            $parser = xml_parser_create();
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
            xml_parse_into_struct($parser, $string, $vals, $index);
            xml_parser_free($parser);

            $mnary=array();
            $ary=&$mnary;
            foreach ($vals as $r) {
                $t=$r['tag'];
                if ($r['type']=='open') {
                    if (isset($ary[$t])) {
                        if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                        $cv=&$ary[$t][count($ary[$t])-1];
                    } else $cv=&$ary[$t];
                    if (isset($r['attributes'])) {
                        foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;
                    }
                    $cv['_c']=array();
                    $cv['_c']['_p']=&$ary;
                    $ary=&$cv['_c'];

                } elseif ($r['type']=='complete') {
                    if (isset($ary[$t])) { // same as open
                        if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                        $cv=&$ary[$t][count($ary[$t])-1];
                    } else $cv=&$ary[$t];
                    if (isset($r['attributes'])) {
                        foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;
                    }
                    $cv['_v']=(isset($r['value']) ? $r['value'] : '');

                } elseif ($r['type']=='close') {
                    $ary=&$ary['_p'];
                }
            }

            _del_p($mnary);
            return $mnary;
        }

// _Internal: Remove recursion in result array
        function _del_p(&$ary) {
            foreach ($ary as $k=>$v) {
                if ($k==='_p') unset($ary[$k]);
                elseif (is_array($ary[$k])) _del_p($ary[$k]);
            }
        }

// Array to XML
        function ary2xml($cary, $d=0, $forcetag='') {
            $res=array();
            foreach ($cary as $tag=>$r) {
                if (isset($r[0])) {
                    $res[]=ary2xml($r, $d, $tag);
                } else {
                    if ($forcetag) $tag=$forcetag;
                    $sp=str_repeat("\t", $d);
                    $res[]="$sp<$tag";
                    if (isset($r['_a'])) {
                        foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";
                    }
                    $res[]=">".((isset($r['_c'])) ? "\n" : '');
                    if (isset($r['_c'])) $res[]=ary2xml($r['_c'], $d+1);
                    elseif (isset($r['_v'])) $res[]=$r['_v'];
                    $res[]=(isset($r['_c']) ? $sp : '')."</$tag>\n";
                }

            }
            return implode('', $res);
        }

// Insert element into array
        function ins2ary(&$ary, $element, $pos) {
            $ar1=array_slice($ary, 0, $pos);
            $ar1[]=$element;
            $ary=array_merge($ar1, array_slice($ary, $pos));
        }
        $data=xml2ary($xml_data);
//Displaying the Array
//echo "<pre>";
//echo print_r($data['new-order-notification']['_c']['google-order-number']['_v']);
//echo "<br/>";
//echo print_r($data['new-order-notification']['_c']['order-total']['_v']);
//echo "<br/>";
//echo "<pre>";
//print_r($data);
//echo "</pre>";


        $TRANSACTIONID=$data['new-order-notification']['_c']['google-order-number']['_v'];
        $amouttotal=$data['new-order-notification']['_c']['order-total']['_v'];
        $locations = $_SESSION['location_id'];
        $orderDAO = new orderDAO();
        $order_id = $orderDAO->setOrder($_SESSION['address'],$_SESSION['city'],$_SESSION['state'],$_SESSION['zip'],'testname','',time(),'',"proved",$_SESSION['term'],$amouttotal,$_SESSION['venderid'],$_SESSION['gift_id'],$TRANSACTIONID);
        for ($i=0; $i<count($locations);$i++) {
            $last_location_id = $orderDAO->setOrderLocatoin($order_id,$locations[$i]);
        }
        $giftDAO = new giftDAO();
        $giftDAO->activateGift($_SESSION['gift_id']);
        unset($_SESSION['gift_id']);
        unset($_SESSION['address']);
        unset($_SESSION['city']);
        unset($_SESSION['state']);
        unset($_SESSION['zip']);
        unset($_SESSION['term_selected']);
        unset($_SESSION['user_location']);
        // $giftDAO = new giftDAO();
        //  $result = $giftDAO->getUserGifts($_SESSION['venderid']);
        //   $reports = $giftDAO->getUserGiftsReport($_SESSION['venderid']);
        //   return $this->render("views/users/vender_dashboard.php",compact('result','reports','resArray'));

    }
    public function storeAddress() {
//        var_dump($_POST['baddress']);
//        var_dump($_POST['city']);
//        var_dump($_POST['state']);
//        var_dump($_POST['zip']);
//        var_dump($_POST['phone']);
        $_SESSION["baddress"] = $_POST['baddress'];
        $_SESSION["city"] = $_POST['city'];
        $_SESSION["state"] = $_POST['state'];
        $_SESSION["zip"] = $_POST['zip'];
        $_SESSION["phone"] = $_POST['phone'];
        var_dump($_SESSION["baddress"]);
        die;
    }
    public function verifyEmail($email) {
        $userDAO = new userDAO();
        $result = $userDAO->verifyEmail($email);
        if(mysql_num_rows($result)>0) {
            echo "0";
//            return false;
        }
        else {
            echo "1";
//            return true;
        }
    }
    public function addGift() {
        $cat_id = $_POST["option_id"];
        $email = $_POST['email'];
        $password = $_POST['pass'];
        $pass=$this->encrypt_password($password);
        $name = $_POST["name"];
        $gift = $_POST["gift"];
        $gift_value = $_POST["gift_value"];
        $gift_perday = $_POST["gift_perday"];
        $description = $_POST["description"];
        $image_name = $_FILES['image']['name'];
        $target_path = "public/uploaded_images";
        $image_name = $_SESSION['image_name'];
        $target_path = "public/uploaded_images";
        $bemail = $_POST["bemail"];
        $bphone = $_POST["bphone"];
        $website = $_POST["website"];
        $baddress = $_POST["baddress"];
        $activiation_key=mt_rand().mt_rand().mt_rand();
        $userDAO = new userDAO();
        $user_id = $userDAO->setUser("$email","$pass","","12-10-2001","$name","2","$cat_id","1","$activiation_key","1","0");
        $profileDAO = new profileDAO();
        $profileDAO->setUserProfile("$user_id","","", "","","","","","$bemail","$bphone", "$website");
        $giftDao = new giftDAO();
        $gift_id = $giftDao->setGift("$gift","$gift_value","$gift_perday","$description","$image_name","$user_id");
        $locationDAO = new locationDAO();
        $giftlocationDAO = new giftlocationDAO();
        foreach($baddress as $key=>$value) {
            $city = $_POST['city'][$key];
            $state = $_POST['state'][$key];
            $zip = $_POST['zip'][$key];
            $phone = $_POST["phone"][$key];
            if($value!="" && $city!="" && $zip!="") {
                $location_id = $locationDAO->setLocation("$value","$phone","$city","$state","$zip","$user_id");
            }
        }
        if($user_id!="") {
            $to = $email;
            $from = "test.account.rac@gmail.com";
            $headers = "From: $from";
            // $message = "Welcome to <a href=''>WWW.birthday.com</a>,Here you can add your gifts and people would love them";
            $message = "Hi Vender! <br/>
                Welcome to our website you are successfully registered at<a href=' ".site_url()."users/activate_user/".$activiation_key."'>".site_url()."users/activate_user/".$activiation_key."</a>";
            $sendmail=new send_mail();
            //  $sendmail->send_email($to,$subject,$message,$headers);
        }
        $answer = $userDAO->getEmail($user_id);
        //// $this->login_vender($answer['business_name'],$answer['id']);
        // $_SESSION['gift_id'] = $gift_id;
        //$_SESSION['cat_id'] = $cat_id;
        //$_SESSION['active'] = "no";
        unset($_SESSION['image_name']);
//            $category_list = new categoryDAO();
//            $cat_name = $category_list->getCategory($cat_id);
//            $_SESSION['cat_name'] = $cat_name;
//            $gift_info = $giftDao->getGift($gift_id);
//            while($data = mysql_fetch_array($gift_info)) {
//                $_SESSION['gift_info'] = $data['gift_intro'];
//            }

//            return $this->render("views/users/checkout.php",compact('result'));
//            var_dump(site_url()."views/users/checkout.php");
        ///  redirect_to(site_url());
        $answer = $userDAO->getEmail($user_id);
        $this->login_vender($answer['business_name'],$answer['id']);
        $_SESSION['gift_id'] = $gift_id;
        $_SESSION['cat_id'] = $cat_id;
        unset($_SESSION['image_name']);
        redirect_to(site_url()."venders/renderCheckout");
    }
    public function uploadTempImg() {
        $target_path = "public/uploaded_images";
        $image_name = $name;
        var_dump($_FILES['image']['name']);
//        $_FILES['image']['tmp_name'] = $name;
        $temp_image = "tttt.jpeg";
        if(move_uploaded_file($temp_image,"$target_path/$image_name")) {
            $resizeObj = new resize($target_path."/".$image_name);
            $resizeObj -> resizeImage(244,280,'crop');
            $resizeObj -> saveImage($target_path."/".$image_name,100);
            $_SESSION['img_name'] = $name;
            echo $_SESSION['img_name'];
        }
        else {
            echo"not uploaded";
        }
    }
    public function addUsergift() {

      
         $_SESSION['set_locations_add'] = $_POST["set_locations"];
    //var_dump($_SESSION['set_locations_add']);

        $gift = $_POST["gift"];
        $gift_value = $_POST["gift_value"];
        $gift_perday = $_POST["gift_perday"];
        $description = $_POST["description"];
        $image_name = $_SESSION['image_name'];
        $target_path = "public/uploaded_images";
        $baddress = $_POST["baddress"];

        $giftDAO = new giftDAO();
        $gift_id = $giftDAO->setGift($gift,$gift_value,$gift_perday,$description,$image_name,$_SESSION['venderid']);
        $userDAO = new userDAO();
        $cat_id = $userDAO->getCatId($_SESSION['venderid']);
        $_SESSION['gift_id'] = $gift_id;
        $_SESSION['cat_id'] = $cat_id;
        foreach($baddress as $key=>$value) {
            $city = $_POST['city'][$key];
            $state = $_POST['state'][$key];
            $zip = $_POST['zip'][$key];
            $phone = $_POST["phone"][$key];
            if($value!="" && $city!="" && $zip!="") {
                $locationDAO = new locationDAO();
                $location_id = $locationDAO->setLocation("$value","$phone","$city","$state","$zip",$_SESSION['venderid']);
            $new_loc= Array($location_id);

        // $valueee=$_SESSION['set_locations_add'];
       // $valueee += $idee;
        $_SESSION['set_locations_add']=array_merge($_SESSION['set_locations_add'], $new_loc);
            }
        }
      // var_dump($_SESSION['set_locations_add']);

        redirect_to(site_url()."venders/renderCheckout");

    }
    public function same_as_bus() {
        $userDAO = new userDAO();
        $data = $userDAO->getUserbus($_SESSION['venderid']);
        echo '<div class="conLeft1">
                            <div class="regInputMain1 mtop20">
                                <label>Address :</label>
                                <input type="text" name="address1" id="address" value="'.$data['address'].'" />
                            </div>
                            <div class="regInputMain1 mtop20">
                                <label>City :</label>
                                <input type="text" name="city" value="'.$data['city'].'" />
                            </div>
                            <div class="regInputMain1 mtop20">
                               <label>State :</label>
                                <select id=state name=state>
                                    <option value=></option>
                                    <option value=AK '; if(!empty($data['state']) && $data['state']=='AK') echo "selected"; echo'>AK</option>
                                    <option value=AL '; if(!empty($data['state']) && $data['state']=='AL') echo "selected"; echo'>AL</option>
                                    <option value=AR '; if(!empty($data['state']) && $data['state']=='AR') echo "selected"; echo'>AR</option>
                                    <option value=AZ '; if(!empty($data['state']) && $data['state']=='AZ') echo "selected"; echo'>AZ</option>
                                    <option value=CA '; if(!empty($data['state']) && $data['state']=='CA') echo "selected"; echo'>CA</option>
                                    <option value=CO '; if(!empty($data['state']) && $data['state']=='CO') echo "selected"; echo'>CO</option>
                                    <option value=CT '; if(!empty($data['state']) && $data['state']=='CT') echo "selected"; echo'>CT</option>
                                    <option value=DC '; if(!empty($data['state']) && $data['state']=='DC') echo "selected"; echo'>DC</option>
                                    <option value=DE '; if(!empty($data['state']) && $data['state']=='DE') echo "selected"; echo'>DE</option>
                                    <option value=FL '; if(!empty($data['state']) && $data['state']=='FL') echo "selected"; echo'>FL</option>
                                    <option value=GA '; if(!empty($data['state']) && $data['state']=='GA') echo "selected"; echo'>GA</option>
                                    <option value=HI '; if(!empty($data['state']) && $data['state']=='HI') echo "selected"; echo'>HI</option>
                                    <option value=IA '; if(!empty($data['state']) && $data['state']=='IA') echo "selected"; echo'>IA</option>
                                    <option value=ID '; if(!empty($data['state']) && $data['state']=='ID') echo "selected"; echo'>ID</option>
                                    <option value=IL '; if(!empty($data['state']) && $data['state']=='IL') echo "selected"; echo'>IL</option>
                                    <option value=IN '; if(!empty($data['state']) && $data['state']=='IN') echo "selected"; echo'>IN</option>
                                    <option value=KS '; if(!empty($data['state']) && $data['state']=='KS') echo "selected"; echo'>KS</option>
                                    <option value=KY '; if(!empty($data['state']) && $data['state']=='KY') echo "selected"; echo'>KY</option>
                                    <option value=LA '; if(!empty($data['state']) && $data['state']=='LA') echo "selected"; echo'>LA</option>
                                    <option value=MA '; if(!empty($data['state']) && $data['state']=='MA') echo "selected"; echo'>MA</option>
                                    <option value=MD '; if(!empty($data['state']) && $data['state']=='MD') echo "selected"; echo'>MD</option>
                                    <option value=ME '; if(!empty($data['state']) && $data['state']=='ME') echo "selected"; echo'>ME</option>
                                    <option value=MI '; if(!empty($data['state']) && $data['state']=='MI') echo "selected"; echo'>MI</option>
                                    <option value=MN '; if(!empty($data['state']) && $data['state']=='MN') echo "selected"; echo'>MN</option>
                                    <option value=MO '; if(!empty($data['state']) && $data['state']=='MO') echo "selected"; echo'>MO</option>
                                    <option value=MS '; if(!empty($data['state']) && $data['state']=='MS') echo "selected"; echo'>MS</option>
                                    <option value=MT '; if(!empty($data['state']) && $data['state']=='MT') echo "selected"; echo'>MT</option>
                                    <option value=NC '; if(!empty($data['state']) && $data['state']=='NC') echo "selected"; echo'>NC</option>
                                    <option value=ND '; if(!empty($data['state']) && $data['state']=='ND') echo "selected"; echo'>ND</option>
                                    <option value=NE '; if(!empty($data['state']) && $data['state']=='NE') echo "selected"; echo'>NE</option>
                                    <option value=NH '; if(!empty($data['state']) && $data['state']=='NH') echo "selected"; echo'>NH</option>
                                    <option value=NJ '; if(!empty($data['state']) && $data['state']=='NJ') echo "selected"; echo'>NJ</option>
                                    <option value=NM '; if(!empty($data['state']) && $data['state']=='NM') echo "selected"; echo'>NM</option>
                                    <option value=NV '; if(!empty($data['state']) && $data['state']=='NV') echo "selected"; echo'>NV</option>
                                    <option value=NY '; if(!empty($data['state']) && $data['state']=='NY') echo "selected"; echo'>NY</option>
                                    <option value=OH '; if(!empty($data['state']) && $data['state']=='OH') echo "selected"; echo'>OH</option>
                                    <option value=OK '; if(!empty($data['state']) && $data['state']=='OK') echo "selected"; echo'>OK</option>
                                    <option value=OR '; if(!empty($data['state']) && $data['state']=='OR') echo "selected"; echo'>OR</option>
                                    <option value=PA '; if(!empty($data['state']) && $data['state']=='PA') echo "selected"; echo'>PA</option>
                                    <option value=RI '; if(!empty($data['state']) && $data['state']=='RI') echo "selected"; echo'>RI</option>
                                    <option value=SC '; if(!empty($data['state']) && $data['state']=='SC') echo "selected"; echo'>SC</option>
                                    <option value=SD '; if(!empty($data['state']) && $data['state']=='SD') echo "selected"; echo'>SD</option>
                                    <option value=TN '; if(!empty($data['state']) && $data['state']=='TN') echo "selected"; echo'>TN</option>
                                    <option value=TX '; if(!empty($data['state']) && $data['state']=='TX') echo "selected"; echo'>TX</option>
                                    <option value=UT '; if(!empty($data['state']) && $data['state']=='UT') echo "selected"; echo'>UT</option>
                                    <option value=VA '; if(!empty($data['state']) && $data['state']=='VA') echo "selected"; echo'>VA</option>
                                    <option value=VT '; if(!empty($data['state']) && $data['state']=='VT') echo "selected"; echo'>VT</option>
                                    <option value=WA '; if(!empty($data['state']) && $data['state']=='WA') echo "selected"; echo'>WA</option>
                                    <option value=WI '; if(!empty($data['state']) && $data['state']=='WI') echo "selected"; echo'>WI</option>
                                    <option value=WV '; if(!empty($data['state']) && $data['state']=='WV') echo "selected"; echo'>WV</option>
                                    <option value=WY '; if(!empty($data['state']) && $data['state']=='WY') echo "selected"; echo'>WY</option>
                                    <option value=AA '; if(!empty($data['state']) && $data['state']=='AA') echo "selected"; echo'>AA</option>
                                    <option value=AE '; if(!empty($data['state']) && $data['state']=='AE') echo "selected"; echo'>AE</option>
                                    <option value=AP '; if(!empty($data['state']) && $data['state']=='AP') echo "selected"; echo'>AP</option>
                                    <option value=AS '; if(!empty($data['state']) && $data['state']=='AS') echo "selected"; echo'>AS</option>
                                    <option value=FM '; if(!empty($data['state']) && $data['state']=='FM') echo "selected"; echo'>FM</option>
                                    <option value=GU '; if(!empty($data['state']) && $data['state']=='GU') echo "selected"; echo'>GU</option>
                                    <option value=MH '; if(!empty($data['state']) && $data['state']=='NM') echo "selected"; echo'>MH</option>
                                    <option value=MP '; if(!empty($data['state']) && $data['state']=='MP') echo "selected"; echo'>MP</option>
                                    <option value=PR '; if(!empty($data['state']) && $data['state']=='PR') echo "selected"; echo'>PR</option>
                                    <option value=PW '; if(!empty($data['state']) && $data['state']=='PW') echo "selected"; echo'>PW</option>
                                    <option value=VI '; if(!empty($data['state']) && $data['state']=='VI') echo "selected"; echo'>VI</option>
                                </select>
                                
                            </div>
                            <div class="regInputMain1 mtop20">
                                <label>Zip :</label>
                                <input type="text" name=zip value="'.$data['zipcode'].'"/>
                            </div>
                        </div';
        // echo $array = array($data['city'], $data['zipcode'],$data['phone']);
// "city=".$data['city']."&zip=".$data['zipcode']."&address=".$data['phone'];
    }
    public function getLocations() {
        $baddress = $_GET['baddress'];
        $city = $_GET['city'];
        $state = $_GET['state'];
        $zip = $_GET['zip'];
        $phone = $_GET['phone'];
//        var_dump($bddress);
        $user_id = $id;
        $locationDAO = new locationDAO();
//        $locations = $locationDAO->getLocation($user_id);
        return $this->render("partials/_addresses.php",compact('baddress','city','state','zip','phone'));
    }
    public function getuserLocations($id) {
        $locationDAO = new locationDAO();
        $locations = $locationDAO->getLocation($id);
        return $this->render("partials/_locations.php",compact('locations'));


    }
    public function getAddress($id) {
        $location_id = $id;
        $locationDAO = new locationDAO();
        $address = $locationDAO->getAddress($location_id);
        return $this->render("partials/_address.php",compact('address'));
    }
    public function dellocation($id) {
        $location_id = $id;
        $locationDAO = new locationDAO();
        $address = $locationDAO->getuserd($location_id);
        $total=mysql_num_rows($address);
        if(empty($total)){
        $locationDAO = new locationDAO();
        $address = $locationDAO->delNotUserdLocation($location_id);
        $locationDAO = new locationDAO();
        $locations = $locationDAO->getLocation($_SESSION['venderid']);
        return $this->render("partials/_locations.php",compact('locations'));
        }else{
             echo "0";
        }
       
    }
    public function updateAddress() {
        $baddress = $_GET['address'];
        $bphone = $_GET['phone'];
        $city = $_GET['city'];
        $state = $_GET['state'];
        $zip = $_GET['zip'];
        $location_id = $_GET['id'];
        $user_id = $_GET['user_id'];
        $locationDAO = new locationDAO();
        $locations = $locationDAO->updateLocation($baddress,$bphone,$city,$state,$zip,$location_id,$user_id);
        return $this->render("partials/_locations.php",compact('locations'));
    }
    public function giftView() {
//        $giftDAO = new giftDAO();
//        $categoryDAO = new categoryDAO();
//        $userDAO = new userDAO();
//        $cat_id = $userDAO->getCatId($id);
//        $cat_name = $categoryDAO->getCategory($cat_id);
//        $result = $giftDAO->getUserGifts($id);
        if(empty($_GET['b_name'])){
             $b_name = $_SESSION['vender_name'];
        } else{
           $b_name = $_GET['b_name'];
        }
        $cat_name = $_GET['cat_name'];
        
        $name = $_GET['name'];
        $value = $_GET['value'];
        $perday = $_GET['perday'];
        $desc = $_GET['desc'];

        return $this->render("partials/_proofview.php",compact('b_name','name','value','perday','desc','cat_name'));
    }
    public function giftViewPreview($gift_id) {
//        $giftDAO = new giftDAO();
//        $categoryDAO = new categoryDAO();
//        $userDAO = new userDAO();
//        $cat_id = $userDAO->getCatId($id);
//        $cat_name = $categoryDAO->getCategory($cat_id);
//        $result = $giftDAO->getUserGifts($id);

        $businessgift=new giftDAO();
        $result=$businessgift->get_Gift_byPreview($gift_id);

        return $this->render("partials/_proofview_gift.php",compact('result'));
    }
    public function getReg() {
        return $this->render("partials/_register.php",compact('var1','var2'));
    }
    public function getLogDiv() {
        return $this->render("partials/_login.php",compact('var1','var2'));
    }
    public function loginVenedr() {

        $userDAO =new userDAO();
        $answer=$userDAO->verifyVenderlogin($_GET['email']);
        if(!$answer) {
            echo "Invalid Email Address!";
        } else {

            $userDAO =new userDAO();
            $answer=$userDAO->verifyVenderActive($_GET['email']);
            if(!$answer) {
                echo "Email Address is not active!";
            }  else {
                $userDAO =new userDAO();
                $answer=$userDAO->logVender($_GET['email'],$this->encrypt_password($_GET['pass']));
                if(!$answer) {
                    echo "Invalid Password";
                }  else {
                    $_SESSION['cat_id']=$answer['cat_id'];
                    $this->login_vender($answer['business_name'],$answer['id']);
                    echo "1";
                }
            }


        }
    }
    public function logoutAdmin() {
        unset($_SESSION['vender_name']);
        unset($_SESSION['admin_session_id']);
        session_destroy();
        redirect_to(site_url()."home/index");
    }
        public function logoutVender() {
        unset($_SESSION['vender_name']);
         unset($_SESSION['admin_session_id']);
        unset($_SESSION['venderid']);
        session_destroy();
        redirect_to(site_url()."home/index");
    }
    public function paypalReview() {

    }
    public function dashBoard() {
        if(empty($_SESSION['venderid'])) {
            redirect_to(site_url()."home/index");
        }
        if(empty($_SESSION['gift_id'])) {
            $giftDAO = new giftDAO();
            $result = $giftDAO->getUserGifts($_SESSION['venderid']);
            $reports = $giftDAO->getUserGiftsReport($_SESSION['venderid']);
            return $this->render("views/users/vender_dashboard.php",compact('result','reports'));
        }
        if(isset($_SESSION['loginname']) && $_SESSION['loginname']!='') {
            $vender_id = $_SESSION["userid"];
        }
        if(isset($_REQUEST['checkout_yes']) && $_REQUEST['checkout_yes']=="yes") {
            if($_POST['term_selected']=="term1") {
                for ($i=1; $i<=count($_POST['user_location']);$i++) {
                    $test = $i;
                }
                $amount = 60 * $test;
            } elseif($_POST['term_selected']=="term2") {
                for ($i=1; $i<=count($_POST['user_location']);$i++) {
                    $test = $i;
                }
                $amount = 30 * $test;
            }else {
                for ($i=1; $i<=count($_POST['user_location']);$i++) {
                    $test = $i;
                }
                $amount = 350 * $test;
            }
            if($_REQUEST['creditCardType']=='GoogleCheckout') {

             
                $paymentType =urlencode( $_POST['paymentType']);
                $firstName =urlencode( $_POST['firstName']);
                $lastName =urlencode( $_POST['lastName']);
                // $creditCardType =urlencode( $_POST['creditCardType']);
                //  $creditCardNumber = urlencode($_POST['creditCardNumber']);
                // $expDateMonth =urlencode( $_POST['expDateMonth']);
                // Month must be padded with leading zero
                //  $padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
                //$expDateYear =urlencode( $_POST['expDateYear']);
                //$cvv2Number = urlencode($_POST['cvv2Number']);
                $address1 = urlencode($_POST['address1']);
                $address2 = urlencode($_POST['address2']);
                $city = urlencode($_POST['city']);
                $state =urlencode($_POST['state']);
                $zip = urlencode($_POST['zip']);
                $currencyCode="USD";
                $paymentType="Sale";

      
                $giftDao = new giftDAO();
                $gift_info = $giftDao->getGiftinfo($_SESSION['gift_id']);
                
                              $orderDAO = new orderDAO();
                    $order_id = $orderDAO->setOrder($_POST["address1"],$_POST["city"],$_POST["state"],$_POST["zip"],$_POST["lastName"],'',time(),'',"none",$_POST["term_selected"],$amount,$vender_id,$_POST["gift_id"],$resArray['TRANSACTIONID']);
                    for ($i=0; $i<count($_POST['user_location']);$i++) {
                        $last_location_id = $orderDAO->setOrderLocatoin($order_id,$_POST['user_location'][$i]);
                    }

                    
                echo "<html>\n";
                echo "<head><title>Processing Payment...</title></head>\n";
                echo "<body onLoad=\"document.BB_BuyButtonForm.submit();\">\n";
                echo "<center><h3>Please wait,  your order is being processed...</h3></center>\n";


                echo '<form action="https://sandbox.google.com/checkout/api/checkout/v2/checkoutForm/Merchant/646088904248786" id="BB_BuyButtonForm" method="post" name="BB_BuyButtonForm" target="_top">
   <input name="item_selection_1" type="hidden" value="1"/>

<input name="item_option_name_1" type="hidden" value="Birthday"/>
                <input name="item_option_price_1" type="hidden" value="'.$amount.'"/>
                <input name="item_option_description_1" type="hidden" value="'.$gift_info.'"/>
                <input name="item_option_quantity_1" type="hidden" value="1"/>
                <input name="item_option_currency_1" type="hidden" value="USD"/>
                 <input name="item_option_order_1" type="hidden" value="111111"/>
                <input type="hidden" name="ship_method_name_1" value="no"/>
                <input type="hidden" name="shopping-cart.merchant-private-data" value="'.$order_id.'">
                 <input type="hidden" name="shopping-cart.merchant-gift-data" value="'.$_SESSION['gift_id'].'">

  <input type="hidden" name="ship_method_price_1" value="00"/>
  <input type="hidden" name="ship_method_us_area_1" value="FULL_50_STATES"/>
  <input type="hidden" name="ship_method_currency_1" value="USD"/>
<input name="order_no" type="hidden" value="1111"/>
  <input type="hidden" name="tax_rate" value="00"/>
  <input type="hidden" name="tax_us_state" value="'.$state.'"/>
                </form>';
                echo "</form>\n";
                echo "</body></html>\n";
                die;
            } else if($_REQUEST['creditCardType']=='Paypal') {
                $token = $_REQUEST['token'];
                if(! isset($token)) {
                    unset($_SESSION['gift_id']);
                    unset($_SESSION['address']);
                    unset($_SESSION['city']);
                    unset($_SESSION['state']);
                    unset($_SESSION['zip']);
                    unset($_SESSION['term_selected']);
                    unset($_SESSION['user_location']);
                    $serverName = $_SERVER['SERVER_NAME'];
                    $serverPort = $_SERVER['SERVER_PORT'];
                    $url=dirname('http://'.$serverName.':'.$serverPort.$_SERVER['REQUEST_URI']);
                    $currencyCodeType="USD";
                    $paymentType="Sale";
                    $personName        = $_REQUEST['lastName'];
                    $locations = $_REQUEST['user_location'];
                    $_SESSION['location_id'] = $locations;
                    $SHIPTOSTREET      = $_REQUEST['address1'];
                    $_SESSION['address'] = $SHIPTOSTREET;
                    $SHIPTOCITY        = $_REQUEST['city'];
                    $_SESSION['city'] = $SHIPTOCITY;
                    $SHIPTOSTATE	      = $_REQUEST['state'];
                    $_SESSION['state'] = $SHIPTOSTATE;
                    $SHIPTOCOUNTRYCODE = $_REQUEST['SHIPTOCOUNTRYCODE'];
                    $SHIPTOZIP         = $_REQUEST['zip'];
                    $_SESSION['zip'] = $SHIPTOZIP;
                    $term         = $_REQUEST['term_selected'];
                    $_SESSION['term'] = $term;
                    $L_NAME0           =$_REQUEST['L_NAME1'];
                    $L_AMT0            = $amount;
                    $L_QTY0            = "1";
                    $returnURL =urlencode($url.'/dashBoard/?currencyCodeType='.$currencyCodeType.'&paymentType='.$paymentType.'&creditCardType=Paypal&checkout_yes=yes');
                    $cancelURL =urlencode("$url/dashBoard" );
                    $itemamt = 0.00;
                    $itemamt = $L_AMT0;
                    $amt = $itemamt;
                    $maxamt= $amt+25.00;
                    $nvpstr="";
                    $shiptoAddress = "&SHIPTONAME=$personName&SHIPTOSTREET=$SHIPTOSTREET&SHIPTOCITY=$SHIPTOCITY&SHIPTOSTATE=$SHIPTOSTATE&SHIPTOCOUNTRYCODE=$SHIPTOCOUNTRYCODE&SHIPTOZIP=$SHIPTOZIP";
                    $nvpstr="&ADDRESSOVERRIDE=1$shiptoAddress&METHOD=SetExpressCheckout&L_NAME0=".$L_NAME0."&L_AMT0=".$L_AMT0."&L_QTY0=".$L_QTY0."&MAXAMT=".(string)$maxamt."&AMT=".(string)$amt."&ITEMAMT=".(string)$itemamt."&CALLBACKTIMEOUT=4&INSURANCEOPTIONOFFERED=true&L_NUMBER0=1000&L_DESC0=Size: 8.8-oz&ReturnUrl=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$currencyCodeType."&PAYMENTACTION=".$paymentType;
                    $nvpstr = $nvpHeader.$nvpstr;
                    $resArray=hash_call("SetExpressCheckout",$nvpstr);
                    $_SESSION['reshash']=$resArray;

                    $ack = strtoupper($resArray["ACK"]);

                    if($ack=="SUCCESS") {
                        ob_start();
                        // Redirect to paypal.com here
                        $token = urldecode($resArray["TOKEN"]);
                        $payPalURL = PAYPAL_URL.$token;
                        header("Location: ".$payPalURL);
                    } else {
                        $category_list = new categoryDAO();
                        $cat_name = $category_list->getCategory($cat_id);
                        $_SESSION['cat_name'] = $cat_name;
                        $giftDao = new giftDAO();
                        $gift_info = $giftDao->getGift($_SESSION['gift_id']);
                        $_SESSION['gift_info'] = $gift_info;

                        $user_id = $_SESSION['venderid'];
                        $locationDAO = new locationDAO();
                        $result = $locationDAO->getLocation($user_id);
                        return $this->render("views/users/checkout.php",compact('result','resArray'));
                    }
                } else {
                    /* At this point, the buyer has completed in authorizing payment
			at PayPal.  The script will now call PayPal with the details
			of the authorization, incuding any shipping information of the
			buyer.  Remember, the authorization is not a completed transaction
			at this state - the buyer still needs an additional step to finalize
			the transaction
                    */
                    $token =urlencode( $_REQUEST['token']);
                    /* Build a second API request to PayPal, using the token as the
			ID to get the details on the payment authorization
                    */
                    $nvpstr="&TOKEN=".$token;
                    $nvpstr = $nvpHeader.$nvpstr;
                    /* Make the API call and store the results in an array.  If the
			call was a success, show the authorization details, and provide
			an action to complete the payment.  If failed, show the error
                    */
                    $resArray=hash_call("GetExpressCheckoutDetails",$nvpstr);
                    $_SESSION['reshash']=$resArray;
                    $ack = strtoupper($resArray["ACK"]);

                    if($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
                        $_SESSION['paymentAmount']=$_REQUEST['paymentAmount'];
                        $resArray=$_SESSION['reshash'];
                        $_SESSION['TotalAmount']= $resArray['AMT'] + $resArray['SHIPDISCAMT'];
                        ini_set('session.bug_compat_42',0);
                        ini_set('session.bug_compat_warn',0);
                        /* Gather the information to make the final call to
                            finalize the PayPal payment.  The variable nvpstr
                            holds the name value pairs
                        */
                        $token =urlencode( $_REQUEST['token']);
                        $paymentAmount =urlencode ($_SESSION['TotalAmount']);
                        $paymentType = urlencode($_REQUEST['paymentType']);
                        $currCodeType = urlencode($_REQUEST['currencyCodeType']);
                        $payerID = urlencode($_REQUEST['PayerID']);
                        $serverName = urlencode($_SERVER['SERVER_NAME']);
                        $nvpstr='&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName ;
                        /* Make the call to PayPal to finalize payment
                           If an error occured, show the resulting errors
                        */
                        $resArray=hash_call("DoExpressCheckoutPayment",$nvpstr);

                        /* Display the API response back to the browser.
                            If the response from PayPal was a success, display the response parameters'
                            If the response was an error, display the errors received using APIError.php.
                        */
                        $ack = strtoupper($resArray["ACK"]);
                        if($ack != 'SUCCESS' && $ack != 'SUCCESSWITHWARNING') {
                            $_SESSION['reshash']=$resArray;
                            $category_list = new categoryDAO();
                            $cat_name = $category_list->getCategory($cat_id);
                            $_SESSION['cat_name'] = $cat_name;
                            $giftDao = new giftDAO();
                            $gift_info = $giftDao->getGift($_SESSION['gift_id']);
                            $_SESSION['gift_info'] = $gift_info;

                            $user_id = $_SESSION['venderid'];
                            $locationDAO = new locationDAO();
                            $result = $locationDAO->getLocation($user_id);
                            return $this->render("views/users/checkout.php",compact('result','resArray'));
                        }
                        else {
                            $locations = $_SESSION['location_id'];
                            $orderDAO = new orderDAO();
                            $order_id = $orderDAO->setOrder($_SESSION['address'],$_SESSION['city'],$_SESSION['state'],$_SESSION['zip'],'testname','',time(),'',"proved",$_SESSION['term'],$resArray['AMT'],$_SESSION['venderid'],$_SESSION['gift_id'],$resArray['TRANSACTIONID']);
                            for ($i=0; $i<count($locations);$i++) {
                                $last_location_id = $orderDAO->setOrderLocatoin($order_id,$locations[$i]);
                            }
                            $giftDAO = new giftDAO();
                            $giftDAO->activateGift($_SESSION['gift_id']);
                            unset($_SESSION['gift_id']);
                            unset($_SESSION['address']);
                            unset($_SESSION['city']);
                            unset($_SESSION['state']);
                            unset($_SESSION['zip']);
                            unset($_SESSION['term_selected']);
                            unset($_SESSION['user_location']);
                            $giftDAO = new giftDAO();
                            $result = $giftDAO->getUserGifts($_SESSION['venderid']);
                            $reports = $giftDAO->getUserGiftsReport($_SESSION['venderid']);
                            return $this->render("views/users/vender_dashboard.php",compact('result','reports','resArray'));
                        }
                    } else {
                        $category_list = new categoryDAO();
                        $cat_name = $category_list->getCategory($cat_id);
                        $_SESSION['cat_name'] = $cat_name;
                        $giftDao = new giftDAO();
                        $gift_info = $giftDao->getGift($_SESSION['gift_id']);
                        $_SESSION['gift_info'] = $gift_info;

                        $user_id = $_SESSION['venderid'];
                        $locationDAO = new locationDAO();
                        $result = $locationDAO->getLocation($user_id);
                        return $this->render("views/users/checkout.php",compact('result','resArray'));
                    }
                }

            } else {
                $paymentType =urlencode( $_POST['paymentType']);
                $firstName =urlencode( $_POST['firstName']);
                $lastName =urlencode( $_POST['lastName']);
                $creditCardType =urlencode( $_POST['creditCardType']);
                $creditCardNumber = urlencode($_POST['creditCardNumber']);
                $expDateMonth =urlencode( $_POST['expDateMonth']);
                // Month must be padded with leading zero
                $padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);
                $expDateYear =urlencode( $_POST['expDateYear']);
                $cvv2Number = urlencode($_POST['cvv2Number']);
                $address1 = urlencode($_POST['address1']);
                $address2 = urlencode($_POST['address2']);
                $city = urlencode($_POST['city']);
                $state =urlencode( $_POST['state']);
                $zip = urlencode($_POST['zip']);
                $currencyCode="USD";
                $paymentType="Sale";

                /* Construct the request string that will be sent to PayPal.
                   The variable $nvpstr contains all the variables and is a
                   name value pair string with & as a delimiter */
                $nvpstr="&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber&EXPDATE=".         $padDateMonth.$expDateYear."&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName&STREET=$address1&CITY=$city&STATE=$state"."&ZIP=$zip&COUNTRYCODE=US&CURRENCYCODE=$currencyCode";
                $resArray=hash_call("doDirectPayment",$nvpstr);

                $ack = strtoupper($resArray["ACK"]);
                if($ack!="SUCCESS") {



                    if(isset ($_SESSION['cat_id'])) {
                        $cat_id = $_SESSION['cat_id'];
                    }
                    $category_list = new categoryDAO();
                    $cat_name = $category_list->getCategory($cat_id);
                    $_SESSION['cat_name'] = $cat_name;
                    $giftDao = new giftDAO();
                    $gift_info = $giftDao->getGift($_SESSION['gift_id']);
                    $_SESSION['gift_info'] = $gift_info;

                    $user_id = $_SESSION['venderid'];
                    $locationDAO = new locationDAO();
                    $result = $locationDAO->getLocation($user_id);
                    return $this->render("views/users/checkout.php",compact('result','resArray'));
                } else {

                    $orderDAO = new orderDAO();
                    $order_id = $orderDAO->setOrder($_POST["address1"],$_POST["city"],$_POST["state"],$_POST["zip"],$_POST["lastName"],'',time(),'',"proved",$_POST["term_selected"],$amount,$vender_id,$_POST["gift_id"],$resArray['TRANSACTIONID']);
                    for ($i=0; $i<count($_POST['user_location']);$i++) {
                        $last_location_id = $orderDAO->setOrderLocatoin($order_id,$_POST['user_location'][$i]);
                    }
                    $giftDAO = new giftDAO();
                    $giftDAO->activateGift($_SESSION['gift_id']);
                    unset($_SESSION['gift_id']);
                    $giftDAO = new giftDAO();
                    $result = $giftDAO->getUserGifts($_SESSION['venderid']);
                    $reports = $giftDAO->getUserGiftsReport($_SESSION['venderid']);
                    return $this->render("views/users/vender_dashboard.php",compact('result','reports','resArray'));
                }
            }

        }
        $category_list = new categoryDAO();
        $cat_name = $category_list->getCategory($cat_id);
        $_SESSION['cat_name'] = $cat_name;
        $giftDao = new giftDAO();
        $gift_info = $giftDao->getGift($_SESSION['gift_id']);
        $_SESSION['gift_info'] = $gift_info;

        $user_id = $_SESSION['venderid'];
        $locationDAO = new locationDAO();
        $result = $locationDAO->getLocation($user_id);
        return $this->render("views/users/checkout.php",compact('result','resArray'));
    }
    public function addView() {
        $locationDAO = new locationDAO();
        $locations = $locationDAO->getLocation($_SESSION['venderid']);
        return $this->render("views/users/addgift.php",compact('locations','var2'));
    }
    public function addLocationView() {
        return $this->render("views/users/addlocation.php",compact('var1','var2'));
    }

    public function addUserLocation() {
        if(isset($_SESSION['loginname']) && $_SESSION['loginname']!='') {
            $id = $_SESSION["vender_user_id"];
        }
        $baddress = $_POST['baddress'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip = $_POST['zip'];
        $phone = $_POST['phone'];
        $locationDAO = new locationDAO();
        $locationDAO->setLocation($baddress, $phone, $city, $state, $zip,$id);
        $giftDAO = new giftDAO();
        $result = $giftDAO->getUserGifts($id);
        $reports = $giftDAO->getUserGiftsReport($id);
        return $this->render("views/users/vender_dashboard.php",compact('result','reports'));
    }
    public function checkOutFromDashBoard($gift_id) {
        $_SESSION['gift_id'] = $gift_id;
        redirect_to(site_url()."venders/renderCheckout");

    }
    public function renderCheckout() {
       
        if(empty($_SESSION['venderid'])) {
            redirect_to(site_url()."home/index");
        }
        if(empty($_SESSION['gift_id'])) {
            $giftDAO = new giftDAO();
            $result = $giftDAO->getUserGifts($_SESSION['venderid']);
            $reports = $giftDAO->getUserGiftsReport($_SESSION['venderid']);
            return $this->render("views/users/vender_dashboard.php",compact('result','reports'));
        }
        if(isset ($_SESSION['cat_id'])) {
            $cat_id = $_SESSION['cat_id'];
        }
        $category_list = new categoryDAO();
        $cat_name = $category_list->getCategory($cat_id);
        $_SESSION['cat_name'] = $cat_name;
        $giftDao = new giftDAO();
        $gift_info = $giftDao->getGift($_SESSION['gift_id']);
        $_SESSION['gift_info'] = $gift_info;
        $user_id = $_SESSION['venderid'];
        $locationDAO = new locationDAO();
        $result = $locationDAO->getLocation($user_id);
        return $this->render("views/users/checkout.php",compact('result'));
    }
    public function checkoutBack() {
        //it will print if any URL errors
        $resArray=$_SESSION['reshash'];
        if(isset($_SESSION['curl_error_no'])) {
            //echo "i m here";
            $errorCode= $_SESSION['curl_error_no'] ;
            $errorMessage=$_SESSION['curl_error_msg'];
            unset($_SESSION['curl_error_no']);
            unset($_SESSION['curl_error_msg']);
            //session_unset('curl_error_no');
            //session_unset('curl_error_msg');
        }
        $user_id = $_SESSION['venderid'];
        $locationDAO = new locationDAO();
        $result = $locationDAO->getLocation($user_id);
        return $this->render("views/users/checkout.php",compact('result','resArray'));
    }
    public function venderDashBoard() {
        unset($_SESSION['set_locations_add']);
        if(empty($_SESSION['venderid'])) {
            redirect_to(site_url()."home/index");
        }
        $giftDAO = new giftDAO();
        $result = $giftDAO->getUserGifts($_SESSION['venderid']);
        $reports = $giftDAO->getUserGiftsReport($_SESSION['venderid']);
        return $this->render("views/users/vender_dashboard.php",compact('result','reports'));
    }
    public function get_claimed($gift_id) {

        $giftDAO = new giftDAO();
        $result = $giftDAO->getClaimedTotal($gift_id);

        echo $claimedDone=$result;
    }
    public function myAccount() {
        if(empty($_SESSION['venderid'])) {
            redirect_to(site_url()."home/index");
        }
        $userprofileDAO= new profileDAO();
        $userdata=$userprofileDAO->get_user_data($_SESSION['venderid']);
        return $this->render("views/users/myAccount.php",compact('userdata'));
    }
    public function sortBy($str) {
        $giftDAO = new giftDAO();
        $result = $giftDAO->getSortedGifts($_SESSION['venderid'],$str);
        if(!isset ($result)) {
            echo "0";
        }else {
            while($data = mysql_fetch_array($result)) {
                echo ' <div class="paymentMethod1">

            <div class="offerDiv1"><input type="checkbox" /></div>
            <div class="offerDiv2"><input type="checkbox" name="check" onchange="editGift(this)" id='.$data['id']. ' /></div>
            <div class="offerDiv3"><input type="checkbox" /></div>
            <div class="offerDiv4">'.$data['gift_intro'].'</div>
            <div class="offerDiv5">'. $data['zipcode'].'</div>
            <div class="offerDiv6">'. date('d/m/Y',strtotime($data['created_on'])).'</div>
            <div class="offerDiv7">';
                if($data['is_active']==1) {
                    echo "active";

                }else {
                    echo   "inactive";

                }
                echo '</div>
            <div class="offerDiv8">'.$data['gift_value'].'</div>
            <div class="offerDiv9">'.$data['gift_p_day'].'</div>
        </div>';

            }
        }

    }
    public function sortByReport($str) {
        $giftDAO = new giftDAO();
        $result = $giftDAO->getSortedGifts($_SESSION['venderid'],$str);
        if(!isset ($result)) {
            echo "0";
        }else {
            while($data = mysql_fetch_array($result)) {
                echo ' <div class="paymentMethod1">
            <div class="offerDiv1"><input type="checkbox" /></div>
            <div class="offerDivMain2">'.$data['gift_intro'].'</div>
            <div class="offerDivMain3">'. $data['zipcode'].'</div>
            <div class="offerDivMain4">'. date('d/m/Y',strtotime($data['created_on'])).'</div>
            <div class="offerDivMain5">';
                if($data['is_active']==1) {
                    echo "active";

                }else {
                    echo   "inactive";

                }
                echo '</div>
            <div class="offerDivMain6">'.$data['gift_value'].'</div>
            <div class="offerDivMain7">'.$data['gift_p_day'].'</div>
                <div class="offerDivMain8"><input type="submit" value="Run Report" /></div>
        </div>';

            }
        }
    }
    public function checkout() {
        if(isset($_SESSION["vender_user_id"]) && $_SESSION["vender_user_id"]!='') {
            $id = $_SESSION["vender_user_id"];
        }
        $giftDAO = new giftDAO();
        $result = $giftDAO->getUserGifts($id);
        $reports = $giftDAO->getUserGiftsReport($id);
        return $this->render("views/users/checkout.php",compact('result','reports'));
    }
    public function newAddress() {
        return $this->render("partials/newaddress.php",compact('var','var1'));
    }
    public function addupload() {

        @unlink(site_url().'public/uploaded_images/'.$_SESSION['image_name']);

        $image_name = $_FILES['uploadfile']['name'];
        $target_path = "public/uploaded_images";
        if(move_uploaded_file($_FILES['uploadfile']['tmp_name'],"$target_path/$image_name")) {
            $resizeObj = new resize($target_path."/".$image_name);
            $resizeObj -> resizeImage(244,280,'crop');
            $resizeObj -> saveImage($target_path."/".$image_name,100);
            $_SESSION['image_name']=$image_name;
            echo "success";
        } else {
            $_SESSION['image_name']='';
            echo "error";
        }



    }
    public function viewGiftClaimed() {
        $userclaimgiftdao = new userclaimgiftDAO();
        $claiminfo = $userclaimgiftdao->viewclaimed($_GET['gift_id']);
        return $this->render("partials/viewGiftClaimed.php",compact('claiminfo'));
    }
    public function viewGiftlocation() {
        $userclaimgiftdao = new userclaimgiftDAO();
        $locationinfo = $userclaimgiftdao->viewLocation($_GET['gift_id']);
        return $this->render("partials/viewGiftLocation.php",compact('locationinfo'));
    }

    public function UpdateStatus() {
        $id = $_GET['gift_id'];
        $giftDAO = new giftDAO();
        $current_status = $giftDAO->getGiftStatus($id);
        if($current_status=='1') {
            $status_val='0';
        }else {
            $status_val='1';
        }
        $result = $giftDAO->UpdateGiftStatus($id,$status_val);
        if($result=='1') {
            if($status_val=='1') {
                echo "Active";
            }else {
                echo "In_Active";
            }
        }else {
            echo "Try Again!";
        }

    }
    public function UpdateLocationStatus() {
        $id = $_GET['orderdetail_id_location'];
        $locationDAO = new locationDAO();
        $current_status = $locationDAO->getLocationStatus($id);
        if($current_status=='1') {
            $status_val='0';
        }else {
            $status_val='1';
        }
        $result = $locationDAO->UpdateLocationStatus($id,$status_val);
        if($result=='1') {
            if($status_val=='1') {
                echo "Active";
            }else {
                echo "In_Active";
            }
        }else {
            echo "Try Again!";
        }

    }
    public function editGift() {
        $id = $_SESSION['gift_id'];
        $giftDAO = new giftDAO();
        $result = $giftDAO->getGift($id);
        return $this->render("views/users/edit_gift.php",compact('result'));

    }
    public function editGiftDash($gift_id) {
        $giftDAO = new giftDAO();
        $result = $giftDAO->getGift($gift_id);
        return $this->render("partials/_editgift.php",compact('result'));
    }
    public function addGiftPopUp($gift_id) {
        $giftDAO = new giftDAO();
        $result = $giftDAO->getGift($gift_id);
        return $this->render("partials/_addGift.php",compact('result'));
    }
    public function updateGift() {
        $desc = $_POST['description'];
        $giftDAO = new giftDAO();
        if($_FILES['image']['name']=="") {
            $giftDAO->updateGift($_SESSION['gift_id'],$_POST['gift'],$_POST['gift_value'],$_POST['gift_perday'],$desc,$_POST['hidden_image']);

        }else {
            $image_name = $_FILES['image']['name'];
            $target_path = "public/uploaded_images";
            if(move_uploaded_file($_FILES['image']['tmp_name'],"$target_path/$image_name")) {
                $resizeObj = new resize($target_path."/".$image_name);
                $resizeObj -> resizeImage(200,200,'crop');
                $resizeObj -> saveImage($target_path."/".$image_name,100);
                $giftDAO->updateGift($_SESSION['gift_id'],$_POST['gift'],$_POST['gift_value'],$_POST['gift_perday'],$desc,$image_name);
            }
        }
        redirect_to(site_url()."venders/renderCheckout");
    }
    public function quickSearch($str) {

        $giftDAO = new giftDAO();
        $result = $giftDAO->SearchgetUserGifts($_SESSION['venderid'],$str);
        $reports = $giftDAO->SearchgetUserGiftsReport($_SESSION['venderid'],$str);
        $search="search";
        return $this->render("views/users/vender_dashboard.php",compact('result','reports','search'));
//        $giftDAO = new giftDAO();
//        $result = $giftDAO->searchByDescription($str);
//        return $this->render("views/users/searchView.php",compact('result'));
    }
    public function addGiftDash() {
        // var_dump($_POST['gift']);
        $giftDAO = new giftDAO();
        if($_FILES['image']['name']=="") {
            $gift_id = $giftDAO->setGift($_POST['gift'],$_POST['gift_value'],$_POST['gift_perday'],$_POST['description'],$_POST['hidden_image'],$_SESSION['venderid']);
        }else {
            $image_name = $_FILES['image']['name'];
            $target_path = "public/uploaded_images";
            if(move_uploaded_file($_FILES['image']['tmp_name'],"$target_path/$image_name")) {
                $resizeObj = new resize($target_path."/".$image_name);
                $resizeObj -> resizeImage(200,200,'crop');
                $resizeObj -> saveImage($target_path."/".$image_name,100);
                $gift_id = $giftDAO->setGift($_POST['gift'],$_POST['gift_value'],$_POST['gift_perday'],$_POST['description'],$image_name,$_SESSION['venderid']);
            }
        }
        $_SESSION['gift_id'] = $gift_id;
        $_SESSION["message"]="Gift is addeded successfully";
        redirect_to(site_url()."venders/renderCheckout");
    }
    public function updateGiftDash() {
        // var_dump($_POST['hidden_gift']);
        $desc = $_POST['description'];
        $giftDAO = new giftDAO();
        if($_FILES['image']['name']=="") {
            $giftDAO->updateGift($_POST['hidden_gift'],$_POST['gift'],$_POST['gift_value'],$_POST['gift_perday'],$desc,$_POST['hidden_image']);

        }else {
            $image_name = $_FILES['image']['name'];
            $target_path = "public/uploaded_images";
            if(move_uploaded_file($_FILES['image']['tmp_name'],"$target_path/$image_name")) {
                $resizeObj = new resize($target_path."/".$image_name);
                $resizeObj -> resizeImage(200,200,'crop');
                $resizeObj -> saveImage($target_path."/".$image_name,100);
                $giftDAO->updateGift($_POST['hidden_gift'],$_POST['gift'],$_POST['gift_value'],$_POST['gift_perday'],$desc,$image_name);

            }

        }
        $_SESSION["message"]="Gift is updated successfully";
        redirect_to(site_url()."venders/venderDashBoard");
    }
    public function addAddress() {
        return $this->render("partials/_add_address.php",compact('result'));
    }
    public function addressPopUp() {
        return $this->render("partials/_dash_address.php",compact('result'));
    }
    public function get_map() {
        $b_name1 = $_GET['b_name1'];
      $address = $_GET['address'];
        $city = $_GET['city'];
        $state = $_GET['state'];
        $zip = $_GET['zip'];
     $phone = $_GET['phone'];

       $addressary = $_GET['addressary'];
        $cityary = $_GET['cityary'];
        $stateary = $_GET['stateary'];
        $zipary = $_GET['zipary'];
          $phoneary = $_GET['phoneary'];

      $set_locations = $_GET['set_locations'];
      if(empty($set_locations)){
          $resultloc='none';
      } else{
        $set_loc = "(". $set_locations.")";
        $locationdao= new locationDAO();
        $resultloc=$locationdao->getAddressMapshow($set_locations);
         $locationdao= new locationDAO();
        $resultloc_map=$locationdao->getAddressMapshow($set_locations);
      }
        return $this->render("partials/_preview_location.php", compact('b_name1','address','city','state','zip','phone','addressary','cityary','stateary','zipary','phoneary','resultloc','resultloc_map','set_loc'));
    }
        public function get_map_next($ptr) {
       

        $locationdao= new locationDAO();
        $resultloc=$locationdao->getAddress($ptr);

        return $this->render("partials/_preview_location_next.php", compact('resultloc'));
    }
     public function get_map_next_ads($ads) {

        
      $resultloc=$_POST['ads'];

        return $this->render("partials/_preview_location_next_ads.php", compact('resultloc'));
    }
    public function show_map() {
        require_once("helpers/EasyGoogleMap.class.php");
        $address= $_GET['address'].' '.$_GET['city'].' '.$_GET['state'].' '.$_GET['zip'].'<br/> '.$_GET['phone'];
        //   $address = $_GET['address'];
        //   $city = $_GET['city'];
        $gm = & new EasyGoogleMap("ABQIAAAAvI6p3LumT-noI1gHlgPv3BTR2jPHZED0VoZURrXZc4WfIdfgThRQ7qZ2WA7K9gkEbhCk07VXbUPOLQ");
        $gm->SetMarkerIconStyle('GT_FLAT');
        $gm->SetMapZoom(10);
        $gm->SetAddress(" ".$address);
        $gm->SetInfoWindowText($address);
        return $this->render("views/users/_show_map.php", compact('gm'));
    }
        public function show_map_next_ads() {
        require_once("helpers/EasyGoogleMap.class.php");
        $address= $_GET['ads'];
        //   $address = $_GET['address'];
        //   $city = $_GET['city'];
        $gm = & new EasyGoogleMap("ABQIAAAAvI6p3LumT-noI1gHlgPv3BTR2jPHZED0VoZURrXZc4WfIdfgThRQ7qZ2WA7K9gkEbhCk07VXbUPOLQ");
        $gm->SetMarkerIconStyle('GT_FLAT');
        $gm->SetMapZoom(10);
        $gm->SetAddress(" ".$address);
        $gm->SetInfoWindowText($address);
        return $this->render("views/users/_show_map.php", compact('gm'));
    }
    public function runReport($id) {
        if($id=="") {
            redirect_to(site_url()."venders/venderDashBoard");
        }
        $userclaimgiftDAO = new userclaimgiftDAO();
        $result = $userclaimgiftDAO->getClaimedGifts($id);
        $check = mysql_fetch_array($result);
        if(empty ($check)) {
            redirect_to(site_url()."venders/venderDashBoard");
        }
        else {
            //$userclaimgiftDAO = new userclaimgiftDAO();
            // $result = $userclaimgiftDAO->claimedGift($id);
            $userclaimgiftdao = new userclaimgiftDAO();
            $result = $userclaimgiftdao->viewclaimed($id);
            $giftDAO = new giftDAO();
            $data = $giftDAO->getGift($id);
            return $this->render("views/users/gift_report.php",compact('result','data'));
        }
    }
    public function updateLocations($id) {
        $locationDAO = new locationDAO();
        $result = $locationDAO->getLocation($id);
        if(!isset ($result)) {
            echo "0";
        }else {
            while($data = mysql_fetch_array($result)) {
                echo '<div class="contMain mtop20">
        <div class="locLeftDetails">
            <p><input name="user_location" value='.$data['id'].' type="checkbox">&nbsp;'.$data['business_address'].''.$data['city'].'</p>
        </div>
        <div class="locCenterDetails">
            <p>1 Year</p>
        </div>
        <div class="locRightDetails">
            <p>$ 350.00</p>
        </div>
    </div>';
            }

        }

    }
    public function addNewAddress() {
        $locationDAO = new locationDAO();
        $result = $locationDAO->setLocation($_GET['address'],$_GET['phone'],$_GET['city'],$_GET['state'],$_GET['zip'],$_GET['user_id']);
        if(!isset ($result)) {
            echo "0";
        }else {
            $user_id = $_SESSION['venderid'];
            $locationDAO = new locationDAO();
            $result = $locationDAO->getLocation($user_id);
            while($data = mysql_fetch_array($result)) {
                echo '<div class="contMain mtop20">
        <div class="locLeftDetails">
            <p><input name="user_location" value='.$data['id'].' type="checkbox">&nbsp;'.$data['business_address'].' &nbsp; '.$data['city'].'</p>
        </div>
        <div class="locCenterDetails">
            <p>1 Year</p>
        </div>
        <div class="locRightDetails">
            <p>$ 350.00</p>
        </div>
    </div>';
            }
        }
    }
    public function addDashAddrress() {

        $locationDAO = new locationDAO();
        $result = $locationDAO->setLocation($_GET['newaddress'],$_GET['newphone'],$_GET['newcity'],$_GET['newstate'],$_GET['newzip'],$_SESSION['venderid']);
        $locationDAO = new locationDAO();
        $locations = $locationDAO->getLocation($_SESSION['venderid']);
        return $this->render("partials/_locations.php",compact('locations'));
    }
    public function faqs() {
        return $this->render("views/users/faqs.php",compact(''));
    }
    public function aboutus() {
        return $this->render("views/users/aboutus.php",compact(''));
    }
    public function contact() {
        return $this->render("views/users/contact.php",compact(''));
    }
    public function contactus() {
        if(!empty($_POST['email'])) {
            echo     $to='meer.aali@ilsainteractive.com';
            //$to = "humza.shahid@ilsainteractive.com";
            $subject = "Birthday";
            //$message = "please activate  this account link is given below";
            //$message = "Welcome to our website you are successfully registered at<a href=' ".$_SERVER['HTTP_REFERER']."?action=activeuser&user_active=".$activiation_key."'>".$_SERVER['HTTP_REFERER']."</a>";
            $message = $_POST['massage_user'];
            //$message = "Welcome to our website you are successfully registered at ".site_url();
            //$from = "test.account.rac@gmail.com";
            $from =  $_POST['email'];
            $name =  $_POST['name'];
            $headers = "From: $name";
            $sendmail=new send_mail();
            $sendmail->send_email($to,$subject,$message,$headers);
            $_SESSION['message']="User Register Successfully. Email has been send to your account";


        }
        redirect_to(site_url()."venders/venderDashBoard/");
        //  $this->venderDashBoard();
    }
    public function videos() {
        return $this->render("views/users/videos.php",compact(''));
    }
    public function help() {
        return $this->render("views/users/help.php",compact(''));
    }
    public function howitwork() {
        return $this->render("views/users/howitwork.php",compact(''));
    }
    public function updateInfo() {

        $id= $_SESSION['venderid'];

        $firstname=$_POST['firstname'];
        $lastname=$_POST['lastname'];
        $city=$_POST['city'];
        $address=$_POST['address'];
        $phone=$_POST['phone'];
        $state=$_POST['state'];
        $zip=$_POST['zip'];
        $dob="";


        $userprofiledao = new profileDAO();
        $userprofiledao->updateInfo($id, $firstname, $lastname, $address,$city, $phone,$state);
        $userdao =new userDAO();
        $userdao->updatezipnopass($id, $zip,$dob);

        $_SESSION['message']= "Save Successfully";
        $this->myAccount();

    }
}

?>