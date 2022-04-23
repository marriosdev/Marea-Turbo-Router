<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exception</title>
    <style>
        *{
            padding:0;
            margin: 0;
            font-family: Gill Sans Extrabold, sans-serif
        }
        header {
            padding: 20px 10px 20px 20px;
            background-color: #1ea4f6;
            color: white;
        }
        .main{
            margin-top: 30px;
            margin-left: 50px;
            margin-right: 50px;
        }

        #errorTitle{
            background: grey;
            color: white;
            padding: 20px 10px 20px 20px;
            font-size: 16pt;
        }
        .containerMsg{

            margin-bottom: 10px;
            padding: 5px;
        }
        #content{
            padding:  20px 10px 20px 20px;
            border: 1px solid grey;
            border-top: none;
        }
        .nameClassError{
            color: grey;
        }
    </style>
</head>
<body>
    <header>
        <h2><strong>Exception</strong></h2>
    </header>

    <div class="main">
        <div id="errorTitle">
            <p><strong>Message: </strong><?php echo $_SESSION["exception"]->getMessage() ?></p>
        </div>
        <div id="content">
            <?php 
                foreach($_SESSION["exception"]->getTrace() as $trace)
                {
                    echo "<div class='containerMsg'>";
                    echo "<div class='msgSpan'> <strong>At</strong> {$trace['function']}() <span class='nameClassError'>\\{$trace['class']}()</span> <a href='vscode://{$trace['file']}'>Editar</a></div>";
                    echo "<div class='msgSpan'><strong> In file </strong>{$trace['file']} <strong>Line</strong> {$trace['line']} </div>";
                    echo "</div>";
                    echo "<hr>";
                }
            ?>
        </div>
    </div>
</body>
</html>