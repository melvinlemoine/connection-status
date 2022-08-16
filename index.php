<!DOCTYPE html>

<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Connection Status</title>

  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" href="assets/css/amethyst.min.css" />
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>

<body class="body home">
  <header class="header pv-3">
    <div class="container flex-c h-centered">
      <h1 class="header__title relative"><span class="header__title--title bold uppercase">Connection Status</span><span class="header__title--span">Retro Edition</span></h1>
    </div>
  </header>

  <div class="informations">

    <div class="container mt-5 informations__grid">
      <div class="informations__grid_information informations__grid_information--connection offline" id="status">waiting...</div>
      <div class="informations__grid_information informations__grid_information--ip" id="ip--box">🌐 IP :&nbsp;<span id="ip">No IP</span></div>

      <div class="informations__grid_information" id="type--box">Type :&nbsp;<span id="type">waiting...</span></div>
      <div class="informations__grid_information" id="effectiveType--box">📶 Effective type :&nbsp;<span id="effectiveType">waiting...</span></div>


      <div class="informations__grid_information" id="downlink--box">⬇️ downlink :&nbsp;<span id="downlink">waiting...</span></div>
      <div class="informations__grid_information" id="rtt--box">🔂 RTT :&nbsp;<span id="rtt">waiting...</span></div>

      <div class="informations__grid_information" id="downlinkMax--box">downlinkMax :&nbsp;<span id="downlinkMax">waiting...</span></div>
      <div class="informations__grid_information">🛡 saveData :&nbsp;<span id="saveData">waiting...</span></div>

      <div class="informations__lastUpdate">⏱ Last change : <span id="lastUpdate"></span></div>

    </div>

  </div>



  <div class="charts flex-r">


    <div class="charts__chart" style="height: 20rem">
      <canvas id="chart-bandwidth"></canvas>
    </div>
    <div class="charts__chart" style="height: 20rem">
      <canvas id="chart-rtt"></canvas>
    </div>
  </div>

</body>

<script src="assets/js/moment.min.js"></script>
<!-- <script src="assets/js/moment_fr.min.js"></script> -->
<!-- <script>
  moment.locale("fr");
</script> -->
<script src="assets/js/chart.min.js"></script>
<!-- <script src="assets/js/chartjs-plugin-datalabels.min.js"></script> -->

<script>
  let data_bandwidth = {
    labels: [],
    datasets: [{
      label: "Connection speed in mb/s",
      data: [],
      backgroundColor: [],
      borderColor: [],
      borderWidth: 1,
    }, ],
  };

  let data_rtt = {
    labels: [],
    datasets: [{
      label: "RTT",
      data: [],
      backgroundColor: [],
      borderColor: [],
      borderWidth: 1,
    }, ],
  };

  const config_bandwidth = {
    type: "bar",
    data: data_bandwidth,

    options: {
      locale: "fr",
      responsive: true,
      maintainAspectRatio: false,
      fill: true,
      scales: {
        y: {
          suggestedMin: 0,
          suggestedMax: 10
        },
      },
      plugins: {
        legend: {
          display: false,
          position: "top",
        },
        title: {
          display: true,
          text: "Connexion speed in mb/s",
        },
      },
    },
  };

  const config_rtt = {
    type: "bar",
    data: data_rtt,

    options: {
      locale: "fr",
      responsive: true,
      maintainAspectRatio: false,
      fill: true,
      y: {
        suggestedMin: 0,
        suggestedMax: 8000
      },
      plugins: {
        legend: {
          display: false,
          position: "top",
        },
        title: {
          display: true,
          text: "RTT in ms",
        },
      },
    },
  };

  const chart_bandwidth = new Chart(document.getElementById("chart-bandwidth"), config_bandwidth);
  const chart_rtt = new Chart(document.getElementById("chart-rtt"), config_rtt);

  const red = "rgba(255, 99, 132, 0.75)";
  const redBorder = "rgb(255, 99, 132)";

  const orange = "rgba(255, 159, 64, 0.75)";
  const orangeBorder = "rgb(255, 159, 64)";

  const yellow = "rgba(255, 205, 86, 0.75)";
  const yellowBorder = "rgb(255, 205, 86)";

  const green = "rgba(75, 192, 192, 0.75)";
  const greenBorder = "rgb(75, 192, 192)";

  let downlink;
  let previous_downlink;

  let rtt;
  let previous_rtt;

  let downlinkmax;
  let previous_downlinkmax;

  let type;
  let previous_type;

  let effectiveType;
  let previous_effectiveType;

  let saveData;
  let previous_saveData;

  function updateChart(date, value, chart) {
    //   console.log("📊 Update chart");

    switch (chart) {
      case "bandwidth":
        chart_bandwidth.data.labels.push(date);
        chart_bandwidth.data.datasets[0].data.push(value);

        if (value > downlink_green) {
          chart_bandwidth.data.datasets[0].backgroundColor.push(green);
          chart_bandwidth.data.datasets[0].borderColor.push(greenBorder);
        } else if (value > downlink_orange) {
          chart_bandwidth.data.datasets[0].backgroundColor.push(orange);
          chart_bandwidth.data.datasets[0].borderColor.push(orangeBorder);
        } else if (value > downlink_yellow) {
          chart_bandwidth.data.datasets[0].backgroundColor.push(yellow);
          chart_bandwidth.data.datasets[0].borderColor.push(yellowBorder);
        } else if (value > downlink_red) {
          chart_bandwidth.data.datasets[0].backgroundColor.push(red);
          chart_bandwidth.data.datasets[0].borderColor.push(redBorder);
        }

        if (chart_bandwidth.data.datasets[0].data.length > 10) {
          chart_bandwidth.data.labels.shift();
          chart_bandwidth.data.datasets[0].data.shift();
          chart_bandwidth.data.datasets[0].backgroundColor.shift();
          chart_bandwidth.data.datasets[0].borderColor.shift();
          console.log("Removed first element of data bandwidth array. Now : " + chart_rtt.data.datasets[0].data.length);
          console.log("Removed first element of bg bandwidth array. Now : " + chart_rtt.data.datasets[0].backgroundColor.length);
          console.log("Removed first element of border bandwidth array. Now : " + chart_rtt.data.datasets[0].borderColor.length);
        }
        chart_bandwidth.update();
        break;

      case "rtt":
        chart_rtt.data.labels.push(date);
        chart_rtt.data.datasets[0].data.push(rtt);

        if (rtt > rtt_red) {
          chart_rtt.data.datasets[0].backgroundColor.push(red);
          chart_rtt.data.datasets[0].borderColor.push(redBorder);
        } else if (rtt > rtt_orange) {
          chart_rtt.data.datasets[0].backgroundColor.push(orange);
          chart_rtt.data.datasets[0].borderColor.push(orangeBorder);
        } else if (value > rtt_yellow) {
          chart_rtt.data.datasets[0].backgroundColor.push(yellow);
          chart_rtt.data.datasets[0].borderColor.push(yellowBorder);
        } else if (value > rtt_green) {
          chart_rtt.data.datasets[0].backgroundColor.push(green);
          chart_rtt.data.datasets[0].borderColor.push(greenBorder);
        }

        if (chart_rtt.data.datasets[0].data.length > 10) {
          chart_rtt.data.labels.shift();
          chart_rtt.data.datasets[0].data.shift();
          chart_rtt.data.datasets[0].backgroundColor.shift();
          chart_rtt.data.datasets[0].borderColor.shift();
          console.log("Removed first element of data rtt array. Now : " + chart_rtt.data.datasets[0].data.length);
          console.log("Removed first element of bg rtt array. Now : " + chart_rtt.data.datasets[0].backgroundColor.length);
          console.log("Removed first element of border rtt array. Now : " + chart_rtt.data.datasets[0].borderColor.length);
        }
        console.log("RTT DATA (" + chart_rtt.data.datasets[0].data.length + ")");
        for (i = 0; i < chart_rtt.data.datasets[0].data.length; i++) {
          console.log("- " + chart_rtt.data.datasets[0].data[i] + " _ " + chart_rtt.data.labels[i]);
        }

        chart_rtt.update();
        break;
    }



  }
</script>

<script>
  // LAST UPDATE FUNCTION################################################################################

  let lastUpdateDate;
  let updating;

  function updateLastUpdate() {
    console.log("🔄 LastUpdate updated");
    if (updating) {
      clearInterval(updating);
    }

    // SET RELATIVE DATE ON LAST UPDATE
    lastUpdateDate = moment().format("LTS");
    document.getElementById("lastUpdate").innerText = moment(lastUpdateDate, "h:mm:ss a").fromNow();

    // UPDATE MOMENT RELATIVE DATE
    updating = setInterval(function() {
      document.getElementById("lastUpdate").innerText = moment(lastUpdateDate, "h:mm:ss a").fromNow();
    }, 5000);
  }

  // END OF LAST UPDATE FUNCTION ######################################################################



  // CHECK CONNECTION WHEN PAGE LOAD ########################################

  let connection = false;

  if (navigator.onLine === true) {
    goOnline();
  } else {
    goOffline();
  }

  function goOnline() {
    document.getElementById("status").innerText = "✅ Online";
    document.getElementById("status").classList.remove("offline");
    document.getElementById("status").classList.add("online");
    connection = true;
    console.log("✅ Connection restored");
    updateLastUpdate();
    getIP();
  }

  function goOffline() {
    document.getElementById("status").innerText = "❌ Offline";
    document.getElementById("status").classList.remove("online");
    document.getElementById("status").classList.add("offline");
    connection = false;
    console.log("❌ Connection lost");
    document.getElementById("ip").innerText = "no IP";
    updateLastUpdate();
  }

  window.addEventListener("online", function() {
    goOnline();
  });

  window.addEventListener("offline", function() {
    goOffline();
  });

  // END OF CHECK CONNECTION WHEN PAGE LOAD ########################################



  // GET IP ##################################################

  function getIP() {
    document.getElementById("ip").innerText = "Reaching IP...";
    fetch("https://api.ipify.org/")
      .then((r) => r.text())
      .then((r) => {
        const IP = r;
        document.getElementById("ip").innerText = IP;
      })
      .catch((error) => {
        document.getElementById("ip").innerText = "Can't get IP (ads/tracker blocker or timeout)";
      });
  }

  if (connection) {
    getIP();
  }

  // END OF GET IP ##################################################

  // UPDATE INFORMATIONS FUNCTION ##################################################

  let previous_effectiveType_color;
  let previous_rtt_color;
  let previous_downlink_color;

  // RTT COLORS
  let rtt_green = 500;
  let rtt_yellow = 1000;
  let rtt_orange = 2000;
  let rtt_red = 3000;

  // DOWNLINK COLORS
  let downlink_green = 2.5;
  let downlink_yellow = 2;
  let downlink_orange = 1;
  let downlink_red = 0;

  function updateInformations() {

    console.log("🔄 Informations Updated");

    if (connection) {

      // let type = 'Not supported';
      // let downlinkMax = 'not supported';

      // if ('connection' in navigator) {
      //   type = navigator.connection.effectiveType;

      //   if ('downlinkMax' in navigator.connection) {
      //     console.log("downlinkMax is in navigator.connection");
      //     let downlinkMax = navigator.connection.downlinkMax;
      //   } else {
      //     console.log("downlinkMax is not in navigator.connection");
      //   }
      // }

      type = navigator.connection.type;
      if (type) {
        document.getElementById("type").innerText = type;
        document.getElementById('type--box').classList.remove('not-supported');
      } else {
        document.getElementById("type").innerText = "Not supported";
        document.getElementById('type--box').classList.add('not-supported');
      }

      effectiveType = navigator.connection.effectiveType;
      document.getElementById("effectiveType").innerText = effectiveType;


      document.getElementById('effectiveType--box').classList.remove(previous_effectiveType_color);
      switch (effectiveType) {
        case "4g":
          document.getElementById('effectiveType--box').classList.add('green');
          previous_effectiveType_color = "green";
          break;
        case "3g":
          document.getElementById('effectiveType--box').classList.add('yellow');
          previous_effectiveType_color = "yellow";
          break;
        case "2g":
          document.getElementById('effectiveType--box').classList.add('orange');
          previous_effectiveType_color = "orange";
          break;
        case "slow-2g":
          document.getElementById('effectiveType--box').classList.add('red');
          previous_effectiveType_color = "red";
          break;

      }

      downlinkMax = navigator.connection.downlinkMax;
      if (downlinkMax) {
        document.getElementById("downlinkMax").innerText = downlinkMax + "mb/s";
        document.getElementById('downlinkMax--box').classList.remove('not-supported');
      } else {
        document.getElementById("downlinkMax").innerText = "Not supported";
        document.getElementById('downlinkMax--box').classList.add('not-supported');
      }

      downlink = navigator.connection.downlink;

      if (downlink >= 10) {
        document.getElementById("downlink").innerText = "+" + downlink + "mb/s";
      } else {
        document.getElementById("downlink").innerText = downlink + "mb/s";
      }

      document.getElementById('downlink--box').classList.remove(previous_downlink_color);
      console.log("previous_downlink_color : " + previous_downlink_color);

      if (downlink > downlink_green) {
        document.getElementById('downlink--box').classList.add('green');
        previous_downlink_color = "green";
      } else if (downlink > downlink_yellow) {
        document.getElementById('downlink--box').classList.add('yellow');
        previous_downlink_color = "yellow";
      } else if (downlink > downlink_orange) {
        document.getElementById('downlink--box').classList.add('orange');
        previous_downlink_color = "orange";
      } else if (downlink > downlink_red) {
        document.getElementById('downlink--box').classList.add('red');
        previous_downlink_color = "red";
      }

      rtt = navigator.connection.rtt;
      document.getElementById("rtt").innerText = rtt + "ms";

      if (rtt > rtt_red) {
        document.getElementById('rtt--box').classList.add('red');
      } else if (rtt > rtt_orange) {
        document.getElementById('rtt--box').classList.add('orange');
      } else if (rtt > rtt_yellow) {
        document.getElementById('rtt--box').classList.add('yellow');
      } else if (rtt > rtt_green) {
        document.getElementById('rtt--box').classList.add('green');
      }

      saveData = navigator.connection.saveData;
      document.getElementById("saveData").innerText = saveData;

      // UPDATE LAST UPDATE AFTER UPDATE INFORMATIONS AND BEFORE ADD DATA TO CHART
      updateLastUpdate();

      // IF DATA DON'T CHANGED, NO CHART INSERT
      if (previous_downlink != downlink) {
        updateChart(lastUpdateDate, downlink, "bandwidth");
        previous_downlink = downlink;
      }
      if (previous_rtt != rtt) {
        updateChart(lastUpdateDate, rtt, "rtt");
        previous_rtt = rtt;
      }

      document.title = "✅ " + downlink + "mb/s" + " - " + rtt + "ms";

      // END OF CONNECTION STATUS ##################################################
    } else {
      // console.log("Don't updated informations because offline");
      document.title = "❌ Offline";
    }
  }

  // END OF UPDATE INFORMATIONS FUNCTION

  // WHEN NAVIGATOR CONNECTION INFORMATIONS CHANGES, UPDATE INFORMATIONS
  navigator.connection.addEventListener("change", updateInformations);

  // INITIALIZE INFORMATIONS
  updateInformations()
</script>

</html>