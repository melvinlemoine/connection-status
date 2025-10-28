// CHECK CONNECTION WHEN PAGE LOAD ##########

if (navigator.onLine === true) {
  goOnline();
} else {
  goOffline();
}

// PING ##########

function launchPing() {
  // console.log("launchPing");
  p.ping("https://google.com")
    .then((data) => {
      // console.log("Successful ping: " + data);
      ping = data;
      document.getElementById("ping").innerText = data + "ms";
      document.getElementById("ping--box").classList.remove(previous_ping_color);

      if (ping > ping_red) {
        document.getElementById("ping--box").classList.add("red");
        previous_ping_color = "red";
      } else if (ping > ping_orange) {
        document.getElementById("ping--box").classList.add("orange");
        previous_ping_color = "orange";
      } else if (ping > ping_yellow) {
        document.getElementById("ping--box").classList.add("yellow");
        previous_ping_color = "yellow";
      } else if (ping < ping_green < ping_yellow) {
        document.getElementById("ping--box").classList.add("green");
        previous_ping_color = "green";
      }
      // setTimeout(function () {
      //   launchPing();
      //   console.log("launchPing from success");
      // }, 3000);

      updateTitle();
      // updateLastUpdate();
      updateChart(moment().format("LTS", "h:mm:ss a"), ping, "ping");
    })
    .catch((ping) => {
      document.getElementById("ping").innerText = "Timeout ⚠️";
      document.getElementById("ping--box").classList.add("red");
      previous_ping_color = "red";
      updateChart(moment().format("LTS", "h:mm:ss a"), "timeout", "ping");
      // setTimeout(function () {
      //   launchPing();
      //   console.log("launchPing from error");
      // }, 3000);
    })
    .then((ping) => {
      setTimeout(function () {
        launchPing();
        console.log("launchPing from last then");
      }, 3000);
    });
}

// CONNECTION STATUS EVENT ##########

window.addEventListener("online", function () {
  goOnline();
});

window.addEventListener("offline", function () {
  goOffline();
});

// CONNECTION STATUS EVENT ##########

function goOnline() {
  document.getElementById("status").innerText = "✅ Online";
  document.getElementById("status").classList.remove("offline");
  document.getElementById("status").classList.add("online");
  connection = true;
  console.log("✅ Connection restored");
  updateLastUpdate();
  getIP();
  launchPing();
  updateTitle();

  // DOWNLINK ENABLE
  document.getElementById("downlink--box").classList.remove("not-supported");
  // RTT ENABLE
  document.getElementById("rtt--box").classList.remove("not-supported");
  // PING ENABLE
  document.getElementById("ping--box").classList.remove("not-supported");
  // EFFECTIVE TYPE ENABLE
  document.getElementById("effectiveType--box").classList.remove("not-supported");
}

function goOffline() {
  document.getElementById("status").innerText = "❌ Offline";
  document.getElementById("status").classList.remove("online");
  document.getElementById("status").classList.add("offline");
  connection = false;
  console.log("❌ Connection lost");
  document.getElementById("ip").innerText = "no IP";
  updateLastUpdate();
  updateTitle();
  clearInterval(pinging);

  // DOWNLINK DISABLE
  document.getElementById("downlink").innerText = 0 + " Mb/s";
  document.getElementById("downlink--box").classList.add("not-supported");
  document.getElementById("downlink--box").classList.remove(previous_downlink_color);
  // RTT DISABLE
  document.getElementById("rtt").innerText = 0 + " Mb/s";
  document.getElementById("rtt--box").classList.add("not-supported");
  document.getElementById("rtt--box").classList.remove(previous_rtt_color);
  // PING DISABLE
  document.getElementById("ping").innerText = 0 + "ms";
  document.getElementById("ping--box").classList.add("not-supported");
  document.getElementById("ping--box").classList.remove(previous_ping_color);
  // EFFECTIVE TYPE DISABLE
  document.getElementById("effectiveType").innerText = "Not supported";
  document.getElementById("effectiveType--box").classList.add("not-supported");
  document.getElementById("effectiveType--box").classList.remove(previous_effectiveType_color);
}

// GET IP ##########

function getIP() {
  document.getElementById("ip").innerText = "Reaching IP...";
  fetch("https://api.ipify.org/")
    .then((r) => r.text())
    .then((r) => {
      const IP = r;
      document.getElementById("ip").innerText = IP;
    })
    .catch((error) => {
      setTimeout(function () {
        getIP();
      }, 2000);
      document.getElementById("ip").innerText = "Can't get IP (ads/tracker blocker or timeout)";
    });
}

if (connection) {
  getIP();
}
