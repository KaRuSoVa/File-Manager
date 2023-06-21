<?php
    
include "database.php";

// Arama parametresini al
if (isset($_POST["search"])) {
  $searchValue = $_POST["search"];
} else {
  $searchValue = "";
}

// user_id ile eşleşen belgeleri getir
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
  $sql = "SELECT folderPath FROM documents WHERE user_id = ?";
  if ($searchValue !== "") {
    $sql .= " AND folderPath LIKE '%" . $searchValue . "%'";
  }
  $sql .= " ORDER BY folderPath ASC";

  if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // her bir belge için bir link oluştur
    while ($row = $result->fetch_assoc()) {
      $icon = "";
      if (strpos($row["folderPath"], "/") !== false) {
        // Eğer "/" karakteri folderPath içeriyorsa, tree icon kullan
        $icon = '<i class="fa-solid fa-folder-tree"></i>';
      } else {
        // Eğer "/" karakteri folderPath içermiyorsa, open folder icon kullan
        $icon = '<i class="fa-sharp fa-solid fa-folder"></i>';
      }
      echo '<li>' . $icon . '<a href="' . $row["folderPath"] . '" class="link-light rounded">' . $row["folderPath"] . '</a></li>';
    }

    $stmt->close();
  }
} else {
  echo "User session not found.";
}

$conn->close();
?>
