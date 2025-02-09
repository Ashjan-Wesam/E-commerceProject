<?php
include_once 'config/Database.php';

class Product {

    private $conn;
    private $table_name = 'products';

    public $id;
    public $name;
    public $description;
    public $price;
    public $image;
    public $category_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($category_id = null, $min_price = 0, $max_price = 500) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE price BETWEEN :min_price AND :max_price";
        if ($category_id) {
            $query .= " AND category_id = :category_id";
        }
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':min_price', $min_price);
        $stmt->bindParam(':max_price', $max_price);

        if ($category_id) {
            $stmt->bindParam(':category_id', $category_id);
        }

        $stmt->execute();
        return $stmt;
    }

    public function countProductsByCategory($category_id) {
        $query = "SELECT COUNT(*) FROM products WHERE category_id = :category_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function readFiltered($category_id = '', $min_price = 0, $max_price = 500, $search_query = '', $sort_order = 'asc') {
        $query = "SELECT p.*, c.name AS category_name FROM products p
                  JOIN categories c ON p.category_id = c.id
                  WHERE p.price BETWEEN :min_price AND :max_price";
    
        if (!empty($category_id)) {
            $query .= " AND p.category_id = :category_id";
        }
    
        if (!empty($search_query)) {
            $query .= " AND (p.name LIKE :search_query OR p.description LIKE :search_query OR c.name LIKE :search_query)";
        }
    
        $query .= " ORDER BY p.price " . ($sort_order == 'asc' ? 'ASC' : 'DESC');
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':min_price', $min_price);
        $stmt->bindParam(':max_price', $max_price);
    
        if (!empty($category_id)) {
            $stmt->bindParam(':category_id', $category_id);
        }
    
        if (!empty($search_query)) {
            $search_query = "%$search_query%";
            $stmt->bindParam(':search_query', $search_query);
        }
    
        $stmt->execute();
        return $stmt;
    }
    
    
    
}
?>
