<!DOCTYPE html>

<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Integration virgin template</title>

  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <link rel="stylesheet" href="assets/css/amethyst.min.css" />
  <link rel="stylesheet" href="assets/css/styles.css" />
</head>

<?php
function getUserIpAddr()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    //ip from share internet
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    //ip pass from proxy
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }
  return $ip;
};
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
      <div class="informations__status">
        <div class="informations__status_connection offline" id="status">waiting...</div>
        <div>IP : <span id="ip"><?php echo getUserIpAddr(); ?></span></div>
      </div>

      <h1>Type : <span id="type">waiting...</span></h1>
      <h1>Effective type : <span id="effectiveType">waiting...</span></h1>
      <h1>downlinkMax : <span id="downlinkMax">waiting...</span></h1>
      <h1>downlink : <span id="downlink">waiting...</span></h1>
      <h1>RTT : <span id="rtt">waiting...</span></h1>
      <h1>Last change : <span id="lastUpdate"></span></h1>
      
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

  function updateChart(date, downlink, rtt) {
    //   console.log("📊 Update chart");

    if (chart_bandwidth.data.datasets[0].data > 10) {
      chart_bandwidth.data.datasets[0].data.shift();
      chart_rtt.data.datasets[0].data.shift();
      console.log("Removed first element of array");
    }

    chart_bandwidth.data.labels.push(date);
    chart_rtt.data.labels.push(date);

    chart_bandwidth.data.datasets[0].data.push(downlink);
    chart_rtt.data.datasets[0].data.push(rtt);

    if (downlink > 5) {
      chart_bandwidth.data.datasets[0].backgroundColor.push(green);
      chart_bandwidth.data.datasets[0].borderColor.push(greenBorder);
      // console.log("green");
    } else if (downlink > 2.5) {
      chart_bandwidth.data.datasets[0].backgroundColor.push(orange);
      chart_bandwidth.data.datasets[0].borderColor.push(orangeBorder);
      // console.log("orange");
    } else if (downlink > 1) {
      chart_bandwidth.data.datasets[0].backgroundColor.push(yellow);
      chart_bandwidth.data.datasets[0].borderColor.push(yellowBorder);
      // console.log("yellow");
    } else {
      chart_bandwidth.data.datasets[0].backgroundColor.push(red);
      chart_bandwidth.data.datasets[0].borderColor.push(redBorder);
      // console.log("red");
    }

    //   myChart.data.datasets[1].data.push(rtt);

    if (rtt > 1000) {
      chart_rtt.data.datasets[0].backgroundColor.push(red);
      chart_rtt.data.datasets[0].borderColor.push(redBorder);
    } else if (rtt > 500) {
      chart_rtt.data.datasets[0].backgroundColor.push(orange);
      chart_rtt.data.datasets[0].borderColor.push(orangeBorder);
    } else if (rtt > 250) {
      chart_rtt.data.datasets[0].backgroundColor.push(yellow);
      chart_rtt.data.datasets[0].borderColor.push(yellowBorder);
    } else {
      chart_rtt.data.datasets[0].backgroundColor.push(green);
      chart_rtt.data.datasets[0].borderColor.push(greenBorder);
    }

    chart_bandwidth.update();
    chart_rtt.update();
  }
</script>

<script>

  function updateInformations() {
    
    console.log("🔄 Informations changed");
    if (navigator.onLine === true) {
      document.getElementById("status").innerText = "online";
      document.getElementById("status").classList.toggle("online");
      document.getElementById("status").classList.toggle("offline");
      console.log("✅ Connected");
    } else {
      document.getElementById("status").innerText = "offline";
      document.getElementById("status").classList.toggle("online");
      document.getElementById("status").classList.toggle("offline");
      console.log("❌ Disconnected");
    }

    let type = navigator.connection.type;
    document.getElementById("type").innerText = type;

    let effectiveType = navigator.connection.effectiveType;
    document.getElementById("effectiveType").innerText = effectiveType;

    let downlinkMax = navigator.connection.downlinkMax;
    if (downlinkMax) {
      document.getElementById("downlinkMax").innerText = downlinkMax + "mb/s";
    } else {
      document.getElementById("downlinkMax").innerText = "unknown";
    }

    let downlink = navigator.connection.downlink;
    document.getElementById("downlink").innerText = downlink + "mb/s";

    let rtt = navigator.connection.rtt;
    document.getElementById("rtt").innerText = rtt + "ms";

    window.addEventListener("online", function() {
      document.getElementById("status").innerText = "online";
      document.getElementById("status").classList.toggle("online");
      document.getElementById("status").classList.toggle("offline");
      updateLastUpdate();
    });

    window.addEventListener("offline", function() {
      document.getElementById("status").innerText = "offline";
      document.getElementById("status").classList.toggle("online");
      document.getElementById("status").classList.toggle("offline");
      updateLastUpdate();
    });

    updateLastUpdate();
    updateChart(moment().format("LTS"), downlink, rtt);
    document.title = downlink + "mb/s" + " - " + rtt + "ms";
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