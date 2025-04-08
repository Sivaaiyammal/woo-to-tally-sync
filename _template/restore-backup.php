<?php
date_default_timezone_set('Asia/Kolkata');
$backupDir = dirname(__DIR__) . '/_backups';
$files = glob("$backupDir/*.xml");

// ✅ Auto-delete backups older than 24 hours
foreach ($files as $file) {
    if (filemtime($file) < (time() - 86400)) {
        unlink($file);
    }
}

// Refresh file list after deletion
$files = glob("$backupDir/*.xml");
rsort($files); // Show latest first

// 🔢 Pagination setup
$itemsPerPage = 5;
$totalFiles = count($files);
$totalPages = ceil($totalFiles / $itemsPerPage);
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $itemsPerPage;
$filesOnPage = array_slice($files, $start, $itemsPerPage);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Restore Backup</title>
  <link rel="stylesheet" href="../css/restore_backup.css">
</head>
<body>
<div class="gradient-bg">
    <div class="backup-panel">
      <h2>📂 Available Backups</h2>
      <ul class="backup-list">

        <?php if (empty($filesOnPage)): ?>
          <li>No backup files found.</li>
        <?php else: ?>
          <?php foreach ($filesOnPage as $file): ?>
            <?php 
              $fileName = basename($file); 
              $modifiedTime = date("d-m-Y h:i A", filemtime($file));
            ?>
            <li>
              <b><?= $fileName ?></b> 
              <small style="color: #f7ff00;">(🕒 <?= $modifiedTime ?>)</small>

              <!-- Restore Button -->
              <!-- <button onclick="restoreBackup('<?= $fileName ?>')">♻️ Restore</button> -->

              <!-- Download Button -->
              <!-- <form action="download-backup.php" method="post" style="display:inline; margin-left: 10px;">
                <input type="hidden" name="file" value="<?= $fileName ?>">
                <button type="submit">📥 Download</button>
              </form> -->

              <div class="backup-actions">
                <form action="download-backup.php" method="post">
                  <input type="hidden" name="file" value="<?= $fileName ?>">
                  <button type="submit">📥 Download</button>
                </form>

                 <button onclick="restoreBackup('<?= $fileName ?>')">♻️ Restore</button>
              </div>


            </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>

      <!-- 🔁 Pagination -->
      <div style="margin-top: 20px;">
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?>">⬅️ Prev</a>
        <?php endif; ?>
        
        <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page + 1 ?>" style="margin-left: 10px;">Next ➡️</a>
        <?php endif; ?>
      </div>

      <!-- Restore Popup -->
      <div class="popup" id="popup">
        <button onclick="document.getElementById('popup').style.display='none'">✖</button>
        <span id="popupMessage"></span>
      </div>
    </div>
  
  </div>
    

  <script>
    function restoreBackup(file) {
      fetch('process-restore.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'file=' + encodeURIComponent(file)
      })
      .then(response => response.text())
      .then(data => {
        document.getElementById('popupMessage').innerHTML = '✅ ' + data;
        document.getElementById('popup').style.display = 'block';
      })
      .catch(error => {
        document.getElementById('popupMessage').innerHTML = '❌ Restore failed.';
        document.getElementById('popup').style.display = 'block';
      });
    }
  </script>
</body>
</html>
