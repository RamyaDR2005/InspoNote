<?php
// File: notes.php and delete.php combined

date_default_timezone_set("Asia/Kolkata");
$notesFile = 'notes.json';
$uploadDir = 'uploads/';

if (!file_exists($uploadDir)) {
  mkdir($uploadDir);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
  $noteId = $_POST['delete'];
  if (file_exists($notesFile)) {
    $notes = json_decode(file_get_contents($notesFile), true);
    $notes = array_filter($notes, function ($note) use ($noteId) {
      return $note['id'] !== $noteId;
    });
    file_put_contents($notesFile, json_encode(array_values($notes), JSON_PRETTY_PRINT));
  }
  echo json_encode(["status" => "deleted"]);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = $_POST['title'] ?? '';
  $description = $_POST['description'] ?? '';
  $createdAt = date('Y-m-d H:i:s');

  $attachments = [];

  foreach (["image", "audio", "file"] as $field) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
      $ext = pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION);
      $filename = uniqid($field . '_') . '.' . $ext;
      move_uploaded_file($_FILES[$field]['tmp_name'], $uploadDir . $filename);
      $attachments[$field] = $uploadDir . $filename;
    }
  }

  $note = [
    'id' => uniqid('note_'),
    'title' => $title,
    'description' => $description,
    'created_at' => $createdAt,
    'attachments' => $attachments
  ];

  $notes = file_exists($notesFile) ? json_decode(file_get_contents($notesFile), true) : [];
  $notes[] = $note;
  file_put_contents($notesFile, json_encode($notes, JSON_PRETTY_PRINT));

  header('Location: index.html');
  exit;
}
?>
