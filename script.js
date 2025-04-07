function syncProducts() {
    fetch('sync-products.php')
      .then(res => res.text())
      .then(data => document.getElementById('result').innerHTML = data);
  }

  function loadBackups() {
    fetch('list-backups.php')
      .then(res => res.text())
      .then(data => document.getElementById('result').innerHTML = data);
  }

  function restoreBackup(file) {
    fetch(`restore-backup.php?file=${file}`)
      .then(res => res.text())
      .then(data => document.getElementById('result').innerHTML = data);
  }