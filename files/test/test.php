<html lang="ko">
    <head>
        <meta charset="UTF-8">
        <title>끝말잇기</title>
        <style>
            body{
                background-color: skyblue;
                border: 10px solid blue;
                margin: 0;
            }
            form{
                text-align: center;
            }
            input[type="text"],input[type="submit"] 
            {
                font-size: 30px;
                padding: 20px;
                margin: 20px;
                border: 0;
            }
            input[type="submit"] {
                background-color: lightblue;
            }
            input[type="submit"]:hover 
            {
                color: white;
                background-color: blue;
            }
        </style>
</head>
<body>
    <br><br><br><br><br><br><br>
    <form method="GET" accept-charset="UTF-8">
        <input type="submit" name="reset"value="NEW START"><br>
        <input type="text" name="name"><br>
        <input type="submit" value="ENTER">
        
    </form>

</body>
</html>
<?php
    session_start();
    if (!isset($_SESSION["countingg"])) 
    {
        $_SESSION["countingg"] = 5;
    }
    if (isset($_GET['reset'])) 
    {
        session_unset();
        echo "<center><h1>끝말잇기 게임 시작합니다</h1></center><br>";
        $_SESSION["countingg"] = 5;
        echo "<center><h1>기회가 ". $_SESSION["countingg"]. "번 남았습니다</h1></center>";
    }
    else if($_GET["name"] == "")
    {
        echo "<center><h1>단어를 입력해주세요</h1></center>";
    }
    else
    {
        $now = iconv_substr($_GET["name"], iconv_strlen($_GET["name"], "UTF-8") - 1, 1, "UTF-8");
        $now2 = iconv_substr($_GET["name"], 0, 1, "UTF-8");
        if(!isset($_SESSION["before"]))
        {
            echo "<center><h1>입력받은단어 :". $_GET["name"]. "</h1></center><br>";
            echo "<center><h1>".$now." 으로 시작하는 단어를 넣어주세요</h1></center>";
            echo "<center><h1>기회가 ". $_SESSION["countingg"]. "번 남았습니다</h1></center>";
            $_SESSION['before'] = $_GET["name"];
        }
        else
        {
            $end = iconv_substr($_SESSION['before'], iconv_strlen($_SESSION['before'], "UTF-8") - 1, 1, "UTF-8");
            if($end == $now2)
            {
                echo "<center><h1>입력받은단어 :". $_GET["name"]. "</h1></center><br>";
                echo "<center><h1>".$now."으로 시작하는 단어를 넣어주세요</h1></center><br>";
                echo "<center><h1>기회가 ". $_SESSION["countingg"]. "번 남았습니다</h1></center>";
                $_SESSION['before'] = $_GET["name"];
            }
            else
            {
                $_SESSION["countingg"]--;
                if ($_SESSION["countingg"] <= 0) 
                {
                    echo "<center><h1>GAME OVER<br></h1></center>";
                    session_unset();
                    echo "<center><h1>끝말잇기 게임을 다시 시작합니다</h1></center><br>";
                    $_SESSION["countingg"] = 5;
                    echo "<center><h1>기회가 ". $_SESSION["countingg"]. "번 남았습니다</h1></center>";
                }
                else
                {
                    echo "<center><h1>시작단어 : ".$end."</h1></center>";
                    echo "<center><h1><br>틀렸습니다 다시입력해주세요</h1></center><br>";
                    echo "<center><h1>기회가 ". $_SESSION["countingg"]. "번 남았습니다</h1></center>";
                }
            }
        }
    }
?>