<!DOCTYPE html>

<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Connection Status</title>

  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" href="assets/css/amethyst.min.css" />
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>

<?php
// function getUserIpAddr()
// {
//   if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//     //ip from share internet
//     $ip = $_SERVER['HTTP_CLIENT_IP'];
//   } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//     //ip pass from proxy
//     $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//   } else {
//     $ip = $_SERVER['REMOTE_ADDR'];
//   }
//   return $ip;
// };
// 
?>

<body class="body home">
  <header class="header pv-3">
    <div class="container flex-c h-centered">
      <h1 class="header__title relative"><span class="header__title--title bold uppercase">Connection Status</span><span class="header__title--span">Retro Edition</span></h1>
    </div>
  </header>

  <!-- <hr class="separator mv-5"> -->

  <div class="informations">

    <div class="container mt-5">
      <div class="informations__box informations__status">
        <div class="informations__box_information informations__status_connection offline" id="status">waiting...</div>
        <div class="informations__box_information informations__status_ip">IP :&nbsp;<span id="ip">Waiting...</span></div>
      </div>

      <div class="informations__box">
        <div class="informations__box_information">Type :&nbsp;<span id="type">waiting...</span></div>
        <div class="informations__box_information">Effective type :&nbsp;<span id="effectiveType">waiting...</span></div>
      </div>


      <div class="informations__box">
        <div class="informations__box_information">downlinkMax :&nbsp;<span id="downlinkMax">waiting...</span></div>
        <div class="informations__box_information">downlink :&nbsp;<span id="downlink">waiting...</span></div>
      </div>

      <div class="informations__box">
        <div class="informations__box_information">RTT :&nbsp;<span id="rtt">waiting...</span></div>
      </div>

      <div class="informations__box">
        <div class="informations__box_information">saveData :&nbsp;<span id="saveData">waiting...</span></div>
      </div>


      <div class="informations__lastUpdate">Last change : <span id="lastUpdate"></span></div>

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
<script src="assets/js/moment_fr.min.js"></script>
<script>
  moment.locale("fr");
</script>
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

  const red = "rgba(255, 99, 132, 0.2)";
  const redBorder = "rgb(255, 99, 132)";

  const orange = "rgba(255, 159, 64, 0.2)";
  const orangeBorder = "rgb(255, 159, 64)";

  const yellow = "rgba(255, 205, 86, 0.2)";
  const yellowBorder = "rgb(255, 205, 86)";

  const green = "rgba(75, 192, 192, 0.2)";
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

        if (value > 5) {
          chart_bandwidth.data.datasets[0].backgroundColor.push(green);
          chart_bandwidth.data.datasets[0].borderColor.push(greenBorder);
        } else if (value > 2.5) {
          chart_bandwidth.data.datasets[0].backgroundColor.push(orange);
          chart_bandwidth.data.datasets[0].borderColor.push(orangeBorder);
        } else if (value > 1) {
          chart_bandwidth.data.datasets[0].backgroundColor.push(yellow);
          chart_bandwidth.data.datasets[0].borderColor.push(yellowBorder);
        } else {
          chart_bandwidth.data.datasets[0].backgroundColor.push(red);
          chart_bandwidth.data.datasets[0].borderColor.push(redBorder);
        }

        if (chart_bandwidth.data.datasets[0].data.length > 10) {
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

        if (rtt > 1000) {
          chart_rtt.data.datasets[0].backgroundColor.push(red);
          chart_rtt.data.datasets[0].borderColor.push(redBorder);
        } else if (rtt > 500) {
          chart_rtt.data.datasets[0].backgroundColor.push(orange);
          chart_rtt.data.datasets[0].borderColor.push(orangeBorder);
        } else if (value > 250) {
          chart_rtt.data.datasets[0].backgroundColor.push(yellow);
          chart_rtt.data.datasets[0].borderColor.push(yellowBorder);
        } else {
          chart_rtt.data.datasets[0].backgroundColor.push(green);
          chart_rtt.data.datasets[0].borderColor.push(greenBorder);
        }

        if (chart_rtt.data.datasets[0].data.length > 10) {
          chart_rtt.data.datasets[0].data.shift();
          chart_rtt.data.datasets[0].backgroundColor.shift();
          chart_rtt.data.datasets[0].borderColor.shift();
          console.log("Removed first element of data rtt array. Now : " + chart_rtt.data.datasets[0].data.length);
          console.log("Removed first element of bg rtt array. Now : " + chart_rtt.data.datasets[0].backgroundColor.length);
          console.log("Removed first element of border rtt array. Now : " + chart_rtt.data.datasets[0].borderColor.length);
        }
        console.log(chart_rtt.data.datasets[0].data);
        chart_rtt.update();
        break;
    }



  }
</script>

<script>
  // GET IP ##################################################
  function getIP() {
    fetch("https://api.ipify.org/")
      .then((r) => r.text())
      .then((r) => {
        const IP = r;
        document.getElementById("ip").innerText = IP;
      })
      .catch((error) => {
        document.getElementById("ip").innerText = "Blocked by ads/trackers blocker";
      });
  }

  getIP();

  // END OF GET IP ##################################################

  let connection = false;

  // CHECK CONNECTION WHEN PAGE LOAD ########################################
  if (navigator.onLine === true) {
    document.getElementById("status").innerText = "online";
    document.getElementById("status").classList.toggle("online");
    document.getElementById("status").classList.toggle("offline");
    console.log("✅ Connected");
    connection = true;
  } else {
    document.getElementById("status").innerText = "offline";
    document.getElementById("status").classList.toggle("online");
    document.getElementById("status").classList.toggle("offline");
    console.log("❌ Disconnected");
    connection = false;
  }

  // END OF CHECK CONNECTION WHEN PAGE LOAD ########################################

  function updateInformations() {

    console.log("🔄 Informations changed");

    let type = navigator.connection.type;
    if (type) {
      document.getElementById("type").innerText = type;
    } else {
      document.getElementById("type").innerText = "unknown";
    }


    effectiveType = navigator.connection.effectiveType;
    document.getElementById("effectiveType").innerText = effectiveType;

    downlinkMax = navigator.connection.downlinkMax;
    if (downlinkMax) {
      document.getElementById("downlinkMax").innerText = downlinkMax + "mb/s";
    } else {
      document.getElementById("downlinkMax").innerText = "unknown";
    }

    downlink = navigator.connection.downlink;
    document.getElementById("downlink").innerText = downlink + "mb/s";

    rtt = navigator.connection.rtt;
    document.getElementById("rtt").innerText = rtt + "ms";

    saveData = navigator.connection.saveData;
    document.getElementById("saveData").innerText = saveData;

    window.addEventListener("online", function() {
      document.getElementById("status").innerText = "online";
      document.getElementById("status").classList.toggle("online");
      document.getElementById("status").classList.toggle("offline");
      updateLastUpdate();
      connection = true;
    });

    window.addEventListener("offline", function() {
      document.getElementById("status").innerText = "offline";
      document.getElementById("status").classList.toggle("online");
      document.getElementById("status").classList.toggle("offline");
      connection = false;
      updateLastUpdate();
    });

    updateLastUpdate();

    if (previous_downlink != downlink) {
      updateChart(moment().format("LTS"), downlink, "bandwidth");
      previous_downlink = downlink;
    }
    if (previous_rtt != rtt) {
      updateChart(moment().format("LTS"), rtt, "rtt");
      previous_rtt = rtt;
    }



    // UPDATE CONNECTION STATUS ##################################################
    if (connection) {
      document.title = "✅ " + downlink + "mb/s" + " - " + rtt + "ms";
    } else {
      document.title = "❌ " + downlink + "mb/s" + " - " + rtt + "ms";
    }

    // END OF CONNECTION STATUS ##################################################

  }

  let lastUpdateDate;
  let updating;

  function updateLastUpdate() {
    // console.log("update function");
    if (updating) {
      clearInterval(updating);
    }

    lastUpdateDate = moment().format("LTS");
    document.getElementById("lastUpdate").innerText = moment(lastUpdateDate, "hh:mm:ss").fromNow();

    updating = setInterval(function() {
      document.getElementById("lastUpdate").innerText = moment(lastUpdateDate, "hh:mm:ss").fromNow();
      // console.log("update interval");
    }, 5000);
  }

  navigator.connection.addEventListener("change", updateInformations);

  updateInformations();
</script>

</html>