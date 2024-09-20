<?php

function guest(): ?bool
{
    $is_authenticated = !empty($_SESSION['user']);

    if ($is_authenticated) {
        $_SESSION['errors'] = [
            'Logout, first!'
        ];
        header('Location: index.php');
        exit;
    }

    return true;
}