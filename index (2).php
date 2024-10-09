<?php
session_start();


if (!isset($_SESSION['turn'])) {
    $_SESSION['turn'] = 'X'; 
}
if (!isset($_SESSION['moves'])) {
    $_SESSION['moves'] = []; 
}

if (isset($_POST['reset'])) {
    session_destroy();
    header("Location: index.php"); 
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $position = $_POST['position'];
    if (!isset($_SESSION[$position])) {
        $_SESSION[$position] = $_SESSION['turn'];
        $_SESSION['moves'][] = $position; 

        
        $winner = whoIsWinner();
        if ($winner) {
            echo "<script>alert('Player $winner wins!');</script>";
            session_destroy(); 
            header("Refresh:0");
            exit;
        } elseif (count($_SESSION['moves']) == 9) {
            echo "<script>alert('It\'s a draw!');</script>";
            session_destroy(); 
            header("Refresh:0"); 
            exit;
        }

        $_SESSION['turn'] = ($_SESSION['turn'] == 'X') ? 'O' : 'X';
    }
}
function whoIsWinner()
{
    // 1 of 8: top row
    $winner = checkWhoHasTheSeries(['1-1', '2-1', '3-1']);
    if ($winner != null) return $winner;
    // 2 of 8: middle row
    $winner = checkWhoHasTheSeries(['1-2', '2-2', '3-2']);
    if ($winner != null) return $winner;
    // 3 of 8: bottom row
    $winner = checkWhoHasTheSeries(['1-3', '2-3', '3-3']);
    if ($winner != null) return $winner;
    // 4 of 8: left column
    $winner = checkWhoHasTheSeries(['1-1', '1-2', '1-3']);
    if ($winner != null) return $winner;
    // 5 of 8: middle column
    $winner = checkWhoHasTheSeries(['2-1', '2-2', '2-3']);
    if ($winner != null) return $winner;
    // 6 of 8: right column
    //$winner = checkWhoHasTheSeries(['3-1', '3-2', '3-3']);
   // if ($winner != null) return $winner;
    // 7 of 8: diagonal left to right
    $winner = checkWhoHasTheSeries(['1-1', '2-2', '3-3']);
    if ($winner != null) return $winner;
    // 8 of 8: diagonal right to left
    $winner = checkWhoHasTheSeries(['3-1', '2-2', '1-3']);
    if ($winner != null) return $winner;
    return null; // It's a draw
}

function checkWhoHasTheSeries($list)
{
    $XCount = 0;
    $OCount = 0;
    foreach ($list as $value) {
        if (isset($_SESSION[$value]) && $_SESSION[$value] == 'X') {
            $XCount++;
        } elseif (isset($_SESSION[$value]) && $_SESSION[$value] == 'O') {
            $OCount++;
        }
    }
    if ($XCount == 3)
        return 'X';
    elseif ($OCount == 3)
        return 'O';
    else
        return null;
}
?>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="tic-tac-toe-style-guide.css"> 
    <title>Tic-Tac-Toe</title>
</head>
<body>
    <h1>Tic-Tac-Toe</h1>
    <form method="post">
        <div style="display: grid; grid-template-columns: repeat(3, 100px); gap: 10px;">
            <?php
            for ($row = 1; $row <= 3; $row++) {
                for ($col = 1; $col <= 3; $col++) {
                    $position = "$col-$row";
                    $value = isset($_SESSION[$position]) ? $_SESSION[$position] : '';
                    echo "<button type='submit' name='position' value='$position' style='width: 100px; height: 100px; font-size: 24px;'>$value</button>";
                }
            }
        
            ?>
        </div>
        <br>
        <button type="submit" name="reset">Reset Game</button>
    </form>
</body>
</html>