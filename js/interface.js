// DOCUMENT TITLE UPDATE ##########
function updateTitle() {
  console.log("UpdateTitle");
  if (connection) {
    if (!ping) {
      ping = "Pinging...";
    }
    document.title = "✅ " + downlink + " Mb/s" + "｜ ↔️ " + ping + " ms";
  } else {
    document.title = "❌ Offline";
  }
}

// LAST UPDATE INFORMATION ##########

function updateLastUpdate() {
    console.log("🔄 LastUpdate updated");
    if (updating) {
      clearInterval(updating);
    }

    // SET RELATIVE DATE ON LAST UPDATE
    lastUpdateDate = moment().format("LTS");
    document.getElementById("lastUpdate").innerText = moment(lastUpdateDate, "h:mm:ss a").fromNow();

    // UPDATE MOMENT RELATIVE DATE
    updating = setInterval(function () {
      document.getElementById("lastUpdate").innerText = moment(lastUpdateDate, "h:mm:ss a").fromNow();
    }, 5000);
  }
