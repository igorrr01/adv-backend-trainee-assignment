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
        if(in_array($_GET['sort'],$sortTypes)){
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
}