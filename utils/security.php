<?php
function pass_hash($pass){
    return hash('sha256', $pass);
}
?>