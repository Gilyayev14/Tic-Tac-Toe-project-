<?php
session_start();

function initializeBoard() {
    for ($row = 1; $row <= 3; $row++) {
        for ($col = 1; $col <= 3; $col++) {
            $position = "$col-$row";
            if (!isset($_SESSION[$position])) {
                $_SESSION[$position] = '';
            }
        }
    }
}

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

initializeBoard();

$winner = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['position']) && !$winner) {
    $position = $_POST['position'];
    if ($_SESSION[$position] === '') {
        $_SESSION[$position] = $_SESSION['turn'];
        $_SESSION['moves'][] = $position; 
        
        $winner = whoIsWinner();
        if ($winner) {
            // End the game and show the winner, clear unchosen cells
            foreach ($_SESSION as $key => $value) {
                if ($value === '') {
                    $_SESSION[$key] = ''; // Clear unchosen cells
                }
            }
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

// Function to check for a winner
function whoIsWinner()
{
    $winningCombinations = [
        ['1-1', '2-1', '3-1'], // top row
        ['1-2', '2-2', '3-2'], // middle row
        ['1-3', '2-3', '3-3'], // bottom row
        ['1-1', '1-2', '1-3'], // left column
        ['2-1', '2-2', '2-3'], // middle column
        ['3-1', '3-2', '3-3'], // right column
        ['1-1', '2-2', '3-3'], // diagonal left to right
        ['3-1', '2-2', '1-3']  // diagonal right to left
    ];

    foreach ($winningCombinations as $combination) {
        $winner = checkWhoHasTheSeries($combination);
        if ($winner != null) return $winner;
    }
    return null; // No winner
}

// Function to check if a series has a winner
function checkWhoHasTheSeries($list)
{
    $XCount = 0;
    $OCount = 0;

    foreach ($list as $value) {
        if ($_SESSION[$value] == 'X') {
            $XCount++;
        } elseif ($_SESSION[$value] == 'O') {
            $OCount++;
        }
    }

    if ($XCount == 3) return 'X';
    if ($OCount == 3) return 'O';
    
    return null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic-Tac-Toe</title>
    <style>
        .board {
            display: grid;
            grid-template-columns: repeat(3, 100px);
            gap: 10px;
            margin-top: 20px;
        }
        button {
            width: 100px;
            height: 100px;
            font-size: 24px;
            background-color: blue; 
            border: none;
        }
        .X {
            background-color: green;
            color: white;
        }
        .O {
            background-color: red;
            color: white;
        }
        button:disabled {
            background-color: ;
        }
    </style>
</head>
<body>
    <h1>Tic-Tac-Toe</h1>
    <form method="post">
        <div class="board">
            <?php
            for ($row = 1; $row <= 3; $row++) {
                for ($col = 1; $col <= 3; $col++) {
                    $position = "$col-$row";
                    $value = $_SESSION[$position]; 
                    
                    // Apply color based on whether it's X or O
                    $class = $value == 'X' ? 'X' : ($value == 'O' ? 'O' : '');

                    // Only show buttons if the game is still on and there is no winner yet
                    if (!$winner || $value !== '') {
                        echo "<button type='submit' name='position' value='$position' class='$class' ".($value !== '' ? 'disabled' : '').">$value</button>";
                    } else {
                        echo "<button disabled style='background-color: white;'>$value</button>";
                    }
                }
            }
            ?>
        </div>
        <br>
        <button type="submit" name="reset">Reset Game</button>
    </form>
</body>
</html>
