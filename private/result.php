<?php
function postedAction($action) {
    return isset($_POST["action"]) && $_POST["action"] === $action;
}

class Result {
    public $is_error;
    public $message;
    public $note;

    function __construct($is_error, $message)
    {
        $this->is_error = $is_error;
        $this->message = $message;
        $this->note = "";
    }

    function setNote($note) {
        $this->note = $note;
    }

    function display() {
        $fullMessage = $this->message;

        if($this->note !== "") {
            $fullMessage .= "<br>$this->note";
        }

        if($this->is_error) { ?>
            <div class="container">
                <div class="result-text">
                    <span class="error">
                        <?=$fullMessage?>
                    </span>
                </div>
            </div> <?php
        } else { ?>
            <div class="container">
                <div class="result-text">
                    <span class="success">
                        <?=$fullMessage?>
                    </span>
                </div>
            </div> <?php
        }
    }
}

$postResult = new Result(false, "");
function showPostResult() {
    global $postResult;
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $postResult->display();
    }
}

function successResult($message) {
    global $postResult;
    $postResult->is_error = false;
    $postResult->message = $message;
}

function errorResult($message) {
    global $postResult;
    $postResult->is_error = true;
    $postResult->message = $message;
}

function noteResult($note) {
    global $postResult;
    $postResult->setNote($note);
}
?>
