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


  document.getElementById("settingsBtn").addEventListener("click", () => {
    document.getElementById("settingsModal").classList.remove("hidden");
    // You can optionally fetch and fill existing config values here using AJAX
  });
  
  function closeModal() {
    document.getElementById("settingsModal").classList.add("hidden");
  }
  
  function saveConfig() {
    const data = {
      consumer_key: document.getElementById("consumerKey").value,
      consumer_secret: document.getElementById("consumerSecret").value,
      site_url: document.getElementById("siteURL").value,
      tally_company: document.getElementById("tallyCompany").value
    };
    
    fetch('_template/save-config.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    })
    .then(res => res.text())
    .then(msg => {
      alert(msg);
      closeModal();
    });
  }
  

  