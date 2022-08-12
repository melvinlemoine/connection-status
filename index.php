<!DOCTYPE html>

<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Integration virgin template</title>

  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- <link rel="stylesheet" href="assets/css/reset.css" /> -->
  <!-- <link rel="stylesheet" href="assets/css/styles.css" /> -->
</head>

<body>
  <h1 id="status">waiting...</h1>
  <h1>Type : <span id="type">waiting...</span></h1>
  <h1>Effective type : <span id="effectiveType">waiting...</span></h1>
  <h1>downlinkMax : <span id="downlinkMax">waiting...</span></h1>
  <h1>downlink : <span id="downlink">waiting...</span></h1>
  <h1>RTT : <span id="rtt">waiting...</span></h1>
  <h1>Last change : <span id="lastUpdate"></span></h1>

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
  <h1>IP : <span id="ip"><?php echo getUserIpAddr(); ?></span></h1>

  <div style="height: 20rem">
    <canvas id="myChart"></canvas>
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
  let data = {
    labels: [],
    datasets: [{
        label: "Connection speed in mb/s",
        data: [],
        backgroundColor: [],
        borderColor: [],
        borderWidth: 1,
      },
      // {
      //   label: "RTT",
      //   data: [],
      //   backgroundColor: [],
      //   borderColor: [],
      //   borderWidth: 1,
      // },
    ],
  };

  const config = {
    type: "bar",
    data: data,

    options: {
      locale: "fr",
      responsive: true,
      maintainAspectRatio: false,
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

  const myChart = new Chart(document.getElementById("myChart"), config);

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

    if (myChart.data.datasets[0].data > 10) {
      myChart.data.datasets[0].data.shift();
      console.log("Removed first element of array");
    }

    myChart.data.labels.push(date);
    myChart.data.datasets[0].data.push(downlink);

    if (downlink > 5) {
      myChart.data.datasets[0].backgroundColor.push(green);
      myChart.data.datasets[0].borderColor.push(greenBorder);
      // console.log("green");
    } else if (downlink > 2.5) {
      myChart.data.datasets[0].backgroundColor.push(orange);
      myChart.data.datasets[0].borderColor.push(orangeBorder);
      // console.log("orange");
    } else if (downlink > 1) {
      myChart.data.datasets[0].backgroundColor.push(yellow);
      myChart.data.datasets[0].borderColor.push(yellowBorder);
      // console.log("yellow");
    } else {
      myChart.data.datasets[0].backgroundColor.push(red);
      myChart.data.datasets[0].borderColor.push(redBorder);
      // console.log("red");
    }

    //   myChart.data.datasets[1].data.push(rtt);

    //   if (rtt > 1000) {
    //     myChart.data.datasets[1].backgroundColor.push(red);
    //     myChart.data.datasets[1].borderColor.push(redBorder);
    //   } else if (rtt > 500) {
    //     myChart.data.datasets[1].backgroundColor.push(orange);
    //     myChart.data.datasets[1].borderColor.push(orangeBorder);
    //   } else if (rtt > 250) {
    //     myChart.data.datasets[1].backgroundColor.push(yellow);
    //     myChart.data.datasets[1].borderColor.push(yellowBorder);
    //   } else {
    //     myChart.data.datasets[1].backgroundColor.push(green);
    //     myChart.data.datasets[1].borderColor.push(greenBorder);
    //   }

    myChart.update();
  }
</script>

<script>
  // function getIPFromAmazon() {
  //   fetch("https://checkip.amazonaws.com/")
  //     .then((res) => res.text())
  //     .then((data) => console.log(data));
  // }

  // getIPFromAmazon();

  function getIP() {
    fetch("https://api.ipify.org/")
      .then((r) => r.text())
      .then((r) => {
        const IP = r;
        document.getElementById("ip").innerText = IP;
      })
      .catch((error) => {
        document.getElementById("ip").innerText = "Blocked by Ads Block";
      });
  }

  getIP();

  function updateInformations() {
    console.log("🔄 Informations changed");
    if (navigator.onLine === true) {
      document.getElementById("status").innerText = "online";
      // console.log("✅ Connected");
    } else {
      document.getElementById("status").innerText = "offline";
      // console.log("❌ Disconnected");
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
      update();
    });

    window.addEventListener("offline", function() {
      document.getElementById("status").innerText = "offline";
      update();
    });

    updateLastUpdate();
    updateChart(moment().format("LTS"), downlink, rtt);
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