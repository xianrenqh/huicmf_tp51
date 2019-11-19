<?php
if(!file_exists(dirname(__FILE__).'/install.lock'))
{
    header('Location:/install/index.php');
    exit();
}
?>