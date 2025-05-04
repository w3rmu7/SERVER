
<html>
<head>
    <title>baseball</title>
</head>
    <style>
        .outro{
            border: 30px solid green;
            width: 100%;
            height: 100%;
            background-color: lightgoldenrodyellow;
        }
        form{
                text-align: center;
            }
            input[type="text"],input[type="submit"] 
            {
                font-size: 40px;
                padding: 20px;
                margin: 20px;
                color: green;
            }
            input[type="submit"]:hover 
            {
                background-color: green;
                color: white;
            }
    </style>
<body>
    <table class = "outro">
        <tr >
            <td colspan = '2' width = "1000px" height = "300px">
            <?php
    session_start();
    if (isset($_GET['reset'])) 
    {
        echo "<center><h1>숫자 야구 게임을 시작합니다.<br>서로 다른 숫자 3개를 입력후 입력 버튼을 눌러주세요<br></h1></center>";
        session_unset();
    }
    else
    {
        if(!isset($_SESSION["map"]))
        {
            $_SESSION["map"] = [[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]];
        }
        if(!isset($_SESSION["coun"]))
        {
            $_SESSION["coun"] = 0;
        }
        if(!isset($_SESSION["ans"]))
        {
            $cha = rand(0,9);
            $answer[0] = $cha;
            $cha = rand(0,9);
            while($cha == $answer[0])
            {
                $cha = rand(0,9);
            }
            $answer[1] = $cha;
            $cha = rand(0,9);
            while($cha == $answer[0] || $cha == $answer[1])
            {
                $cha = rand(0,9);
            }
            $answer[2] = $cha;
            $_SESSION["ans"] = $answer;
        }

        if(isset($_GET["name"])&& $_SESSION["coun"] < 10)
        {
            
            $now = str_split($_GET["name"]);
            $strike = 0;
            $ball = 0;
            $chacck = [0,0,0,0,0,0,0,0,0,0];
            for($i = 0; $i < 3; $i++)
            {
                if($now[$i] == $_SESSION["ans"][$i])
                {
                        $strike++;
                        $chacck[$now[$i]] = 1;
                }
            }
            for($i = 0; $i < 3; $i++)
            {
                for($n = 0; $n < 3; $n++)
                {
                    if($now[$i] == $_SESSION["ans"][$n] && $i != $n)
                    {
                        if($chacck[$now[$i]] == 0)
                        {
                            $ball++;
                            $chacck[$now[$i]] = 1;
                        }
                    }
                }
            }
            $outt = 3-$strike-$ball;
            $_SESSION["map"][$_SESSION["coun"]][0] = $_GET["name"];
            $_SESSION["map"][$_SESSION["coun"]][1] = $strike. " Strike ". $ball. " Ball " ;
            if($_GET["name"] != " ")
            {
                $_SESSION["coun"]++;
            }
            if($strike == 3)
            {
                echo "<center><h1>$strike Strike <br> correct answer!!!<br></h1></center>";
                echo "<center><h1>새로운 숫자 야구 게임을 시작합니다.<br>서로 다른 숫자 3개를 입력후 입력 버튼을 눌러주세요<br></h1></center>";
                session_unset();
            }
            else if($_SESSION["coun"] >= 10)
            {
                echo "<center><h1>GAME OVER<br></h1></center>";
                echo "<center><h1>정답 : ".$_SESSION["ans"][0]. $_SESSION["ans"][1]. $_SESSION["ans"][2]."<br></h1></center>";
                echo "<center><h1>새로운 숫자 야구 게임을 시작합니다.<br></h1></center>";
                session_unset();
            }
            else if($strike == 0 && $ball == 0)
            {
                echo "<center><h1>OUT<br>서로 다른 숫자 3개를 입력해 주세요<br></h1></center>";
            }
            else
            {
                echo "<center><h1>$strike Strike  $ball Ball  $outt Out<br>서로 다른 숫자 3개를 입력해 주세요<br></h1></center>";
            }
        }
           
    }
?>

            </td>
        </tr>
        <tr class = "outro">
            <td width = 500px height = 500px>
                <form method="GET">
                    <input type="submit" name="reset"value="새로 시작"><br>
                    <input type="text" name="name"><br>
                    <input type="submit" value="입력">
                </form>
            </td>
            <td width = 500px height = 500px>
                <?php
                if (isset($_SESSION["map"]) && isset($_GET["name"]))
                {
                    echo $_SESSION['ans'][0];
                    echo $_SESSION['ans'][1];
                    echo $_SESSION['ans'][2];
                    echo "<center><h2>지금까지 결과</h2></center>";
                    echo "<center><table bgcolor = 'green' style='color: white;' width = '400px'>";
                    for($i = 0; $i < $_SESSION["coun"]; $i++)
                    {
                        echo "<tr><td width = '400px' height = '40px' style = 'font-size: 25px'>".$_SESSION["map"][$i][0] ."</td><td width = '400px' style = 'font-size: 25px'>". $_SESSION["map"][$i][1]. "</td></tr>";
                    }
                    echo "</table></center>";
                    echo "<center style = 'font-size: 20px'>".(10-$_SESSION["coun"]) ."번의 기회가 남았습니다</center><br>";
                }
                ?>
            </td>
        </tr>
    </table>
    
</body>
</html>

