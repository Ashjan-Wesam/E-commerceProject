<?php 

    require '../config2.php';
    class SQLHelper {

        private $conn;

        public function __construct() {
            $db = new Database();
            $this->conn = $db->getConnection();
        }

        public function getUser($email) {
            $get = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
            $get->execute([$email]);

            return $get->fetch(PDO::FETCH_ASSOC);

        }


        public function getData($table, $id = "") {
            $stmt = "SELECT * FROM $table";

            if($id != "") {
                $stmt .= " WHERE id = ?";
                $data = $this->conn->prepare($stmt);
                $data->execute([$id]);
            } else {
                $data = $this->conn->prepare($stmt);
                $data->execute();
            }

            return $data->fetchAll(PDO::FETCH_ASSOC);
        }

        public function getReviews($search = "") {
            $sql = "SELECT r.id, p.name AS  product_name, r.comment, r.rating, u.name, u.email FROM reviews r 
                                            INNER JOIN products p ON p.id = r.product_id
                                            INNER JOIN users u ON r.user_id = u.id";

            if($search != "") {
                $sql .=  " WHERE p.name LIKE ?";
                $reviews = $this->conn->prepare($sql);
                $search = "%{$search}%";
                $reviews->execute([$search]);
            }
            
            else {
                $reviews = $this->conn->prepare($sql);
                $reviews->execute();
            }
            

            return $reviews->fetchAll(PDO::FETCH_ASSOC);
        }


        public function deleteRecord($table, $id) {
            $stmt = "DELETE FROM {$table} WHERE id = ?";

            $delete = $this->conn->prepare($stmt);
            $delete->execute([$id]);
            return;
        }

        public function updateCategory($id, $name, $dec) {
            $update =$this->conn->prepare("UPDATE categories SET name = ?, description = ? , updated_at = ? WHERE id = ?");
            $update->execute([$name, $dec, date('Y-m-d H:i:s'), $id]);
            return;
        }

        public function updateStatus($id, $status) {
            $update = $this->conn->prepare("UPDATE orders SET status = ?, updated_at = ? WHERE id =?");
            $update->execute([$status, date(format: 'Y-m-d H:i:s'), $id]);
            return;
        }

        public function insertCategory($name, $dec) {
            $insert = $this->conn->prepare("INSERT INTO categories (name, description, created_at, updated_at) VALUES (? , ? , NOW(), NOW())");
            $insert->execute([$name, $dec]);
            return;
        }

        // moh
        public function getAllSubscriptions() {
            $stmt = $this->conn->prepare("SELECT u.name AS user_name, s.*  FROM subscriptions s
                                          JOIN users u ON s.user_id = u.id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function updateSubscriptionStatus( $status , $subscription_id) {
            $stmt = $this->conn->prepare("UPDATE subscriptions SET status = ? WHERE id = ?");
            $stmt->execute([$status, $subscription_id]);
            return;
        }

        // ham
        public function getTotalIncome() {
            $sql = "SELECT SUM(total_amount) as total_income FROM orders WHERE status = 'completed'";
            $stmt = $this->conn->prepare($sql);
            // return $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_income'];
        }

        public function getTotalCategories() {
            $sql = "SELECT COUNT(*) as total_categories FROM categories";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_categories'];
        }

        public function getTotalOrders() {
            $sql = "SELECT COUNT(*) as total_orders FROM orders";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_orders'];
        }

        public function getTotalCustomers() {
            $sql = "SELECT COUNT(*) as total_customers FROM users";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total_customers'];
        }

        public function getDailyRevenue() {
            $query = "
                SELECT 
                    DATE(order_date) AS order_day, 
                    COUNT(id) AS total_orders, 
                    SUM(total_amount) AS total_revenue
                FROM 
                    orders
                WHERE 
                    status = 'completed'
                GROUP BY 
                    DATE(order_date)
                ORDER BY 
                    order_day DESC
            ";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
          }

          public function updateUser($name, $email, $phone, $address , $id){
            $update = $this->conn->prepare("UPDATE users SET name = ?, email = ? , phone = ?, address = ? , updated_at = now() WHERE id = ?");
            $update->execute([$name, $email, $phone, $address, $id]);
            return;
          }
    }
?>