<?php
class Disaster {
    private $conn;
    private $table_name = "disasters";

    public $id;
    public $name;
    public $description;
    public $date;
    public $location;

    public function __construct($db) {
        $this->conn = $db;
    }

    function read() {
        $query = "SELECT id, name, description, date, location FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, description=:description, date=:date, location=:location";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->location = htmlspecialchars(strip_tags($this->location));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":location", $this->location);

        if($stmt->execute()) {
            return true;
        }

        return false;
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description, date = :date, location = :location WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->date = htmlspecialchars(strip_tags($this->date));
        $this->location = htmlspecialchars(strip_tags($this->location));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':date', $this->date);
        $stmt->bindParam(':location', $this->location);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>
