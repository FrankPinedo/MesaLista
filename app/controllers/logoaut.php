<?php
session_start();
session_unset();
session_destroy();//destruir la sesión
header('Location: ' . BASE_URL . '/login');
exit;
