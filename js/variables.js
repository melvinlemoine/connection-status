let connection = false;
let ping;

// LAST UPDATE FUNCTION ##########

let lastUpdateDate;
let updating;

// UNIT MODE ##########
let unitMode = "mb"; // or "mo"

// NETWORK DATA ##########
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

let previous_effectiveType_color;
let previous_rtt_color;
let previous_downlink_color;
let previous_ping_color;

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

// PING COLORS
let ping_green = 150;
let ping_yellow = 250;
let ping_orange = 500;
let ping_red = 1000;

// PING LIBRARY ##########
let p = new Ping();
