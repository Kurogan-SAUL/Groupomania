
<?php include 'header.php'; 

    
      $message_obj = new Message($con, $userLoggedIn);
      if(isset($_GET['u']))
      $user_to = $_GET['u'];
      else {
          $user_to = $message_obj->getMostRecentUser();
          if ($user_to == false)
          $user_to = 'new';
        }
        
        if($user_to != "new")
            $user_to_obj = new User($con, $user_to);

    //obtenir des données sur l'utilisateur
    $get_uset_to_data = mysqli_query($con, "select * from users where username='$user_to'");
    $user_to_info = mysqli_fetch_array($get_uset_to_data);

    if(isset($_POST['submit_msg'])){
        if(isset($_POST['msg_body'])){
            $body = $_POST['msg_body'];
            $body = mysqli_real_escape_string($con, $body);
            $date = date("Y-m-d H:i:s");
            $message_obj->sendMessage($user_to, $body, $date);
        }
    }

    if(isset($_POST['search_btn'])){
        $msg="";
        $user = $_POST['search'];
        $query = mysqli_query($con, "select * from users where username='$user'");
        if($query){
            header("Location: messages.php?u=$user");
        }
        else {
            $msg = "No User Found";
        }
    }
        
?>

<style>
    .msg_main{
        width: 600px;
        height: 475px;
        position: fixed;
        background: #4E5166;
        margin-top: 95px;
        margin-bottom: 150px;
        margin-left: 515px;
        margin-right: auto;
        border-radius: 5px;
        padding-top: 1px;
        padding-bottom: 30px;
        padding-left: 20px;
        padding-right: 20px;
    }

    .old_chats{
        width: 375px;
        height: 475px;
        position: fixed;
        background: #4E5166;        
        margin-top: 95px;
        margin-bottom: 150px;
        margin-left: 50px;
        margin-right: auto;
        border-radius: 5px;
        padding-top: 1px;
        padding-bottom: 30px;
        padding-left: 20px;
        padding-right: 20px;
    }

    .name{
        margin-top: auto;
        margin-bottom: auto;
        margin-left: 10px;
    }

    hr{
        width: 95%;
        background: rgb(73, 199, 238);
        border: none;
        height: 2px;
        margin-left: 10px;
        margin-bottom: 20px;
    }

    #msg_area{
        width: 70%;
        height: 37px;
        margin-right: 10px;
        margin-left: 5px;
        border-radius: 7px;
        border: 2px solid #D3D3D3;
        font-size: 16px;
        font-family: 'roboto';
        padding: 5px;
    }

    input[type="submit"]{
        padding: 5px 30px 5px 30px;
        height: 50px;
        background: #0090ff;
        color: white;
        border: none;
        border-radius: 7px;
        margin-top: auto;
        margin-bottom: auto;
        position: absolute;
    }

    .msg{
        border: 1px solid #000;
        border-radius: 5px;
        padding: 5px 10px;
        display: inline-block;
        color: #fff;
    }

    .msg#blue{
        background: #3498bd;
        border-color: #3498bd;
        float: right;
        margin-right: 15px;
    }

    .msg#green{
        background: #73d640;
        border-color: #73d640;
        float: left;   
    }

    .load_msgs{
        height: 65%;
        overflow-y: scroll;
        margin-bottom: 20px
    }

    .headding, .find_user{
        margin-top: 15px;
        margin-bottom: 15px;
        font-size: 20px;
        color: #ffffff;
        border: 1px solid #27b4ea;
        padding: 5px;
        border-radius: 5px;
        background: #27b4ea;
    }

    a{
        text-decoration-line: none;
    }

    .chat_name{
        margin-left: 60px;
        margin-top: -40px;
    }

    .other{
        margin-left: 60px;
        margin-top: -17px;
        color: #d3d3d3;
    }

    .time_sml{
        font-size: 12px;
        margin-left: 148px;
        color: #d3d3d3;
    }

    .chat_p{
        margin-top: 0px;
    }

    .chats{
        overflow-y: scroll;
    }


</style>

    <div class="msg_main">
        <div class="msg_heading" >
            <div class="heading_wreper" style="margin-top: 15px; display: flex;">
                <?php 
                    if($user_to != "new"){
                        echo "<span ><img style='height: 40px;margin-bottom: 3px;border-radius: 50%;' src='".$user_to_info['profile_pic']."' style='margin-bottom: 3px;'></span>";
                        echo "<span class='name'><a href='$user_to'>".$user_to_obj->getFnameAndLname()."</a></span></h5><br>";
                    }
                    else {
                        echo "Nouveau message";
                    }
                ?>
            </div>
        </div>
        <hr>
            <?php
                echo "<div class='load_msgs' id='scroll_msg'>";
                    echo $message_obj->getMessages($user_to);
                echo "</div>";
            ?>
        <hr>
        <div class="send_msg">
            <div class="msg_wreper">
                <form action="" method="post">
                    <?php 
                        if ($user_to == "new") {
                            echo "Chercher un ami pour debuter une conversation<br><br>";
                            echo "A : <input type='text' id='msg_area' name='search' placeholder='Qui cherchez-vous?'>";
                            echo "<input type='submit' name='search_btn' value='Chercher'>";
                            echo "<lable value='search_lbl'>";
                        }
                        else {
                            echo "<textarea name='msg_body' id='msg_area' placeholder='Ecrivez votre message...'></textarea>";
                            echo "<input type='submit' name='submit_msg' value='Envoyer'>";
                        }
                    ?>
                </form>
            </div>
        </div>
    </div>

    <script>//prioriser le chargement des messages depuis le haut
        var div = document.getElementById("scroll_msg");
        div.scrollTop = div.scrollHeight;
    </script>
    
    <div class="old_chats">
        <div class="chat_wreper">
            <div class="headding">
                <span class="head"><b><center>Ancienes discussions</center></b></span>
            </div>
            <div class="chats">
                <?php echo $message_obj->getOtherChats(); ?>
            </div>
            <div class="find_user"><center><a style="color:white;" href="messages.php?u=new"> Chercher quelqu'un </a></center></div>            
        </div>
    </div>
