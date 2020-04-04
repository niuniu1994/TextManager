<html lang="en">
<head>
    <title>Text manager</title>
    <link rel="stylesheet" href="https://unpkg.com/mustard-ui@latest/dist/css/mustard-ui.min.css">
    <style>
        .highlight {
            background: yellow;
        }

        .xx{
            width: 450px;
            display: inline-block;
            overflow: scroll;
        }

        a{
            position: relative;
            margin: 0 2px;
        }

    </style>
</head>
<body>
<?php
$text = '';
$numElement = array();
$keywords = array();
$allpos = array();
function strpos_all($text, $needle)
{
    $offset = 0;
    $allPos = array();

    while (($offset = strpos($text, $needle, $offset)) !== false) {
        $allPos[] = $offset;
        $offset = $offset + strlen($needle);
    }
    return $allPos;
}


if (isset($_POST['textUrl'])) {
    $text = file_get_contents($_POST['textUrl']);
}

//Find out the position of keywords in the text
if (strlen($text) > 0 && strlen($_POST['searchQuery']) > 0) {
    $keywords = preg_split("/[\s,]+/", $_POST['searchQuery']);
    for ($i = 0; $i < count($keywords); $i++) {

        $allpos[] = strpos_all($text, $keywords[$i]);
        $numElement[] = count($allpos[$i]);

    }
}
?>

<header style="height: 200px;">
    <h1>Text manager</h1>
</header>
<br>
<div class="row">
    <div class="col col-sm-5">
        <div class="panel">
            <div class="panel-body">
                <form action="index.php" method="post">
                    <h2 class="panel-title">1. Get text</h2>
                    <input type="text" placeholder="Enter the poem url" name="textUrl"
                           value="<?php if (isset($_POST[textUrl])) {
                               echo $_POST['textUrl'];
                           } ?>"><br>
                    <button type="submit" name="action" value="fetch" class="button-primary">Fetch text</button>
                    <h2 class="panel-title">2. Find keywords</h2>
                    <input type="text" placeholder="Enter text to be highlighted" name="searchQuery"
                           value="<?php if (isset($_POST[searchQuery])) {
                               echo $_POST['searchQuery'];
                           } ?>"><br>
                    <button type="submit" name="action" value="search" class="button-primary">Search text</button>
                </form>
                <?php
                if (!empty($keywords)) {
                    echo "<h2 class='panel-title'>3. Check results</h2>";
                    echo "<div class='stepper'>";
                    for ($i = 0; $i < count($numElement); $i++) {
                        echo "<div class='step'>";
                        echo "<p class='step-number'>$numElement[$i]</p>";
                        echo "<p class='step-title'><span class='tag tag-blue'>Keyword: </span><i>$keywords[$i]</i></p>";
                        echo "<div class='xx'>";
                        for ($j = 1; $j <= $numElement[$i]; $j++) {
                            if ($j % 10 == 0 ){
                                echo "<div class='xx'>";
                                echo "</div>";
                            }
                            $id = $keywords[$i] . '-' . ($j - 1);
                            echo "<a href=#$id class='button-primary-outlined'>$j</a>";

                        }
                        echo "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>

    <div class="col col-sm-7" style="padding-left: 25px;">
        <pre><code>
           <?php
           //Display text and write text in a file
           if (strlen($text) > 0 && empty($_POST["searchQuery"])) {
               $fp = fopen("text.txt", "w");
               if ($fp) {
                   fwrite($fp, $text);
                   fclose($fp);
               }
               echo $text;
           }


           //Find all keywords in the text and replace them
           if (strlen($text) > 0 && strlen($_POST['searchQuery']) > 0) {
               for ($i = 0; $i < count($keywords); $i++) {
                   $offset = 0;
                   for ($j = 0; $j < count($allpos[$i]); $j++) {
                       $id = $keywords[$i] . '-' . $j;
                       $keyword = "<span id=$id class='highlight'>$keywords[$i]</span>";
                       $len = strlen($keyword) - strlen($keyword[$i]);
                       if ($j == 0) {
                           $text = substr_replace($text, $keyword, $allpos[$i][$j], strlen($keywords[$i]));
                       } else {
                           $text = substr_replace($text, $keyword, $allpos[$i][$j] + $offset, strlen($keywords[$i]));
                       }
                       $offset += ($len - 2);
                   }
               }
               echo $text;
           }
           ?>
        </code></pre>
    </div>
</div>

</body>
</html>