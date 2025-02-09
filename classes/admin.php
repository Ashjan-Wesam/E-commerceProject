<?php 

    require "../classes/SQLHelper.php";

    class Admin {
        private $sql;

        public function __construct() {
            $this->sql = new SQLHelper();
        }

        public function getCategories() {

            return $this->sql->getData("categories");
        }
        
        public function getOrders() {
            return $this->sql->getData( "orders");
        }

        public function getUsers() {
            return $this->sql->getData("users");
        }

        public function getReviews($search) {
            return $this->sql->getReviews($search);
        }

        public function delteRecord($table, $id) {
            $this->sql->deleteRecord( $table, $id);

            header("Location:". $table . ".php");

        }

        public function updateCategory( $id, $name, $dec) {
            if(empty($name) || empty($dec)) {
                return false;
            }
            $this->sql->updateCategory($id, $name, $dec);
            header("Location: categories.php");
            exit();
        }

        public function updateStatus($id, $status) {
            $this->sql->updateStatus($id, $status);
            header("Location: orders.php");
            exit();
        }

        public function insertCategory($name, $dec) {

            if(empty($name) || empty($dec)) {
                return false;
            }

            $this->sql->insertCategory($name, $dec);
            header("Location: categories.php");
            exit();
        }

        public function getSub() {
           return $this->sql->getAllSubscriptions();
        }

        public function updateSub($id, $status) {

            $this->sql->updateSubscriptionStatus( $status, $id);
            header("Location: subscriptions.php ");
            exit();
        }

        public function getTotalIncome() {
            return $this->sql->getTotalIncome();
        }

        public function getTotalCategories() {
            return $this->sql->getTotalCategories();
        }

        public function getTotalOrders() {
            return $this->sql->getTotalOrders();
        }

        public function getTotalCustomers() {
            return $this->sql->getTotalCustomers();
        }

        public function getDailyRevenue() {
            return $this->sql->getDailyRevenue();
        }

        public function updateUser($name, $email, $phone, $address , $id) {
            if(empty($name) || empty($email) || empty($phone) || empty("address")){
                return false;
            }

            $this->sql->updateUser($name, $email, $phone, $address , $id);
            header("Location: users.php");
            exit();
        }

}
?>