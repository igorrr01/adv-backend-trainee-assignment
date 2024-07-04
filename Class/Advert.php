<?php

class  Advert{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
        $json = new Json();
        $this->json = $json;
    }

    public function showAd(){

        $id = (int) $_GET['id'];
        $stmt = $this->conn->prepare('SELECT id,title,text,created_at,price,main_photo,additional_photos FROM adverts WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row){
            $response = [
                "id" => $row['id'],
                "title" => $row['title'],
                "text" => $row['text'],
                "price" => $row['price'],
                "main_photo" => $row['main_photo'],
                "additiona_photos" =>$row['additional_photos'],
                "created_at" => $row['created_at']
            ];

            $jsonResponse = [
                "status" => true,
                "status_code" => 200,
                "advert" => $response,
            ];
        }else{
            $jsonResponse = [
                "status" => false,
                "status_code" => 404,
                "message" => "Обьявление не найдено!",
            ];
        }

        $this->json->sendJson($jsonResponse);
    }

    public function showAllAds()
    {
        // Пагинация, выводим 10 обьявлений с БД

        $limit = 10;
        $offset = Helper::paginate($limit);
        $sortTypes = ['price','created_at'];
        $orderTypes = ['desc','asc'];
        $order = $_GET['order'] ?? NULL;

        // Сортировка
        if(isset($_GET['sort']) && in_array($_GET['sort'],$sortTypes)){
            $orderBy = 'ORDER BY `' . htmlspecialchars($_GET['sort'] .'` ');
        }
        if(isset($order) && in_array($_GET['order'],$orderTypes)){
            $orderBy .= mb_strtoupper($order);
        }
        $orderBy = $orderBy ?? NULL;

        $stmt = $this->conn->prepare("SELECT id, title, text, created_at, price, main_photo, additional_photos FROM adverts $orderBy LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset['offset'], PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Рассчитываем количество записей
        $countStmt = $this->conn->prepare('SELECT COUNT(*) FROM adverts');
        $countStmt->execute();
        $totalRows = $countStmt->fetchColumn();

        // Рассчитываем количество страниц
        $totalPages = ceil($totalRows / $limit);

        if ($rows) {
            $jsonResponse = [
                "status" => true,
                "status_code" => 200,
                "advert" => $rows,
                "pagination" => [
                    "current_page" => $offset['page'],
                    "total_pages" => $totalPages,
                    "total_records" => $totalRows
                ]
            ];
        } else {
            $jsonResponse = [
                "status" => false,
                "status_code" => 404,
                "message" => "Объявлений не найдено!",
            ];
        }

        $this->json->sendJson($jsonResponse);
    }

    public function storeAdd()
    {

        // применяем функцию htmlspecialchars ко всем элементам в массиве $_POST
        $newpost = array_map ('htmlspecialchars',$_POST);

        $title = $newpost['title'] ?? NULL;
        $text = $newpost['text'] ?? NULL;
        $price = $newpost['price'] ?? NULL;
        $main_photo = $newpost['main_photo'] ?? NULL;
        $additional_photos = $newpost['additional_photos'] ?? NULL;

        if((is_null($text) || is_null($title) || mb_strlen($title) < 4) || (mb_strlen($title) > 200)) {
            $jsonResponse = [
                "status" => false,
                "status_code" => 422,
                "message" => "Заголовок должен иметь длину от 4 до 200 символов",
            ];
        }

        if((is_null($text) || is_null($title) || mb_strlen($text) < 10) || (mb_strlen($text) > 1000)) {
            $jsonResponse = [
                "status" => false,
                "status_code" => 422,
                "message" => "Описание должно иметь длину от 10 до 1000 символов",
            ];
        }

        // Считаем количество дополнительных фото, ссылки на фото разделены точкой с запятой - ";"
        if(!is_null($additional_photos) && $additional_photos !== "") {
            $additional_photos_count = explode(";", $additional_photos);
            if(count($additional_photos_count) > 3){
                $jsonResponse = [
                    "status" => false,
                    "status_code" => 422,
                    "message" => "Дополнительных фото должно быть не больше 3",
                ];
            }
        }

        if(!isset($jsonResponse)) {
            $query = "INSERT INTO adverts SET title = :title, text = :text, price = :price, created_at = :created_at, main_photo = :main_photo, additional_photos = :additional_photos";
            $stmt = $this->conn->prepare($query);
            $created_at = date("Y-m-d H:i:s");
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":text", $text);
            $stmt->bindParam(":price", $price);
            $stmt->bindParam(":created_at", $created_at);
            $stmt->bindParam(":main_photo", $main_photo);
            $stmt->bindParam(":additional_photos", $additional_photos);
            $stmt->execute();

            $jsonResponse = [
                "status" => false,
                "status_code" => 201,
                "message" => "Пользователь успешно создан",
            ];
        }

        $this->json->sendJson($jsonResponse);
    }
}