<?php

class Helper
{
    public static function paginate($count)
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if($page < 1){$page = 1;}
        $limit = $count;
        $offset = ($page - 1) * $limit;
        $result = [
            'page' => $page,
            'offset' => $offset];
        return $result;
    }

}