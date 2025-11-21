<?php

class Task {
    private $conn;
    private $table_name = "tasks";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create
    public function create($user_id, $title, $description, $due_date, $status = 'To Do') {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, title, description, due_date, status) 
                  VALUES (:user_id, :title, :description, :due_date, :status)";
        $stmt = $this->conn->prepare($query);

        $title = htmlspecialchars(strip_tags($title));
        $description = htmlspecialchars(strip_tags($description));
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":due_date", $due_date);
        $stmt->bindParam(":status", $status);

        return $stmt->execute();
    }

    // Get All (With Filter)
    public function getAllByUser($user_id, $filter = 'all') {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";

        if ($filter == 'pending') {
            $query .= " AND status != 'Complete'";
        } elseif ($filter == 'complete') {
            $query .= " AND status = 'Complete'";
        }

        $query .= " ORDER BY due_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt;
    }

    // NEW: Get Single Task for Editing (This was missing!)
    public function getSingleTask($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // NEW: Update Task Details (This was missing!)
    public function update($id, $title, $description, $due_date) {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, description = :description, due_date = :due_date 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $title = htmlspecialchars(strip_tags($title));
        $description = htmlspecialchars(strip_tags($description));

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":due_date", $due_date);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    // Update Status
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // Delete
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>