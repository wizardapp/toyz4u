<?php

require 'app/start.php';

$query = 'SELECT id, label, slug from pages';

$pages = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

require VIEW_ROOT . '/home.php';