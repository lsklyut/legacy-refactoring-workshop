<?php 
    $con=mysql_connect('localhost','root','');
    $select_db = mysql_select_db('api',$con);
    if(!$con){
        echo "Not connected..";
    }
    $base_url = "http://localhost:8000/";
    $method = $_REQUEST['method'];
    
    if($method==1) //Profile Get
    {
        $output = "";
        $user_id = $_REQUEST['user_id'];
        $sql = "SELECT user_fname,user_lname,user_status,user_profile_img,user_login_by FROM `user` WHERE user_id = '$user_id'";
        $res = mysql_query($sql);
        $contents = array();
        while($queRow = mysql_fetch_array($res))
        {
            $contents['user_name'] = $queRow['user_fname'];
            $contents['status'] = $queRow['user_status'];
            $loginby = $queRow['user_login_by'];
            if($loginby=='facebook')
            {
                $image = $queRow['user_profile_img'];
            }
            else if($loginby=='google')
            {
                $image = $queRow['user_profile_img'];
            }
            else if($loginby=='twitter')
            {
                $image = $queRow['user_profile_img'];
            }
            else{
                $image = $queRow['user_profile_img'];
                if($image==""){
                    $image = "images/profile.png";
                }else{
                    $image = $base_url.$image;
                }
            }
            $contents['image'] = $image;
            echo json_encode(array("event" => $contents));
        }
    }
    
    if($method==2) //Profile Get
    {
        $output = "";
        class Emp {}
        $e = new Emp();
        $user_name = $_REQUEST['user_name'];
        $status = $_REQUEST['status'];
        $user_id = $_REQUEST['userid'];
        $sql = "update `user` set user_fname='$user_name',user_status='$status' WHERE user_id = '$user_id'";
        $res = mysql_query($sql);
        if($res){
            $e->user_id = "0";
        }else{
            $e->user_id = "1";
        }
        echo json_encode(array("event" => $e));
    }

    if($method==3) //Profile Get
    {
        $output = "";
        class Emp {}
        $e = new Emp();
        $user_name = $_REQUEST['user_name'];
        $file_name = $_REQUEST['file_name'];
        $status = $_REQUEST['status'];
        $user_id = $_REQUEST['userid'];
        $sql = "update `user` set user_fname='$user_name',user_status='$status',user_profile_img='$file_name' WHERE user_id = '$user_id'";
        $res = mysql_query($sql);
        if($res){
            $e->user_id = "0";
        }else{
            $e->user_id = "1";
        }
        echo json_encode(array("event" => $e));
    }
    
    //Single POST
    if($method==4) //Profile Get
    {
        $output = "";
        $data_id = $_REQUEST['data_id'];
        $user_id = $_REQUEST['user_id'];
        $sql = "SELECT user_id,wall_text,wall_thumb,datetime,total_like,total_dislike,total_comment FROM `wall` WHERE wall_id='$data_id'";
        $res = mysql_query($sql);
        $contents = array();
        if($queRow = mysql_fetch_array($res))
        {
            mysql_query("update wall set total_view=total_view+1 where wall_id='$data_id'");
            $datetime = date('Y-m-d H:i:s');
            mysql_query("INSERT INTO `wall_view`(`wall_id`, `user_id`, `datetime`) VALUES('$data_id','$user_id','$datetime')");
            
            $user_id1 = $queRow[0];
            $sql1 = "SELECT user_fname,user_profile_img,user_login_by FROM `user` WHERE user_id='$user_id1'";
            $res1 = mysql_fetch_array(mysql_query($sql1));
            
            $contents['user_name'] = $res1[0];
            $status = $res1[2];
            
            if($status!=""){
                $image = $res1[1];
            }else{
                $image = $res1[1];
                if(!file_exists($image) || $image==""){
                    $image = "images/userid.png";
                }else{
                    $image = $base_url.$image;
                }
            }
            
            $contents['user_image'] = $image;
            $contents['wall_text'] = $queRow['wall_text'];
            $contents['wall_thumb'] = $base_url.$queRow['wall_thumb'];
            $contents['datetime'] = $queRow['datetime'];
            $contents['total_like'] = $queRow['total_like'];
            $contents['total_dislike'] = $queRow['total_dislike'];
            $contents['total_comment'] = $queRow['total_comment'];
            
        }
        echo json_encode(array("event" => $contents));
    }
    
    //Comment Insert
    if($method==5)
    {
        $output = "";
        $data_id = $_REQUEST['data_id'];
        $cmnt = urldecode($_REQUEST['cmnt']);
        $user_id = $_REQUEST['user_id'];
        $datetime = date('Y-m-d H:i:s');
        
        preg_match_all('/(#\w+)/', $cmnt, $matches);
        foreach ($matches[0] as $hashtag)
        {
            $hst = explode("#", $hashtag);
            mysql_query("insert into wall_category(catname,wall_id,user_id) values('$hst[1]','$data_id','$user_id')");
        }
        
        
        $contents = array();
        $sql = mysql_query("insert into wall_cmnt(wall_id,user_id,datetime,cmnt_txt) values('$data_id','$user_id','$datetime','$cmnt')");
        $sql = mysql_query(" update wall set total_comment=total_comment+1 where wall_id='$data_id'");
        
        
        if($sql){
            $contents['status'] = "0";
        }else{
            $contents['status'] = "1";
        }
        echo json_encode(array("event" => $contents));
    }
    
    //Comment Data
    if($method==6)
    {
        $output = "";
        $data_id = $_REQUEST['data_id'];
        $count =0;
        $b = mysql_query("SELECT `user_id`,`cmnt_txt`,`datetime` FROM `wall_cmnt` WHERE `wall_id`='$data_id' Order by cmnt_id asc"); 
        while($fetch1=mysql_fetch_array($b))
        {
            $count =1; 
            $user_id = $fetch1[0];
            $sql1 = "SELECT user_fname,user_profile_img,user_login_by FROM `user` WHERE user_id='$user_id'";
            $res1 = mysql_fetch_array(mysql_query($sql1));
            
            $user_name = $res1[0];
            $status = $res1[2];
            
            if($status!=""){
                $image = $res1[1];
            }else{
                $image = $res1[1];
                if(!file_exists($image) || $image==""){
                    $image = "images/userid.png";
                }else{
                    $image = $base_url.$image;
                }
            }
            
            $arr[]=array(
                            'user_id'=>$user_id,'user_name'=>$user_name,'image'=>$image,'datetime'=>$fetch1['datetime'],'cmnt_txt'=>$fetch1['cmnt_txt']
                        );
            
            $contents['result'] = 'Succesfuly';
            $contents['signature'] =$arr;
        }
        if($count != 1)
        {
            $contents['result'] = 'no post found';
        }
        echo json_encode($contents);
    }
    
        //Like Insert
    if($method==7)
    {
        $output = "";
        $data_id = $_REQUEST['data_id'];
        $user_id = $_REQUEST['user_id'];
        $datetime = date('Y-m-d H:i:s');
        $contents = array();
        
        $sqli = mysql_query("select * from wall_like where user_id='$user_id' and wall_id='$data_id'");
        if($rowi = mysql_fetch_array($sqli))
        {
            $contents['status'] = "2";
        }
        else{
            $sql = mysql_query("insert into wall_like(wall_id,user_id,datetime) values('$data_id','$user_id','$datetime')");
            $sql = mysql_query(" update wall set total_like=total_like+1 where wall_id='$data_id'");
            if($sql){
                $contents['status'] = "0";
            }else{
                $contents['status'] = "1";
            }
        }
        echo json_encode(array("event" => $contents));
    }
    
        //Unlike Insert
    if($method==8)
    {
        $output = "";
        $data_id = $_REQUEST['data_id'];
        $user_id = $_REQUEST['user_id'];
        $datetime = date('Y-m-d H:i:s');
        $contents = array();
        
        $sqli = mysql_query("select * from wall_unlike where user_id='$user_id' and wall_id='$data_id'");
        if($rowi = mysql_fetch_array($sqli))
        {
            $contents['status'] = "2";
        }
        else{
            $sql = mysql_query("insert into wall_unlike(wall_id,user_id,datetime) values('$data_id','$user_id','$datetime')");
            $sql = mysql_query(" update wall set total_dislike=total_dislike+1 where wall_id='$data_id'");
            if($sql){
                $contents['status'] = "0";
            }else{
                $contents['status'] = "1";
            }
        }
        echo json_encode(array("event" => $contents));
    }
    
    //All Category
    if($method==9)
    {
        $count = 0;
        $user_id = $_REQUEST['user_id'];
        $mainarray = array();
        $b = mysql_query("SELECT `catname` FROM `wall_category` where user_id='$user_id' group by catname"); 
        while($fetch1=mysql_fetch_array($b))
        {
            $cat_name = $fetch1[0];
            $count = 1;
            $contents = array();
            $wc = mysql_query("SELECT wall_id from wall_category where catname='$cat_name' and user_id='$user_id'");
            while($qwc = mysql_fetch_array($wc))
            {
            
                $wall_id = $qwc[0];
                $w = mysql_query("SELECT wall_thumb,wall_text from wall where wall_id='$wall_id'");
                if($walldata=mysql_fetch_array($w))
                {
                    $count =1;
                    $arr[] = array('wall_id'=>$wall_id,
                                    'wall_thumb'=>"http://localhost:8000/".$walldata['wall_thumb'],
                                    'wall_text'=>$walldata['wall_text']
                                );
                    $contents['wall'] =$arr;
                }
            }
            unset($arr);
            $newarr[] = array(
                                'catname' => $fetch1[0],
                                'walls'=>$contents
                            );
            $mainarray["cat"] = $newarr;
        }
        if($count != 1)
        {
            $mainarray['cat'] = 'no post found';
        }
        echo json_encode($mainarray);
    }
    
    //Friends Profile
    if($method==10)
    {
        $user_id = $_REQUEST['frnd_id'];
        $frnd_id = $_REQUEST['user_id'];
        $contents = array();
        $frnd = "NO";
        $sqlchkfrnd = "SELECT friend_id,request_status from friends where (user_id1='$user_id' and user_id2='$frnd_id') or (user_id2='$user_id' and user_id1='$frnd_id')";
        $reschkfrnd = mysql_query($sqlchkfrnd);
        if($rowfrnd = mysql_fetch_array($reschkfrnd)){
            $frnd = $rowfrnd['request_status'];
        }
        $contents['friend_status'] = $frnd;
        
        $sql = "SELECT user_fname,user_lname,user_status,user_profile_img,user_login_by FROM `user` WHERE user_id = '$user_id'";
        $res = mysql_query($sql);
        while($queRow = mysql_fetch_array($res))
        {   
            $contents['user_name'] = $queRow['user_fname'];
            $contents['status'] = $queRow['user_status'];
            $loginby = $queRow['user_login_by'];
            if($loginby=='facebook')
            {
                $image = $queRow['user_profile_img'];
            }
            else if($loginby=='google')
            {
                $image = $queRow['user_profile_img'];
            }
            else if($loginby=='twitter')
            {
                $image = $queRow['user_profile_img'];
            }
            else{
                $image = $queRow['user_profile_img'];
                if($image==""){
                    $image = "images/profile.png";
                }
                $image = $base_url.$image;
            }
            $contents['image'] = $image;
        }
        
        $chkwall=0;
        $b = mysql_query("SELECT `wall_id`,`wall_status`,`datetime`,`wall_thumb`,`wall_text` FROM `wall` WHERE `user_id`='$user_id' Order by user_id desc"); 
        while($fetch1=mysql_fetch_array($b))
        {
            $chkwall=1;
            $arr[]=array(
                            'wall_id'=>$fetch1['wall_id'],'wall_status'=>$fetch1['wall_status'],'datetime'=>$fetch1['datetime']
                            ,'wall_thumb'=>"http://localhost:8000/".$fetch1['wall_thumb'],'wall_text'=>$fetch1['wall_text']
                        );
            $contents['walls'] =$arr;
        }
        if($chkwall==0){
            $contents['walls'] ="No";
        }
        
        echo json_encode($contents);
    }

    if($method==11)
    {
        $user_id = $_REQUEST['user_id'];
        $frnd_id = $_REQUEST['frnd_id'];
        $sql = mysql_query("Insert into friends(user_id1,user_id2,request_by,request_status) values('$user_id','$frnd_id','$user_id','0')");
        if($sql){
            $contents["result"] = "1";
        }
        else{
            $contents["result"] = "0";
        }
        echo json_encode($contents);
    }
    
    //All Friends
    if($method==12)
    {
        $user_id = $_REQUEST['user_id'];
        $contents = array();
        $datatext = array();
        $chk=0;
        $sqlrow = mysql_query("SELECT user_id1,user_id2 from friends where (user_id1='$user_id' or user_id2='$user_id') and request_status='1'");
        while($queRow = mysql_fetch_array($sqlrow))
        {   
            $chk=1;
            $user_id1 = $queRow['user_id1'];
            $user_id2 = $queRow['user_id2'];
            if($user_id1!=$user_id){
                $sql = "SELECT user_id,user_fname,user_lname,user_status,user_profile_img,user_login_by FROM `user` WHERE user_id = '$user_id1'";
                $res = mysql_query($sql);
                if($queRow = mysql_fetch_array($res))
                {   
                    $user_name = $queRow['user_fname'];
                    $status = $queRow['user_status'];
                    $loginby = $queRow['user_login_by'];
                    if($loginby=='facebook')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else if($loginby=='google')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else if($loginby=='twitter')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else{
                        $image = $queRow['user_profile_img'];
                        if($image==""){
                            $image = "images/profile.png";
                        }
                        $image = $base_url.$image;
                    }
                }
                $arr[]=array('friend_id'=>$user_id1,'user_name'=>$user_name,'image'=>$image);
                $contents['friend'] =$arr;
            }
            else if($user_id2!=$user_id){
                $sql = "SELECT user_id,user_fname,user_lname,user_status,user_profile_img,user_login_by FROM `user` WHERE user_id = '$user_id2'";
                $res = mysql_query($sql);
                if($queRow = mysql_fetch_array($res))
                {   
                    $user_name = $queRow['user_fname'];
                    $status = $queRow['user_status'];
                    $loginby = $queRow['user_login_by'];
                    if($loginby=='facebook')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else if($loginby=='google')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else if($loginby=='twitter')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else{
                        $image = $queRow['user_profile_img'];
                        if($image==""){
                            $image = "images/profile.png";
                        }
                        $image = $base_url.$image;
                    }
                }
                $arr[]=array('friend_id'=>$user_id2,'user_name'=>$user_name,'image'=>$image);
                $contents['friend'] =$arr;
            }
            $datatext["friends"] = $contents;
        }
        if($chk==0){
            $datatext["friends"] ="No";
        }
        echo json_encode($datatext);
    }
    
    
    //All Pending Friends Request
    if($method==13)
    {
        $user_id = $_REQUEST['user_id'];
        $contents = array();
        $datatext = array();
        $sqlrow = mysql_query("SELECT friend_id,user_id1,user_id2 from friends where (user_id1='$user_id' or user_id2='$user_id') and request_status='0' and request_by!='$user_id'");
        while($queRow = mysql_fetch_array($sqlrow)){
            $chk=1;
            $user_id1 = $queRow['user_id1'];
            $user_id2 = $queRow['user_id2'];
            $request_id = $queRow['friend_id'];
            if($user_id1!=$user_id){
                $sql = "SELECT user_id,user_fname,user_lname,user_status,user_profile_img,user_login_by FROM `user` WHERE user_id = '$user_id1'";
                $res = mysql_query($sql);
                if($queRow = mysql_fetch_array($res))
                {   
                    $user_name = $queRow['user_fname'];
                    $status = $queRow['user_status'];
                    $loginby = $queRow['user_login_by'];
                    if($loginby=='facebook')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else if($loginby=='google')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else if($loginby=='twitter')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else{
                        $image = $queRow['user_profile_img'];
                        if($image==""){
                            $image = "images/profile.png";
                        }
                        $image = $base_url.$image;
                    }
                }
                $arr[]=array('friend_id'=>$user_id1,'request_id'=>$request_id,'user_name'=>$user_name,'image'=>$image);
                $contents['friend'] =$arr;
            }
            else if($user_id2!=$user_id){
                $sql = "SELECT user_id,user_fname,user_lname,user_status,user_profile_img,user_login_by FROM `user` WHERE user_id = '$user_id2'";
                $res = mysql_query($sql);
                if($queRow = mysql_fetch_array($res))
                {   
                    $user_name = $queRow['user_fname'];
                    $status = $queRow['user_status'];
                    $loginby = $queRow['user_login_by'];
                    if($loginby=='facebook')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else if($loginby=='google')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else if($loginby=='twitter')
                    {
                        $image = $queRow['user_profile_img'];
                    }
                    else{
                        $image = $queRow['user_profile_img'];
                        if($image==""){
                            $image = "images/profile.png";
                        }
                        $image = $base_url.$image;
                    }
                }
                $arr[]=array('friend_id'=>$user_id2,'request_id'=>$request_id,'user_name'=>$user_name,'image'=>$image);
                $contents['friend'] =$arr;
            }
            $datatext["friends"] = $contents;
        }
        if($chk==0){
            $datatext["friends"] ="No";
        }
        echo json_encode($datatext);
    }
    
    if($method==14)
    {
        $request_id = $_REQUEST['request_id'];
        $sql = mysql_query("update friends set request_status='1' where friend_id='$request_id'");
        if($sql){
            $contents["result"] = "1";
        }
        else{
            $contents["result"] = "0";
        }
        echo json_encode($contents);
    }
?>